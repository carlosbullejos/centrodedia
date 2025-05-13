#!/usr/bin/env bash
set -euo pipefail

# 1) Instala herramientas necesarias
yum install -y amazon-efs-utils aws-cli jq

# 2) Instala kubectl
curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
chmod +x kubectl && mv kubectl /usr/local/bin/

# 2.5) Configura credenciales AWS en el shell
cat <<EOF >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42QPI76WWS"
export AWS_SECRET_ACCESS_KEY="gH8Mr3PPFxgUD49DjpDgkGkiSITHMBR1VdUj5xWh"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjED4aCXVzLXdlc3QtMiJHMEUCIHZpIm7M/fREJTwtMNX6K+iAk09db4p2ne81lQ9iuYN/AiEAjnTAuJq1c5IwghoiWZ3VYEs/9ciMyYfBRTPozkNWFBQqwAII5///////////ARACGgw3OTk4ODA1OTczMDEiDOjPR/BekWRBAJjJFiqUAkSfDoUA5+7kiQ9ksUraMyzlq/gXXhJZn7MywlFHYxt7WYIVhugtk0jnQuK6WgZR6TI8ekg959vt9CYEIp5eNEpo3csRQrDUK6RFgl3i5NBTmEQZU51V03ozjjwioMqBxIXK7LKnQJeOrHJ58t81iqNJoxbD4+DvvKlYMnTJYdcdTCczAi6+gwt0Gu9W4B2oQs15iZKiXBss7ASDGFvfuKu4Fevj3fdRPeWIhbwa1lP8UgIQQE02xYWuvh0Xn92AUQB1QWae97mUN3P3zhj1SsviJy9L6+JJ/OcOtQTPi23jeYw2p+FpDV16jXUbDaKIFG5GL8VntsixfwJ91GZsGFmIaKhfSrxbh/cAxUEXLVnQjttPNDC9wYvBBjqdAQC2s8I+9VPwgoSyLLx7swmOhLjbRJyaRDBzzdJrOkIlj9/D+8bLhs+/3kKCNayTbZj/rpXPsu+09ocP7FQo2cAUx2MTIjKLKhI16vcCN9AAWYqSkbCRt6G4/u8YMHqU8SfHLMb8Q+ufbhluveOQ/Eo6eOrdih9PGgK5yms40ecsOmgBoVi60HTnT0JwkGuPTB5UfmFoXPXa42+bkW8="
EOF
# Carga las variables ahora
source ~/.bashrc

# 3) Espera a que el clúster EKS esté ACTIVE
until aws eks describe-cluster --name "${cluster_name}" --region "${region}" \
       --query "cluster.status" --output text | grep ACTIVE; do
  echo "Esperando a que el EKS (${cluster_name}) esté ACTIVE..."
  sleep 10
done

# 4) Configura kubeconfig e instala el driver CSI de EFS
aws eks update-kubeconfig --name "${cluster_name}" --region "${region}"
kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"

# 5) Espera a que los pods CSI estén listos
kubectl wait --for=condition=Ready pods \
  -l app.kubernetes.io/name=aws-efs-csi-driver -n kube-system \
  --timeout=600s

# 6) Monta tu EFS en la EC2 y prepara carpetas
sudo mkdir -p ${efs_mount_point}
sudo mount -t efs ${efs_id}:/ ${efs_mount_point}
sudo bash -c "echo \"${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0\" >> /etc/fstab"
sudo mkdir -p ${efs_mount_point}/ftp ${efs_mount_point}/mysql ${efs_mount_point}/pagina
sudo mkdir -p /mnt/efs/ftp /mnt/efs/mysql /mnt/efs/nginx
sudo chmod -R 777 /mnt/efs
# 7) Instala y arranca SSH
yum install -y openssh-server
systemctl enable sshd
systemctl start sshd
