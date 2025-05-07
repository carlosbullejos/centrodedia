terraform {
  backend "s3" {
    bucket         = "carlosbullejos-copiasdeseguridad"
    key            = "eks-prod/state.tfstate"
    region         = "us-east-1"          
    dynamodb_table = "terraform-locks"
    encrypt        = true
  }
}
