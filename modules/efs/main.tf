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
locals {
  storageclass_manifest = templatefile(
    "/kubernetes/nfs-storageclass.yaml",
    {
      efs_id = aws_efs_file_system.this.id
    }
  )
}

# 5) Exponer el manifiesto como output para tu CI/CD
output "efs_storageclass_manifest" {
  description = "YAML del StorageClass + PV con el EFS ID inyectado"
  value       = local.storageclass_manifest
}
