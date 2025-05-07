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
variable "cluster_endpoint_public_access" {
  description = "Habilita endpoint público de la API de EKS"
  type        = bool
  default     = true
}

variable "cluster_endpoint_private_access" {
  description = "Habilita endpoint privado de la API de EKS"
  type        = bool
  default     = false
}

variable "cluster_public_access_cidrs" {
  description = "Lista de CIDRs autorizados para el endpoint público"
  type        = list(string)
  default     = ["0.0.0.0/0"]
}
