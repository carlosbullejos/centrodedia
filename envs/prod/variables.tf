variable "aws_region" {
  type    = string
  default = "us-east-1"
}

variable "cluster_name" {
  description = "Nombre del cluster EKS"
  type        = string
}

variable "my_ip_cidr" {
  description = "Rango CIDR para acceso SSH a EC2"
  type        = string
}

variable "node_group_name" {
  type    = string
  default = "worker-nodes"
}

variable "node_count" {
  type    = number
  default = 2
}

variable "node_instance_type" {
  type    = string
  default = "t3.medium"
}

variable "efs_name" {
  type    = string
  default = "app-efs"
}

variable "ami_id" {
  description = "AMI ID para la instancia EC2"
  type        = string
}

variable "instance_type" {
  type    = string
  default = "t3.micro"
}

variable "backup_bucket_name" {
  description = "Nombre del bucket S3 para backups"
  type        = string
  default     = "carlosbullejos-copiasdeseguridad"
}

variable "cidr" {
  description = "CIDR principal de la VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "public_subnet_cidrs" {
  description = "List of public subnet CIDRs"
  type        = list(string)
  default     = ["10.0.1.0/24", "10.0.2.0/24", "10.0.3.0/24"]
}

variable "cluster_role_arn" {
  description = "ARN del Service‑Linked Role de EKS"
  type        = string
}

variable "node_role_arn" {
  description = "ARN del IAM Role que usarán los worker nodes"
  type        = string
}
