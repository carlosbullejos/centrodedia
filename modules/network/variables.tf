variable "cidr" {
  description = "CIDR de la VPC"
  type        = string
}

variable "public_subnet_cidrs" {
  description = "CIDRs de las subnets públicas"
  type        = list(string)
}

variable "azs" {
  description = "AZs donde crear las subnets; debe coincidir la longitud con public_subnet_cidrs"
  type        = list(string)
}

variable "tags" {
  description = "Tags para los recursos"
  type        = map(string)
}
