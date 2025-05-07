variable "cluster_name" {
  description = "Nombre del cluster EKS"
  type        = string
}
variable "vpc_id" {
  description = "VPC ID donde desplegar EKS"
  type        = string
}
variable "subnet_ids" {
  description = "Lista de subnets para EKS"
  type        = list(string)
}
variable "node_group_name" {
  description = "Nombre del grupo de nodos"
  type        = string
}
variable "node_count" {
  description = "Número de réplicas de nodos"
  type        = number
}
variable "node_instance_type" {
  description = "Tipo de instancia para los nodos"
  type        = string
}
variable "eks_sg_id" {
  description = "Security Group para EKS control plane"
  type        = string
}
variable "cluster_role_arn" {
  description = "ARN del service-linked role usado por el plano de control de EKS"
  type        = string
}

variable "node_role_arn" {
  description = "ARN del IAM Role que usarán los worker nodes"
  type        = string
}
