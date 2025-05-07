terraform {
  backend "s3" {
    bucket         = "carlosbullejos-copiasdeseguridad"
    key            = "eks-prod/state.tfstate"
    region         = var.aws_region
    dynamodb_table = "terraform-locks"
    encrypt        = true
  }
}