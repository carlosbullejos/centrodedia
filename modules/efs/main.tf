# modules/efs/main.tf

provider "aws" {}

# 1) Tu EFS
resource "aws_efs_file_system" "this" {
  creation_token = var.name
  lifecycle_policy {
    transition_to_ia = "AFTER_14_DAYS"
  }
}

# 2) Mapear CIDRs a subnets dinámicamente
locals {
  subnet_map = {
    for cidr in var.public_subnet_cidrs :
    cidr => var.subnet_ids[index(var.public_subnet_cidrs, cidr)]
  }
}

# 3) Crear mount targets en cada subnet
resource "aws_efs_mount_target" "this" {
  for_each        = local.subnet_map
  file_system_id  = aws_efs_file_system.this.id
  subnet_id       = each.value
  security_groups = [var.security_group_id]
}

# 4) Renderizado "on-the-fly" de tu StorageClass + PV de Kubernetes
output "efs_file_system_id" {
  description = "The ID of the EFS file system."
  value       = aws_efs_file_system.this.id
}
