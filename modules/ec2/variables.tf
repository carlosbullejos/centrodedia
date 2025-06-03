variable "ami_id" {
  type = string
}
variable "instance_type" {
  type = string
}
variable "subnet_id" {
  type = string
}
variable "security_group_id" {
  type = string
}
variable "instance_name" {
  type = string
}
variable "root_volume_size" {
  type = number
}
variable "efs_id" {
  type = string
}
variable "efs_mount_point" {
  type        = string
}
variable "ssh_key_name" {
  description = "Par SSH para la instancia EC2"
  type        = string
}
variable "git_token" {
  description = "Token de GitHub para clonar el repositorio privado"
  type        = string
  sensitive   = true
}
variable "aws_access_key_id" {
 
  type        = string
  sensitive   = true
}

variable "aws_secret_access_key" {
 
  type        = string
  sensitive   = true
}
variable "aws_session_token" {
 
  type        = string
  sensitive   = true
}
variable "aws_region" {
 
  type        = string
  sensitive   = true
}
