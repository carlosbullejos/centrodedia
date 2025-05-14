#!/bin/bash
set -euo pipefail
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42WJRQTXVJ"
export AWS_SECRET_ACCESS_KEY="4x1vz9wDPfX3EPM//pOCZof/NXXfXXUX0hkeGeqW"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEFYaCXVzLXdlc3QtMiJGMEQCIBYSIdhKuESE2vq1sAfnLqZuxW6WFru2oMMHkXs4C7qoAiA4nanHKNVFHSaPrw3FWjxPtQcXBDTK+FSx+pBqsJ61+CrAAgj///////////8BEAIaDDc5OTg4MDU5NzMwMSIM86azylkQGHgB0UKPKpQCugrLK3Lbuwgko0mGKdMC3ZfD5oTI5EzeJ1rD1QeuSNyL1X8UIjwcGeNY5/iY6xanNAIbm0Xd4x0SwUNvEIF4sz9CyTTAjrOBwzA1gDAv2LocPKwCajJCH63HCWJmb/RvW3HypV8qOwfSdXeSWLuAvqrLO7LRGyE0sJQnxwaAVuNu2MuSd1TRTYOKo44vo8k17VPSHjfm2hOmUHoRgXRjLTpHcu9fYwH+EoQPxruF7nztP2NZ+wNECszV08n+GTCrlG/M9DQcvjm8e+vtNMukZ6JBpY6ez5dfKGKS1Yp0BJM9D41tRqRDPquzx2vTAiDlaX4t6KE74EBNy41eEqdTTfvZPhdsdqKjfH7hyc0fJSpzpjPQMLLkkMEGOp4Bp1EewlqAQJ5Nn2tOwuhv+cV214+Wd8eggTmQO8UV3RlKKbEwvfHQbwFGEuYPI1SvLfd+y9PA3lhrDMNK8mVsOq9Mx+bsgQzwFZYAWrheui53uiccDIENWHZETZydVgg4v5s4bkGgjetVDEtKDR/IXR1Q8b1Hfpqw+72KTZX7egYtxW+Jx3PQ5onttonLaqgKf062AKpoCAGbfhmSSgI="

sudo yum install -y amazon-efs-utils aws-cli jq git
sudo curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
sudo chmod +x kubectl && sudo mv kubectl /usr/local/bin/



until aws eks describe-cluster --name "${cluster_name}" --region "${region}" \
       --query "cluster.status" --output text | grep ACTIVE; do
  echo "Esperando a que el EKS (${cluster_name}) esté ACTIVE..."
  sleep 10
done

aws eks update-kubeconfig --name "centrodedia-cluster" --region "us-east-1"
kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"

sudo mkdir -p ${efs_mount_point}
sudo mount -t efs ${efs_id}:/ ${efs_mount_point}
sudo bash -c "echo \"${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0\" >> /etc/fstab"
sudo mkdir -p ${efs_mount_point}/ftp ${efs_mount_point}/mysql ${efs_mount_point}/pagina
sudo mkdir -p /mnt/efs/ftp /mnt/efs/mysql /mnt/efs/nginx
sudo chmod -R 777 /mnt/efs
yum install -y openssh-server
systemctl enable sshd
systemctl start sshd
