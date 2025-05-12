// main.tf
###############################################################################
# 1) Providers & Variables
###############################################################################
variable "region" {
  description = "AWS region"
  type        = string
  default     = "us-east-1"
}

variable "root_volume_size" {
  description = "Size of the root EBS volume (GiB)"
  type        = number
  default     = 20
}

provider "aws" {
  region = var.region
}

provider "kubernetes" {
  host                   = module.eks.cluster_endpoint
  cluster_ca_certificate = base64decode(module.eks.cluster_ca_certificate)
  token                  = data.aws_eks_cluster_auth.cluster.token
}

###############################################################################
# 2) Módulos de Infraestructura
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
# 4) EC2 para control y montaje
###############################################################################
resource "aws_instance" "app_server" {
  ami                    = var.ami_id
  instance_type          = var.instance_type
  subnet_id              = module.network.subnet_ids[0]
  vpc_security_group_ids = [module.security.ec2_app_sg_id]
  key_name               = var.ssh_key_name

  root_block_device {
    volume_size = var.root_volume_size
  }

  user_data = ../../modules/ec2/user_data.sh.tpl", {
    cluster_name    = var.cluster_name
    region          = var.region
    efs_id          = module.efs.efs_id
    efs_mount_point = "/mnt/efs"
  })
  tags = {
    Name = "app-server"
  }

  depends_on = [
    module.eks,
    module.efs,
  ]
}
