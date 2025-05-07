provider "aws" {}

resource "aws_instance" "app" {
  ami           = var.ami_id
  instance_type = var.instance_type
  subnet_id     = var.subnet_id

  vpc_security_group_ids = [var.security_group_id]

  tags = { Name = var.instance_name }

  root_block_device {
    volume_size = var.root_volume_size
  }

  # Inyectamos el user_data que monta el EFS y arranca SSH
  user_data = templatefile("${path.module}/user_data.sh.tpl", {
    efs_id          = var.efs_id
    efs_mount_point = var.efs_mount_point
  })
}
