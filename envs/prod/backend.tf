terraform {
  backend "s3" {
    bucket       = "centrodia-backups"
    key          = "eks-prod/state.tfstate"
    region       = "us-east-1"
    use_lockfile = true
    encrypt      = true
  }
}
