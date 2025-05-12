output "cluster_endpoint" {
  value = module.eks.cluster_endpoint
}
output "cluster_ca_certificate" {
  value = module.eks.cluster_ca_certificate
}
output "efs_id" {
  value = module.efs.efs_id
}
output "ec2_instance_ip" {
  value = aws_instance.app_server.public_ip
}

output "backup_bucket_arn" {
  value = data.aws_s3_bucket.backup.arn
}
