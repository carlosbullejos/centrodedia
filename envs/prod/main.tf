###############################################################################
# 1) Providers
###############################################################################


provider "kubernetes" {
  host                   = module.eks.cluster_endpoint
  cluster_ca_certificate = base64decode(module.eks.cluster_ca_certificate)
  token                  = data.aws_eks_cluster_auth.cluster.token
}

provider "helm" {
  kubernetes {
    host                   = module.eks.cluster_endpoint
    cluster_ca_certificate = base64decode(module.eks.cluster_ca_certificate)
    token                  = data.aws_eks_cluster_auth.cluster.token
  }
}

###############################################################################
# 2) Módulos de Infra
###############################################################################
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

module "efs" {
  source            = "../../modules/efs"
  name              = var.efs_name
  subnet_ids        = module.network.subnet_ids
  public_subnet_cidrs  = var.public_subnet_cidrs
  security_group_id = module.security.efs_sg_id
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

  cluster_endpoint_public_access  = true
  cluster_endpoint_private_access = false
  cluster_public_access_cidrs     = ["0.0.0.0/0"]
  ssh_key_name                    = var.ssh_key_name
  node_security_group_ids         = [module.security.ec2_app_sg_id]
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
  depends_on        = [module.efs]
}

###############################################################################
# 3) Data Sources
###############################################################################
data "aws_eks_cluster_auth" "cluster" {
  name = var.cluster_name
}

data "aws_s3_bucket" "backup" {
  bucket = var.backup_bucket_name
}

###############################################################################
# 4) Recursos K8s / NFS Provisioner
###############################################################################
# 4.1 Permitir tráfico NFS (2049) desde nodos EKS al EFS
resource "aws_security_group_rule" "allow_nfs_from_eks" {
  description              = "Permitir montaje NFS (2049) desde EKS"
  type                     = "ingress"
  from_port                = 2049
  to_port                  = 2049
  protocol                 = "tcp"

  security_group_id        = module.security.efs_sg_id
  source_security_group_id = module.security.eks_nodes_sg_id
}

# 4.2 Instalar el NFS subdir external provisioner
resource "helm_release" "nfs_subdir_provisioner" {
  name       = "nfs-subdir-external-provisioner"
  namespace  = "kube-system"
  repository = "https://kubernetes-sigs.github.io/nfs-subdir-external-provisioner/"
  chart      = "nfs-subdir-external-provisioner"
  version    = "4.0.11"

  set {
    name  = "nfs.server"
    value = "${module.efs.efs_id}.efs.us-east-1.amazonaws.com"
  }
  set {
    name  = "nfs.path"
    value = "/"
  }
  set {
    name  = "storageClass.name"
    value = "nfs-client"
  }
  set {
    name  = "storageClass.defaultClass"
    value = "true"
  }
  set {
    name  = "storageClass.archiveOnDelete"
    value = "false"
  }

  timeout         = 300
  atomic          = true
  cleanup_on_fail = true

  # Aseguramos que la release sólo se aplica después de que el clúster EKS esté listo
  depends_on = [module.eks]
}
