# 🏥 Centro de Día Bullejos - Cloud-Native Management Platform

<div align="center">

![AWS](https://img.shields.io/badge/AWS-EKS-FF9900?logo=amazon-aws)
![Kubernetes](https://img.shields.io/badge/Kubernetes-326CE5?logo=kubernetes&logoColor=white)
![Terraform](https://img.shields.io/badge/Terraform-7B42BC?logo=terraform&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql&logoColor=white)
![GitHub Actions](https://img.shields.io/badge/CI%2FCD-GitHub%20Actions-2088FF?logo=github-actions&logoColor=white)

**Cloud-native management platform for day care centers built with AWS EKS, Terraform, and Kubernetes**

[🚀 Live Demo](http://www.centrodiabullejos.es) • [📖 Documentation](./docs) • [🐛 Report Bug](https://github.com/carlosbullejos/centrodedia/issues)

</div>

---

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Architecture](#architecture)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
- [Project Structure](#project-structure)
- [CI/CD Pipeline](#cicd-pipeline)
- [Screenshots](#screenshots)
- [Roadmap](#roadmap)
- [License](#license)

---

## 🎯 About

Modern, scalable, and secure web platform for comprehensive day care center management. 
Built as my final project for Higher Degree in Network Systems Administration (ASIR).

This project demonstrates real-world DevOps practices including:
- ✅ Infrastructure as Code (IaC) with Terraform
- ✅ Container orchestration with Kubernetes (EKS)
- ✅ Automated CI/CD pipelines
- ✅ Cloud-native architecture on AWS
- ✅ GitOps workflows

**🎓 Academic Project** | **💼 Production-Ready** | **🔧 100% Automated**

---

## ⚡ Features

### 👥 User Management
- Multi-role authentication (Admin, Worker, Student, User)
- Comprehensive user profiles with medical records
- Automated enrollment system

### 📚 Academic Management
- Course and subject administration
- Student enrollment and grading system
- Progress tracking and reporting

### 📦 Inventory Control
- Real-time stock management
- Automated alerts for low inventory
- Supplier management

### 📁 Document Management
- Integrated FTP server (vsftpd)
- Secure file upload/download
- Document versioning

### 🔒 Security
- Encrypted passwords (bcrypt)
- SQL injection protection (prepared statements)
- XSS prevention
- Role-based access control (RBAC)

### ☁️ Cloud Infrastructure
- Auto-scaling with Kubernetes HPA
- Automated backups to S3
- High availability setup
- Disaster recovery procedures

---

## 🏗️ Architecture
┌─────────────────────────────────────────────────────┐

│                     AWS Cloud                        │

│  ┌────────────────────────────────────────────────┐ │

│  │              VPC (10.0.0.0/16)                 │ │

│  │  ┌──────────────────────────────────────────┐ │ │

│  │  │         EKS Cluster                       │ │ │

│  │  │  ┌────────┐  ┌────────┐  ┌────────┐     │ │ │

│  │  │  │ Nginx  │  │  PHP   │  │ MySQL  │     │ │ │

│  │  │  │  Pod   │  │  Pod   │  │  Pod   │     │ │ │

│  │  │  └────────┘  └────────┘  └────────┘     │ │ │

│  │  │  ┌────────┐                              │ │ │

│  │  │  │  FTP   │     Persistent Storage       │ │ │

│  │  │  │  Pod   │  ◄──────► EFS                │ │ │

│  │  │  └────────┘                              │ │ │

│  │  └──────────────────────────────────────────┘ │ │

│  │                                                │ │

│  │  ┌──────────┐         ┌──────────┐           │ │

│  │  │   EC2    │ ◄─────► │    S3    │           │ │

│  │  │  Backup  │         │  Backups │           │ │

│  │  └──────────┘         └──────────┘           │ │

│  └────────────────────────────────────────────────┘ │

└─────────────────────────────────────────────────────┘

### Key Components:
- **EKS**: Managed Kubernetes for container orchestration
- **EFS**: Shared persistent storage across pods
- **RDS MySQL**: Production database (or MySQL pod)
- **S3**: Automated backups and static assets
- **EC2**: Backup automation and admin tasks
- **ECR**: Private Docker registry

---

## 🛠️ Tech Stack

### Infrastructure
- **Cloud Provider**: AWS (EKS, EFS, EC2, S3, VPC, ECR)
- **IaC**: Terraform 1.5+
- **Configuration Management**: Ansible
- **Container Orchestration**: Kubernetes 1.27+

### Application
- **Backend**: PHP 8.x
- **Web Server**: Nginx
- **Database**: MySQL 8.x
- **FTP**: vsftpd

### DevOps
- **CI/CD**: GitHub Actions
- **Containerization**: Docker
- **Version Control**: Git
- **Image Registry**: AWS ECR

---

## 🚀 Getting Started

### Prerequisites

# Required tools
- AWS CLI
- Terraform >= 1.5
- kubectl
- Docker
- Git


Installation

1. Clone the repository

  git clone https://github.com/carlosbullejos/centrodedia.git
  cd centrodedia

2. Configure AWS credentials

  aws configure

3. Set up Terraform variables

	cd terraform/envs/prod
	cp terraform.tfvars.example terraform.tfvars
	# Edit terraform.tfvars with your values

4. Deploy infrastructure

	terraform init
	terraform plan
	terraform apply

5. Configure kubectl

	aws eks update-kubeconfig --name centrodedia-cluster --region eu-west-1

6. Deploy Kubernetes manifests

	kubectl apply -f kubernetes/

7. Access the application

  kubectl get svc nginx -o jsonpath='{.status.loadBalancer.ingress[0].hostname}'

📖 Detailed installation guide: docs/installation.md

centrodedia/
├── .github/
│   └── workflows/          # CI/CD pipelines
│       ├── terraform.yml   # Infrastructure deployment
│       ├── build-push.yml  # Docker build & push to ECR
│       └── deploy-k8s.yml  # Kubernetes deployment
├── terraform/
│   ├── modules/            # Reusable Terraform modules
│   │   ├── network/        # VPC, subnets, security groups
│   │   ├── eks/            # EKS cluster configuration
│   │   ├── efs/            # Shared storage
│   │   └── ec2/            # Backup instance
│   └── envs/
│       └── prod/           # Production environment
├── kubernetes/
│   ├── nginx.yaml          # Web server deployment
│   ├── php.yaml            # PHP-FPM deployment
│   ├── mysql.yaml          # Database deployment
│   ├── ftp.yaml            # FTP server deployment
│   └── nfs-storageclass.yaml  # EFS storage class
├── ansible/
│   ├── playbooks/          # Ansible playbooks
│   └── roles/              # Reusable roles
├── pagina/                 # PHP application code
│   ├── usuarios/
│   ├── trabajadores/
│   ├── alumnos/
│   ├── cursos/
│   ├── tareas/
│   └── inventario/
└── docs/                   # Documentation
└── docs/                   # Documentation

🔄 CI/CD Pipeline


Automated deployment pipeline using GitHub Actions:

graph LR
    A[Git Push] --> B[Run Tests]
    B --> C[Build Docker Images]
    C --> D[Push to ECR]
    D --> E[Deploy to EKS]
    E --> F[Health Checks]
    F --> G[Production]

Pipeline Features:

- ✅ Automated Terraform validation

- ✅ Docker image build and push to ECR

- ✅ Kubernetes manifest deployment

- ✅ Automated rollbacks on failure

- ✅ Slack notifications (optional)


---

📸 Screenshots

Dashboard



User Management



Infrastructure (AWS Console)




---

🗺️ Roadmap

-  Implement Prometheus + Grafana monitoring

-  Add Istio service mesh

-  Implement GitOps with ArgoCD

-  Multi-cluster deployment

-  Mobile app (React Native)

-  Email/SMS notifications

-  Advanced reporting with BI tools


---

📄 License


This project is part of my academic work for ASIR degree.


---

👤 Author


José Carlos Bullejos Gómez


- GitHub: @carlosbullejos

- LinkedIn: https://www.linkedin.com/in/jos%C3%A9-carlos-bullejos-g%C3%B3mez-ba193521b/

- Email: carlosbullejos1995@gmail.com


---

🙏 Acknowledgments

- ASIR program professors

- AWS documentation

- Kubernetes community

- Open source contributors


---
<div align="center">

⭐ Star this repo if you find it useful!

Made with ❤️ and ☕ by Carlos Bullejos

</div>

