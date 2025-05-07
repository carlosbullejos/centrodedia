module "network" {
  source              = "../../modules/network"
  cidr                = var.cidr
  public_subnet_cidrs = var.public_subnet_cidrs
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
}

module "efs" {
  source            = "../../modules/efs"
  name              = var.efs_name
  subnet_ids        = module.network.subnet_ids
  security_group_id = module.security.efs_sg_id
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
}

# Data source for existing S3 bucket

data "aws_s3_bucket" "backup" {
  bucket = var.backup_bucket_name
}
```
