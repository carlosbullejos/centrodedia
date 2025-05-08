# AMI de Amazon Linux 2 (us-east-1)
ami_id           = "ami-0c02fb55956c7d316"

# Nombre de tu cluster
cluster_name     = "centrodedia-cluster"

# Tu /24 público
my_ip_cidr       = "203.0.113.0/24"

cluster_role_arn = "arn:aws:iam::799880597301:role/LabRole"
node_role_arn    = "arn:aws:iam::799880597301:role/LabRole"
ssh_key_name = "carloseks"
