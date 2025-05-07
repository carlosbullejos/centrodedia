output "vpc_id" {
  value = aws_vpc.this.id
}

output "subnet_ids" {
  description = "IDs de las subnets públicas"
  value       = [ for s in aws_subnet.public : s.id ]
}
