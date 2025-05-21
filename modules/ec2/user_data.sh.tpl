#!/bin/bash
sudo su
cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42QMNEBUBP"
export AWS_SECRET_ACCESS_KEY="QKfQBM66krGmDFjbMQPj/7X6oFVCReBIwH1VOUwl"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEOb//////////wEaCXVzLXdlc3QtMiJHMEUCIFQCeI9oMkn8fGKCbPNVt3cFyTfVSHEOsdR0B3F2TfTlAiEAloa2vh1VR1PCA+PdRj0AFiX/44iozYE5d5emYcQKGBUqwAIIn///////////ARACGgw3OTk4ODA1OTczMDEiDA2okxzdyTa0+gdUPCqUAhIIgB5dk6QxzrI9S2gqbnnhd5HxrsJgVerAykykbwBs845A8ljYdDnqrL7rseTbZdtjLbZlouNF8ZEkUucXX9snSO58KKIikMpR0qHUVqnaS1TBGl9DHeUxmQ23/mJkkAjeang635gKVk6zCmfGTc20MCiwR+XeSoSJ9o18W5UCTqYD16U/xyJeBjoa6yVfigLNiqs8k2fhcYAEIWm5GwPb0HtVcloIbZoqrWCXqAV4bnhDdmPwtMYJgQ6Bn4bKHQsEQg5R43HS52ZX1syxbrSFbXBpWsTWhTgSr4S/lIpInd/LvwtEznl59D/0p1vVdbBM9I45rn8JPEckPXQ0kcmeHqoGx7QqOISVhNBXduMp59TGUDChuLDBBjqdAVelZdalwNjXMUM8SVTA2J7ThnEQLDCi8k03kBYRl/tesWah6El9MN4l6/XG3pd/+fapWeMDlr6lBRzf5jst/l4u4vDS6JseS/eNWwChKpq/Gw7qAGTbcjQpjIn8dl/ypE30PZYDXl/L+zJAtaoCA2nHZoCMX7cFTn3CplBDcZAc6wuCqfKMCeB85/lBwPF061dLkmVDxw1T9ppP1nE="
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
mount -t efs -o tls ${efs_id}:/ ${efs_mount_point}
bash -c "echo \"${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0\" >> /etc/fstab"
mkdir -p ${efs_mount_point}/ftp ${efs_mount_point}/mysql ${efs_mount_point}/pagina
chmod -R 777 /mnt/efs

kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"
yum install -y openssh-server
systemctl enable sshd
systemctl start sshd
setfacl -R -m d:u::rwx,d:g::rwx,d:o::rwx /mnt/efs/pagina
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

