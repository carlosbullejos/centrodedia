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
