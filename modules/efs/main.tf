provider "aws" {}

resource "aws_efs_file_system" "this" {
  creation_token = var.name
  lifecycle_policy {
    transition_to_ia = "AFTER_14_DAYS"
  }
}

resource "aws_efs_mount_target" "this" {
  for_each         = toset(var.subnet_ids)
  file_system_id   = aws_efs_file_system.this.id
  subnet_id        = each.value
  security_groups  = [var.security_group_id]
}