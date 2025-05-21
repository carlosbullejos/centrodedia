#!/bin/bash
sudo su
cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42SSHS2GQD"
export AWS_SECRET_ACCESS_KEY="0gZPqz3EgcQAnl43KFFvi67P6G0n31bbHUaAU6ho"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEP7//////////wEaCXVzLXdlc3QtMiJHMEUCIAtmgKo0ixIzOQdQBrvmiiMBZtFpiH/1hyHdSowyxLMQAiEAo4AN9/OHzz3QwLCeZm/u++YbsjHtlBGwzEjY4hmX6ykqwAIIt///////////ARACGgw3OTk4ODA1OTczMDEiDISavwcYEHE9aekOmyqUAt2MslRHERluGmX+956Hs1vbbC2E5TrQ4D7RkulcK/8uYK10bMwTwlaCnVl9Svez4wnqjC64IUwtji/v5c8Xb7A4MEFOJEpv+KG0wUezbjBFPTw5AyCRazriPFpoalc/+jFvt3qd9D57elIE/qfRMUzYaQcPf+nCLP+Mc359Tzn50XiucDQ1jvoAxt3Ssmu+/RXFni22kSNPVVBdQUmRWmN4/CntFgYazm9ErH5fK5xAH0+Rvm/ZDtPheirFfQxLQgRDuJsimuZ1e2pL7DiWo+co0Xy8THF1M3zOphToQdiEYMykuaPZkGE1ZKQvZIbZ5npO3F4tmDxCMqEgp92sD/zPKAC2eQ6iVdLqqzTiieqQu7rJlTDi2LXBBjqdAaps1EcoNIoaY4g9kvURcj6Qat0JfHAiciMLecwB3tUK9czl+dOyK+SBBNoLwGfGH7ALezMpEDGXaQgmGCIFixJAU5U9azTRNUGWsFYrtC0qitkv9pkEtZh2ih4T7zgRC4tHJlFOHxYnOpraSj7/oxtI5QOKC0M2inuNEbKMzqaq8pbcx0G7WBWfIJTCpTTB0KxbajhlUnoJf1lMbok="
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

