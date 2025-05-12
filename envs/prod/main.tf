// main.tf
###############################################################################
# 1) Providers
###############################################################################
provider "aws" {
  region = var.region
}

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
  source               = "../../modules/efs"
  name                 = var.efs_name
  subnet_ids           = module.network.subnet_ids
  public_subnet_cidrs  = var.public_subnet_cidrs
  security_group_id    = module.security.efs_sg_id
}

module "eks" {
  source                     = "../../modules/eks"
  cluster_name               = var.cluster_name
  vpc_id                     = module.network.vpc_id
  subnet_ids                 = module.network.subnet_ids
  node_group_name            = var.node_group_name
  node_count                 = var.node_count
  node_instance_type         = var.node_instance_type
  eks_sg_id                  = module.security.eks_nodes_sg_id
  cluster_role_arn           = var.cluster_role_arn
  node_role_arn              = var.node_role_arn
  cluster_endpoint_public_access  = true
  cluster_endpoint_private_access = false
  cluster_public_access_cidrs     = ["0.0.0.0/0"]
  ssh_key_name                    = var.ssh_key_name
  node_security_group_ids         = [module.security.ec2_app_sg_id]

  # asegúrate de que tu módulo eks expone estos outputs:
  #   oidc_provider_arn y oidc_provider_url (aunque no los necesitemos aquí)
}

module "ec2" {
  source            = "../../modules/ec2"
  ami_id            = var.ami_id
  instance_type     = var.instance_type
  subnet_id         = module.network.subnet_ids[0]
  security_group_id = module.security.ec2_app_sg_id
  instance_name     = "app-server"
  root_volume_size  = 20

  # pasamos al userdata los valores que necesita para el CSI estático y el montaje
  user_data = templatefile("${path.module}/user_data.sh.tpl", {
    cluster_name    = var.cluster_name
    region          = var.region
    efs_id          = module.efs.efs_id
    efs_mount_point = "/mnt/efs"
  })

  depends_on = [module.eks, module.efs]
}

###############################################################################
# 3) Data Sources
###############################################################################
data "aws_eks_cluster_auth" "cluster" {
  name = module.eks.cluster_name
}

data "aws_s3_bucket" "backup" {
  bucket = var.backup_bucket_name
}
