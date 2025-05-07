output "eks_nodes_sg_id" {
  value       = aws_security_group.eks_nodes.id
  description = "Security group ID for EKS worker nodes"
}
output "efs_sg_id" {
  value       = aws_security_group.efs.id
  description = "Security group ID for EFS mount targets"
}
output "ec2_app_sg_id" {
  value       = aws_security_group.ec2_app.id
  description = "Security group ID for EC2 application instance"
}