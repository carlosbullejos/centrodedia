#!/bin/bash
sudo su
cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42R3JXPXLD"
export AWS_SECRET_ACCESS_KEY="sbGESarZUICEvkfvBvLm2N4ru+ocRazNvemRr1c1"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEAQaCXVzLXdlc3QtMiJHMEUCIQDFr4kt/P27uM/4Ia4t5KPYcGkUx9RbaxFRGYUCH1yOaQIgexuI9vIemQidIRQmKAX7INg7d8qmP4hVgY1qjCuyfW0qwAIIvf//////////ARACGgw3OTk4ODA1OTczMDEiDLsfzPffw/XxNYqQ8yqUAuBBsdkADsVQnNn540LvGRyykv5AKaQhhtJwLlhRT40Bg1yMtCyALnCfFXQs7pYJG2miHRcRm/ua2HS38lqyCq0xE+X7AtVIYds67tDZkMO4PqZsSQsoF5of6B1BNb9uZn0T9DC0gV5aGxMef0qD84lNDhumaDYbXNDgfDq5//e5bhs7yA5FRPbckcOAasEw+dF+MxwJ4b5EZL8ucBSHohpsA83K6ukM7bQ55Kbe9jX67UT/caCRzoRqnIyhgpy5o/+n523fJTqGPRa1uwZjet+n+XJtJQnuy0ZrgyaJBbRIeaNl1DKhNHlnWK9iUgH9BpPjYGltJ16V8e7sjJDMj8p8rTo3wT5EnSEI8vhApQPlO0I4HTC5hbfBBjqdAQgHKErSz4sYiL+/XiD9BstktrMcXRQoE6on3M0lqQS2gYx/Ek3lyeMnPwdHPiKb8tpkgRl/v9MLe4v4It6h18PZK28vymI5lE3FmXbpJR+Ho3rqUoZ262uE9gZa94+9bFipxRhuUkmkBfg/fsRIXwoJIY55rfZSDpLAqbrYNko3uoUtka4q8ra0VkD/9vRwTeGeMe76X3rco1rmPm4="
EOF
source ~/.bashrc
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o /tmp/awscliv2.zip
unzip -q /tmp/awscliv2.zip -d /tmp
/tmp/aws/install --install-dir /usr/aws-cli --bin-dir /usr/bin --update
rm -rf /tmp/awscliv2.zip /tmp/aws

yum install -y amazon-efs-utils jq git

curl -fsSL "https://dl.k8s.io/release/$(curl -fsSL https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl" \
     -o /usr/local/bin/kubectl
chmod +x /usr/local/bin/kubectl

cp /usr/local/bin/kubectl /usr/bin/kubectl
chmod 755 /usr/bin/kubectl
chown root:root /usr/bin/kubectl

aws eks update-kubeconfig --name "centrodedia-cluster" --region "us-east-1"

mkdir -p ${efs_mount_point}
mkdir -p ${efs_mount_point}/ftp ${efs_mount_point}/mysql ${efs_mount_point}/pagina
setfacl -R -m d:u::rwx,d:g::rwx,d:o::rwx /mnt/efs/pagina
mount -t efs -o tls ${efs_id}:/ ${efs_mount_point}
bash -c "echo \"${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0\" >> /etc/fstab"
chmod -R 777 /mnt/efs

kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"
yum install -y openssh-server
systemctl enable sshd
systemctl start sshd

# ────────────────────────────────────────────────────────────────────
# Sincronización automática con S3 cada 6 horas
# ────────────────────────────────────────────────────────────────────

# Crear el script de sync
cat << 'SYNC' > /usr/local/bin/efs-to-s3.sh
#!/bin/bash
set -euo pipefail

aws s3 sync /mnt/efs/mysql    s3://carlosbullejos-copiasdeseguridad/backups/mysql
aws s3 sync /mnt/efs/ftp      s3://carlosbullejos-copiasdeseguridad/backups/ftp
aws s3 sync /mnt/efs/pagina   s3://carlosbullejos-copiasdeseguridad/backups/pagina
SYNC

chmod +x /usr/local/bin/efs-to-s3.sh

# Programar cron cada 6 horas
# (0 * * * * sería cada hora, */6 cada 6 horas)
# Programar cron cada minuto
(crontab -l 2>/dev/null; echo "* * * * * /usr/local/bin/efs-to-s3.sh") | crontab -

