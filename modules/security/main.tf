provider "aws" {}

# 1) SG para EKS worker nodes
resource "aws_security_group" "eks_nodes" {
  name        = "${var.cluster_name}-nodes-sg"
  description = "SG para worker nodes de EKS (abierto)"
  vpc_id      = var.vpc_id

  # Permitir todo ingress
  ingress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Permitir todo egress
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

# 2) SG para mount targets de EFS
resource "aws_security_group" "efs" {
  name        = "${var.cluster_name}-efs-sg"
  description = "SG para mount targets de EFS (abierto)"
  vpc_id      = var.vpc_id

  # Permitir todo ingress
  ingress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Permitir todo egress
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

# 3) SG para tu instancia EC2 (app-server)
resource "aws_security_group" "ec2_app" {
  name        = "${var.cluster_name}-app-sg"
  description = "SG para instancia EC2 app-server (abierto)"
  vpc_id      = var.vpc_id

  # Permitir todo ingress
  ingress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Permitir todo egress
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}
