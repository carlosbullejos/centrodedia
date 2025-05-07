variable "aws_region" {
  type    = string
  default = "us-east-1"
}

variable "cluster_name" {
  type = string
}
variable "vpc_id" {
  type = string
}
variable "subnet_ids" {
  type = list(string)
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
  type = string
}
variable "instance_type" {
  type    = string
  default = "t3.micro"
}
variable "backup_bucket_name" {
  type    = string
  default = "carlosbullejos-copiasdeseguridad"
}
variable "my_ip_cidr" {
  type = string
}