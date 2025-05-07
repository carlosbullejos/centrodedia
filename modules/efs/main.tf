provider "aws" {}

resource "aws_efs_file_system" "this" {
  creation_token = var.name
  lifecycle_policy {
    transition_to_ia = "AFTER_14_DAYS"
  }
}

# Mapeamos cada CIDR (estático) al ID de subnet correspondiente (dinámico)
locals {
  subnet_map = {
    for cidr in var.public_subnet_cidrs :
    cidr => var.subnet_ids[index(var.public_subnet_cidrs, cidr)]
  }
}

resource "aws_efs_mount_target" "this" {
  # keys (cidr) son conocidas al plan; values (subnet IDs) se llenan en apply
  for_each        = local.subnet_map

  file_system_id  = aws_efs_file_system.this.id
  subnet_id       = each.value
  security_groups = [var.security_group_id]
}
