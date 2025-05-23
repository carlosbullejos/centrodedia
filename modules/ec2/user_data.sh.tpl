#!/bin/bash
sudo su
cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42X4JJWODZ"
export AWS_SECRET_ACCESS_KEY="m4TAyy3RMKzw9zwZufTmhXM3oY8AAkl7SfVhE/Gk"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEC4aCXVzLXdlc3QtMiJIMEYCIQCPHP5K8P1rFqqoRTn4HPFSF347PIKkA47jF4UeySdLGAIhAIpmD+1FJNjyCgVBYVDQbjrHQDpDzDNoIzQOl7AyCgJDKsACCOf//////////wEQAhoMNzk5ODgwNTk3MzAxIgzeVMD01YOZfbjqUWUqlALd7/w0SbunYMwrpL594p13N9oQ8ffQglA2ZH7jSlz9NWoLls0SELINuSBzOn9xwrMIRrGSliQCZ8S5s/tv36+l1N43TfGBL9dq/r+GowTY/JhFsurZBmDTPwxb2cbJEghRim3EXlU0667feKV0J+z+x2MzCrMzWoh8sisySypNZHiQCoTFYF8/DuGpy+koYPjH39Y2GtchmKrPCXyiuW87tWMF9JqTj4JKOYGWCmADgJfqrbx7ZRUHSQCBfqELgZ/HUyw+SANTf0wtAw56FC/XZjZY/TAxVvdhlHSy2nIXOHph6mIPs11KWsjo3rciisWkVUonsd+nfEUfjcA5CmSdinbsCFtak++ztVuud3bMJISYw/8w3Z7AwQY6nAFuIKy6zsyYnv/TPKbBQTl2VPKWzUCOyR0iIv6b8xg+/B5XnBT6U3uroVIXikXhJYX+lrYaT3PZdsj05E1JbAyEru/RjYObNJZbumVxF9uVa3gDCANqGXs9g2SliUWDTmNLrGfTLs9HBqWjcMrj3ixw/8EgkWP7sNDg85jfGWNMdb0T4aOHIyhaWMYoy9KxU0fT0q46lHfDdTTJv8w="
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
sudo setfacl -R -d -m u::rwx,g::rwx,o::rwx /mnt/efs
mount -t efs -o tls ${efs_id}:/ ${efs_mount_point}
bash -c "echo \"${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0\" >> /etc/fstab"
chmod -R 777 /mnt/efs


yum install -y openssh-server
systemctl enable sshd
systemctl start sshd

# ────────────────────────────────────────────────────────────────────
# Sincronización automática con S3 cada 6 horas
# ────────────────────────────────────────────────────────────────────
sudo kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"
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

