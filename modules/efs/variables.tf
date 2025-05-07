variable "name" {
  type = string
}
variable "subnet_ids" {
  type = list(string)
}
variable "public_subnet_cidrs" {
  description = "Lista de CIDRs de las subnets públicas (keys estáticas para el map)"
  type        = list(string)
}
variable "security_group_id" {
  type = string
}
