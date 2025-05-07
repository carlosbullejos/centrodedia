# envs/prod/terraform.tfvars

# AMI de Amazon Linux 2 (us‑east‑1)
ami_id       = "ami-084568db4383264d4"

# Nombre que le darás a tu cluster EKS
cluster_name = "centrodedia-cluster"

# Tu /24 de IP pública (para SSH a EC2)
my_ip_cidr   = "203.0.113.0/24"
