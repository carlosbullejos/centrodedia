#!/usr/bin/env bash
set -euo pipefail

# 1) Instala herramientas necesarias
yum install -y amazon-efs-utils aws-cli jq

# 2) Instala kubectl
curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
chmod +x kubectl && mv kubectl /usr/local/bin/

# 3) Espera a que el clúster EKS esté ACTIVE
until aws eks describe-cluster --name centrodedia-cluster--region us-east-1 \
       --query "cluster.status" --output text | grep ACTIVE; do
  echo "Esperando a que el EKS centrodedia-cluster esté ACTIVE..."
  sleep 10
done

# 4) Configura kubeconfig y despliega el CSI driver de EFS
aws eks update-kubeconfig --name centrodedia-cluster --region us-east-1
kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"

# 5) Espera a que los pods CSI estén listos
kubectl wait --for=condition=Ready pods \
  -l app.kubernetes.io/name=aws-efs-csi-driver -n kube-system \
  --timeout=600s

# 6) Monta el EFS en la EC2 y prepara directorios
mkdir -p ${efs_mount_point}
mount -t efs ${efs_id}:/ ${efs_mount_point}
echo "${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0" >> /etc/fstab
mkdir -p ${efs_mount_point}/ftp ${efs_mount_point}/mysql ${efs_mount_point}/pagina
mkdir -p /mnt/efs/ftp
mkdir -p /mnt/efs/mysql
mkdir -p /mnt/efs/nginx
# 7) Instala y arranca SSH
yum install -y openssh-server
systemctl enable sshd
systemctl start sshd
