variable "vpc_id" {
  description = "VPC ID where to create security groups"
  type        = string
}
variable "cluster_name" {
  description = "EKS cluster name"
  type        = string
}
variable "my_ip_cidr" {
  description = "Your IP/CIDR for SSH access"
  type        = string
}