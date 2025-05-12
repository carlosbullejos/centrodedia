# user_data.sh.tpl
#!/usr/bin/env bash
set -euo pipefail

# 1) Install required tools
yum install -y amazon-efs-utils aws-cli jq

# 2) Install kubectl
curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
chmod +x kubectl && mv kubectl /usr/local/bin/

# 3) Wait for EKS cluster to be ACTIVE
until aws eks describe-cluster --name "${cluster_name}" --region "${region}" \
       --query "cluster.status" --output text | grep ACTIVE; do
  echo "Waiting for EKS cluster ${cluster_name} to be ACTIVE..."
  sleep 10
done

# 4) Configure kubeconfig and deploy the EFS CSI driver
aws eks update-kubeconfig --name "${cluster_name}" --region "${region}"
kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"

# 5) Wait for CSI driver pods
kubectl wait --for=condition=Ready pods \
  -l app.kubernetes.io/name=aws-efs-csi-driver -n kube-system \
  --timeout=600s

# 6) Mount EFS on this EC2 instance
mkdir -p ${efs_mount_point}
mount -t efs ${efs_id}:/ ${efs_mount_point}
echo "${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0" >> /etc/fstab
mkdir -p ${efs_mount_point}/ftp ${efs_mount_point}/mysql ${efs_mount_point}/pagina
mkdir -p /mnt/efs/ftp
mkdir -p /mnt/efs/mysql
mkdir -p /mnt/efs/nginx
# 7) Install and start SSH
yum install -y openssh-server
systemctl enable sshd
systemctl start sshd
