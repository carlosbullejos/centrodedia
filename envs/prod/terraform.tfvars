# AMI de Amazon Linux 2 (us-east-1)
ami_id           = "ami-0c02fb55956c7d316"

# Nombre de tu cluster
cluster_name     = "centrodedia-cluster"

# Tu /24 público
my_ip_cidr       = "203.0.113.0/24"

# ARN del service-linked role de EKS (este lo crea AWS automáticamente)
cluster_role_arn = "arn:aws:iam::799880597301:role/aws-service-role/eks.amazonaws.com/AWSServiceRoleForAmazonEKS"

# ARN que usará Terraform para tu NodeGroup (tu LabRole)
node_role_arn    = "arn:aws:iam::799880597301:role/LabRole"
