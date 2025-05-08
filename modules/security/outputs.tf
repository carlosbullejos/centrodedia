output "eks_nodes_sg_id" {
  description = "Security Group ID for EKS worker nodes"
  value       = aws_security_group.eks_nodes.id
}

output "efs_sg_id" {
  description = "Security Group ID for EFS mount targets"
  value       = aws_security_group.efs.id
}

output "ec2_app_sg_id" {
  description = "Security Group ID for EC2 application server"
  value       = aws_security_group.ec2_app.id
}
