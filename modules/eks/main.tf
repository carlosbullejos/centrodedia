provider "aws" {}
provider "kubernetes" {
  host                   = aws_eks_cluster.this.endpoint
  cluster_ca_certificate = base64decode(aws_eks_cluster.this.certificate_authority[0].data)
  token                  = data.aws_eks_cluster_auth.this.token
}

resource "aws_eks_cluster" "this" {
  name     = var.cluster_name
  role_arn = var.cluster_role_arn

  vpc_config {
    subnet_ids             = var.subnet_ids
    security_group_ids     = [var.eks_sg_id]

    endpoint_public_access  = var.cluster_endpoint_public_access
    endpoint_private_access = var.cluster_endpoint_private_access
    public_access_cidrs     = var.cluster_public_access_cidrs
  }

  # Sin managed add‑ons: no declaramos nada más aquí
}

resource "aws_eks_node_group" "this" {
  cluster_name    = aws_eks_cluster.this.name
  node_group_name = var.node_group_name
  node_role_arn   = var.node_role_arn
  subnet_ids      = var.subnet_ids

  scaling_config {
    desired_size = var.node_count
    max_size     = var.node_count + 1
    min_size     = var.node_count
  }

  # Asegúrate de que tus nodos reciben IP pública:
  # Esto solo está disponible si usas un launch template,
  # de lo contrario activa “Assign public IP” en la consola.
  remote_access {
    ec2_ssh_key             = var.ssh_key_name
    source_security_group_ids = [module.security.ec2_app_sg_id]
  }

  # Versión nueva del provedor usa lista:
  instance_types = [var.node_instance_type]
}

