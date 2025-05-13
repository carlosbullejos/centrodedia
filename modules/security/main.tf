provider "aws" {}

# 1) SG para EKS worker nodes
resource "aws_security_group" "eks_nodes" {
  name        = "${var.cluster_name}-nodes-sg"
  description = "SG para worker nodes de EKS"
  vpc_id      = var.vpc_id

  # Permitir todo entre nodos
  ingress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    self        = true
  }

  # Permitir NFS desde EFS
  ingress {
    description              = "NFS desde EFS"
    from_port                = 2049
    to_port                  = 2049
    protocol                 = "tcp"
    security_groups          = [aws_security_group.efs.id]
  }

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
  description = "SG para mount targets de EFS"
  vpc_id      = var.vpc_id

  # Permitir NFS desde nodos EKS
  ingress {
    description              = "NFS desde EKS"
    from_port                = 2049
    to_port                  = 2049
    protocol                 = "tcp"
    security_groups          = [aws_security_group.eks_nodes.id]
  }

  # (Opcional) Permitir NFS desde tu instancia EC2
  ingress {
    description              = "NFS desde EC2 app-server"
    from_port                = 2049
    to_port                  = 2049
    protocol                 = "tcp"
    security_groups          = [aws_security_group.ec2_app.id]
  }

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
  description = "SG para instancia EC2 app-server"
  vpc_id      = var.vpc_id

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Permitir NFS desde EFS (si vas a montar EFS manualmente aquí)
  ingress {
    description = "NFS desde EFS"
    from_port   = 2049
    to_port     = 2049
    protocol    = "tcp"
    security_groups = [aws_security_group.efs.id]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}
