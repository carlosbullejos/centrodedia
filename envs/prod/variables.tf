variable "aws_region" {
  type    = string
  default = "us-east-1"
}

variable "cluster_name" {
  description = "Nombre del cluster EKS"
  type        = string
  default     = "centrodedia-cluster"
}

variable "my_ip_cidr" {
  description = "Rango CIDR para acceso SSH a EC2"
  type        = string
  default     = "203.0.113.0/24"
}

variable "node_group_name" {
  description = "Nombre del grupo de nodos"
  type        = string
  default     = "worker-nodes"
}

variable "node_count" {
  description = "Cantidad de nodos"
  type        = number
  default     = 2
}

variable "node_instance_type" {
  description = "Tipo de instancia para nodos"
  type        = string
  default     = "t3.medium"
}

variable "efs_name" {
  description = "Nombre del sistema de ficheros EFS"
  type        = string
  default     = "app-efs"
}

variable "ami_id" {
  description = "AMI ID para la instancia EC2"
  type        = string
  # Pon aquí tu AMI real o sobreescríbelo en terraform.tfvars
  default     = "ami-0c02fb55956c7d316"
}

variable "instance_type" {
  description = "Tipo de instancia EC2"
  type        = string
  default     = "t3.micro"
}

variable "backup_bucket_name" {
  description = "Bucket S3 para backups"
  type        = string
  default     = "carlosbullejos-copiasdeseguridad"
}

variable "cidr" {
  description = "CIDR principal de la VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "public_subnet_cidrs" {
  description = "CIDRs de subnets públicas"
  type        = list(string)
  default     = ["10.0.1.0/24", "10.0.2.0/24", "10.0.3.0/24"]
}
variable "public_subnet_azs" {
  type    = list(string)
  default = ["us-east-1a","us-east-1b","us-east-1c"]
}

# Tanto el cluster como el node‑group usarán este mismo LabRole
variable "cluster_role_arn" {
  description = "ARN del role que usará EKS control‑plane"
  type        = string
  default     = "arn:aws:iam::799880597301:role/LabRole"
}

variable "node_role_arn" {
  description = "ARN del role que usarán los worker nodes"
  type        = string
  default     = "arn:aws:iam::799880597301:role/LabRole"
}

variable "cluster_endpoint_public_access" {
  type    = bool
  default = true
}
variable "cluster_endpoint_private_access" {
  type    = bool
  default = false
}
variable "cluster_public_access_cidrs" {
  type    = list(string)
  default = ["0.0.0.0/0"]
}
variable "ssh_key_name" {
  type        = string
  description = "Par de SSH para remote_access (debes crearlo en EC2) o déjalo vacío"
  default     = ""
}
#
# Cuenta AWS (para extraer el nombre del IAM Role)
#
variable "aws_account" {
  description = "ID de tu cuenta AWS (ej. 123456789012)"
  type        = string
  default     = "799880597301"
}
variable "git_token" {
  description = "Token de GitHub para clonar el repositorio privado"
  type        = string
  sensitive   = true
}

