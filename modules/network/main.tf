resource "aws_vpc" "this" {
  cidr_block           = var.cidr
  enable_dns_hostnames = true
  tags                 = var.tags

  lifecycle {
    # Nunca destruir la VPC en terraform destroy
    ignore_destroy = true
  }
}

resource "aws_subnet" "public" {
  for_each = {
    for idx, cidr in var.public_subnet_cidrs :
    idx => {
      cidr = cidr
      az   = var.azs[idx]
    }
  }

  vpc_id                  = aws_vpc.this.id
  cidr_block              = each.value.cidr
  availability_zone       = each.value.az
  map_public_ip_on_launch = true

  tags = merge(
    var.tags,
    { Name = "${var.tags["Environment"]}-public-${each.key}" }
  )

  lifecycle {
    # Nunca destruir estas subnets en terraform destroy
    ignore_destroy = true
  }
}

# Internet Gateway
resource "aws_internet_gateway" "this" {
  vpc_id = aws_vpc.this.id
  tags   = merge(var.tags, { Name = "${var.tags["Environment"]}-igw" })

  # Si también quieres ignorar el IGW en destroy, descomenta:
  # lifecycle {
  #   ignore_destroy = true
  # }
}

# Route Table pública
resource "aws_route_table" "public" {
  vpc_id = aws_vpc.this.id
  tags   = merge(var.tags, { Name = "${var.tags["Environment"]}-public-rt" })
}

resource "aws_route" "public_default_route" {
  route_table_id         = aws_route_table.public.id
  destination_cidr_block = "0.0.0.0/0"
  gateway_id             = aws_internet_gateway.this.id
}

# Asociar cada subnet pública a la route table pública
resource "aws_route_table_association" "public_subnet_assoc" {
  for_each       = aws_subnet.public
  subnet_id      = each.value.id
  route_table_id = aws_route_table.public.id
}
