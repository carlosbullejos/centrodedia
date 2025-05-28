provider "aws" {}

resource "aws_instance" "app" {
  ami                    = var.ami_id
  instance_type          = var.instance_type
  subnet_id              = var.subnet_id
  associate_public_ip_address = true     
  key_name = var.ssh_key_name
  vpc_security_group_ids = [var.security_group_id]
  tags                   = { Name = var.instance_name }

  root_block_device {
    volume_size = var.root_volume_size
  }

  user_data = "user_data.sh.tpl", {
    efs_id          = var.efs_id
    efs_mount_point = var.efs_mount_point
    git_token = var.git_token
  })
 
}

