provider "aws" {}

# 1) SG para EKS worker nodes
resource "aws_security_group" "eks_nodes" {
  name        = "${var.cluster_name}-nodes-sg"
  description = "SG para worker nodes de EKS"
  vpc_id      = var.vpc_id
}

# 2) SG para mount targets de EFS
resource "aws_security_group" "efs" {
  name        = "${var.cluster_name}-efs-sg"
  description = "SG para mount targets de EFS"
  vpc_id      = var.vpc_id
}

# 3) SG para tu instancia EC2 (app-server)
resource "aws_security_group" "ec2_app" {
  name        = "${var.cluster_name}-app-sg"
  description = "SG para instancia EC2 app-server"
  vpc_id      = var.vpc_id

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = [var.my_ip_cidr]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

# Reglas separadas para NFS (puerto 2049) sin crear ciclos
resource "aws_security_group_rule" "efs_allow_nfs_from_ec2" {
  description              = "Permite NFS desde EC2 hacia EFS"
  type                     = "ingress"
  from_port                = 2049
  to_port                  = 2049
  protocol                 = "tcp"
  security_group_id        = aws_security_group.efs.id
  source_security_group_id = aws_security_group.ec2_app.id
}

resource "aws_security_group_rule" "ec2_allow_all_egress" {
  description              = "Permite todo egress desde EC2"
  type                     = "egress"
  from_port                = 0
  to_port                  = 0
  protocol                 = "-1"
  security_group_id        = aws_security_group.ec2_app.id
  cidr_blocks              = ["0.0.0.0/0"]
}

resource "aws_security_group_rule" "efs_allow_all_egress" {
  description              = "Permite todo egress desde EFS"
  type                     = "egress"
  from_port                = 0
  to_port                  = 0
  protocol                 = "-1"
  security_group_id        = aws_security_group.efs.id
  cidr_blocks              = ["0.0.0.0/0"]
}
