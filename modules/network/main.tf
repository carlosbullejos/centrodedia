resource "aws_vpc" "this" {
  cidr_block           = var.cidr
  enable_dns_hostnames = true
  tags                 = var.tags
}

resource "aws_subnet" "public" {
  for_each            = toset(var.public_subnet_cidrs)
  vpc_id              = aws_vpc.this.id
  cidr_block          = each.value
  availability_zone   = data.aws_availability_zones.available.names[index(var.public_subnet_cidrs, each.value)]
  map_public_ip_on_launch = true
  tags                = merge(var.tags, { Name = "public-${each.value}" })
}

data "aws_availability_zones" "available" {}
