module "network" {
  source              = "../../modules/network"
  cidr                = var.cidr
  public_subnet_cidrs = var.public_subnet_cidrs
  azs                 = var.public_subnet_azs
  tags                = { Environment = "prod" }
}


module "security" {
  source       = "../../modules/security"
  vpc_id       = module.network.vpc_id
  cluster_name = var.cluster_name
  my_ip_cidr   = var.my_ip_cidr
}

module "eks" {
  source             = "../../modules/eks"
  cluster_name       = var.cluster_name
  vpc_id             = module.network.vpc_id
  subnet_ids         = module.network.subnet_ids
  node_group_name    = var.node_group_name
  node_count         = var.node_count
  node_instance_type = var.node_instance_type
  eks_sg_id          = module.security.eks_nodes_sg_id

  cluster_role_arn   = var.cluster_role_arn
  node_role_arn      = var.node_role_arn

  # Estos flags replican “Public access” y “Modo automático desactivado”:
  cluster_endpoint_public_access  = true
  cluster_endpoint_private_access = false
  cluster_public_access_cidrs     = ["0.0.0.0/0"]
  ssh_key_name             = var.ssh_key_name
  node_security_group_ids  = [ module.security.ec2_app_sg_id ]
}
#################################################
# 1) Obtén el token de autenticación para el EKS #
#################################################
data "aws_eks_cluster_auth" "this" {
  name = module.eks.cluster_name
}

########################################################
# 2) Configura los providers Kubernetes y Helm para EKS #
########################################################
provider "kubernetes" {
  host                   = module.eks.cluster_endpoint
  cluster_ca_certificate = base64decode(module.eks.cluster_ca_certificate)
  token                  = data.aws_eks_cluster_auth.this.token
}

provider "helm" {
  kubernetes {
    host                   = module.eks.cluster_endpoint
    cluster_ca_certificate = base64decode(module.eks.cluster_ca_certificate)
    token                  = data.aws_eks_cluster_auth.this.token
  }
}

#############################################
# 3) Asocia la política necesaria al NodeRole
#############################################
# Primero extraemos el nombre del role a partir de su ARN
data "aws_iam_role" "node" {
  arn = module.eks.node_role_arn
}

resource "aws_iam_role_policy_attachment" "efs_csi" {
  role       = data.aws_iam_role.node.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonEKS_EFS_CSI_Driver_Policy"
}

####################################################
# 4) Despliega el AWS EFS CSI Driver como un Helm #
####################################################
resource "helm_release" "efs_csi_driver" {
  name       = "efs-csi-driver"
  repository = "https://kubernetes-sigs.github.io/aws-efs-csi-driver"
  chart      = "aws-efs-csi-driver"
  namespace  = "kube-system"
  version    = "2.8.8"   # o la versión que prefieras

  # Forzamos el uso del repositorio público y la etiqueta compatible:
  set {
    name  = "image.repository"
    value = "public.ecr.aws/eks/aws-efs-csi-driver"
  }
  set {
    name  = "image.tag"
    value = "v1.7.0-eks-1-32-6"
  }
  set {
    name  = "controller.image.repository"
    value = "public.ecr.aws/eks/aws-efs-csi-driver"
  }
  set {
    name  = "controller.image.tag"
    value = "v1.7.0-eks-1-32-6"
  }
  set {
    name  = "node.image.repository"
    value = "public.ecr.aws/eks/aws-efs-csi-driver"
  }
  set {
    name  = "node.image.tag"
    value = "v1.7.0-eks-1-32-6"
  }

  timeout         = 300
  atomic          = true
  cleanup_on_fail = true
}

module "efs" {
  source               = "../../modules/efs"
  name                 = var.efs_name
  subnet_ids           = module.network.subnet_ids
  public_subnet_cidrs  = var.public_subnet_cidrs
  security_group_id    = module.security.efs_sg_id
}

module "ec2" {
  source            = "../../modules/ec2"
  ami_id            = var.ami_id
  instance_type     = var.instance_type
  subnet_id         = module.network.subnet_ids[0]
  security_group_id = module.security.ec2_app_sg_id
  instance_name     = "app-server"
  root_volume_size  = 20
  efs_id            = module.efs.efs_id
  efs_mount_point   = "/mnt/efs"
  ssh_key_name      = var.ssh_key_name 
  depends_on = [
     module.efs
     ]
}

data "aws_s3_bucket" "backup" {
  bucket = var.backup_bucket_name
}
