resource "aws_security_group" "eks_nodes" {
  name        = "${var.cluster_name}-nodes-sg"
  description = "SG para worker nodes de EKS"
  vpc_id      = var.vpc_id
}

resource "aws_security_group" "efs" {
  name        = "${var.cluster_name}-efs-sg"
  description = "SG para mount targets de EFS"
  vpc_id      = var.vpc_id
}

resource "aws_security_group" "ec2_app" {
  name        = "${var.cluster_name}-app-sg"
  description = "SG para instancia EC2 app-server"
  vpc_id      = var.vpc_id
}
# EKS nodes: tráfico interno entre nodos
resource "aws_security_group_rule" "eks_nodes_internal" {
  type              = "ingress"
  from_port         = 0
  to_port           = 65535
  protocol          = "tcp"
  security_group_id = aws_security_group.eks_nodes.id
  self              = true
}

# EKS nodes: permitir NFS desde EFS
resource "aws_security_group_rule" "eks_nodes_nfs_from_efs" {
  type                     = "ingress"
  from_port                = 2049
  to_port                  = 2049
  protocol                 = "tcp"
  security_group_id        = aws_security_group.eks_nodes.id
  source_security_group_id = aws_security_group.efs.id
}

# EFS: permitir NFS desde EKS nodes
resource "aws_security_group_rule" "efs_nfs_from_eks" {
  type                     = "ingress"
  from_port                = 2049
  to_port                  = 2049
  protocol                 = "tcp"
  security_group_id        = aws_security_group.efs.id
  source_security_group_id = aws_security_group.eks_nodes.id
}

# EFS: permitir NFS desde EC2 app-server
resource "aws_security_group_rule" "efs_nfs_from_ec2" {
  type                     = "ingress"
  from_port                = 2049
  to_port                  = 2049
  protocol                 = "tcp"
  security_group_id        = aws_security_group.efs.id
  source_security_group_id = aws_security_group.ec2_app.id
}

# EC2: SSH desde cualquier IP
resource "aws_security_group_rule" "ec2_ssh" {
  type              = "ingress"
  from_port         = 22
  to_port           = 22
  protocol          = "tcp"
  security_group_id = aws_security_group.ec2_app.id
  cidr_blocks       = ["0.0.0.0/0"]
}

# EC2: HTTP/HTTPS desde cualquier IP
resource "aws_security_group_rule" "ec2_http" {
  type              = "ingress"
  from_port         = 80
  to_port           = 80
  protocol          = "tcp"
  security_group_id = aws_security_group.ec2_app.id
  cidr_blocks       = ["0.0.0.0/0"]
}
resource "aws_security_group_rule" "ec2_https" {
  type              = "ingress"
  from_port         = 443
  to_port           = 443
  protocol          = "tcp"
  security_group_id = aws_security_group.ec2_app.id
  cidr_blocks       = ["0.0.0.0/0"]
}

# EC2: permitir NFS desde EFS
resource "aws_security_group_rule" "ec2_nfs_from_efs" {
  type                     = "ingress"
  from_port                = 2049
  to_port                  = 2049
  protocol                 = "tcp"
  security_group_id        = aws_security_group.ec2_app.id
  source_security_group_id = aws_security_group.efs.id
}

# Egress para todos los SGs (puedes modularizarlo si quieres)
resource "aws_security_group_rule" "eks_nodes_egress" {
  type              = "egress"
  from_port         = 0
  to_port           = 0
  protocol          = "-1"
  security_group_id = aws_security_group.eks_nodes.id
  cidr_blocks       = ["0.0.0.0/0"]
}
resource "aws_security_group_rule" "efs_egress" {
  type              = "egress"
  from_port         = 0
  to_port           = 0
  protocol          = "-1"
  security_group_id = aws_security_group.efs.id
  cidr_blocks       = ["0.0.0.0/0"]
}
resource "aws_security_group_rule" "ec2_app_egress" {
  type              = "egress"
  from_port         = 0
  to_port           = 0
  protocol          = "-1"
  security_group_id = aws_security_group.ec2_app.id
  cidr_blocks       = ["0.0.0.0/0"]
}
