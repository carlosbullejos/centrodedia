#!/bin/bash

sudo su <<'EOF_SUDO_BLOCK'


cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="${aws_access_key_id}"
export AWS_SECRET_ACCESS_KEY="${aws_secret_access_key}"
export AWS_SESSION_TOKEN="${aws_session_token}"
export AWS_DEFAULT_REGION="${aws_region}"
EOF
source ~/.bashrc


echo "Instalando AWS CLI y herramientas..."
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o /tmp/awscliv2.zip
unzip -q /tmp/awscliv2.zip -d /tmp
/tmp/aws/install --install-dir /usr/aws-cli --bin-dir /usr/bin --update
rm -rf /tmp/awscliv2.zip /tmp/aws

yum install -y amazon-efs-utils jq git cronie

echo "Instalando kubectl..."
curl -fsSL "https://dl.k8s.io/release/$(curl -fsSL https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl" \
     -o /usr/local/bin/kubectl
chmod +x /usr/local/bin/kubectl

cp /usr/local/bin/kubectl /usr/bin/kubectl
chmod 755 /usr/bin/kubectl
chown root:root /usr/bin/kubectl

echo "Actualizando kubeconfig..."
aws eks update-kubeconfig --name "centrodedia-cluster" --region "us-east-1"


echo "Configurando EFS..."
mkdir -p /mnt/efs
mkdir  /mnt/efs/ftp 
mkdir  /mnt/efs/mysql
mount -t efs -o tls ${efs_id}:/ /mnt/efs
bash -c "echo \"${efs_id}:/ /mnt/efs efs defaults,_netdev 0 0\" >> /etc/fstab"

mkdir  /mnt/efs/ftp 
mkdir  /mnt/efs/mysql





# Inicializa un repo vacío
git init "/mnt/efs"
cd "/mnt/efs"

# Añade el remoto y la rama que quieras
git remote add origin "https://${git_token}@github.com/carlosbullejos/centrodedia.git"
git fetch --depth 1 origin "kubernetes"

# Activa sparse checkout y especifica la carpeta
git config core.sparseCheckout true
echo "pagina/" > .git/info/sparse-checkout

# Trae sólo esa carpeta
git checkout "kubernetes"



chmod -R 777 /mnt/efs/pagina
echo "Instalando servidor SSH..."
yum install -y openssh-server
systemctl enable sshd
systemctl start sshd

echo "Aplicando manifiesto de Kubernetes para EFS CSI Driver..."
export KUBECONFIG=/root/.kube/config
kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"
echo "Comando kubectl apply -k ejecutado."

echo "Configurando cron..."
systemctl enable --now crond

# === Aquí modificamos SOLO el bloque de crontab ===

# Crear el script de sync incluyendo las credenciales AWS para cron
cat << 'SYNC' > /usr/local/bin/efs-to-s3.sh
#!/usr/bin/env bash
set -euo pipefail

# 1) Credenciales y PATH para cron

export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42U5E5I2ZX"
export AWS_SECRET_ACCESS_KEY="X8E22dcWYq43rNsonmtk3alChBAn0TIeeHUi2tYp"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjELL//////////wEaCXVzLXdlc3QtMiJIMEYCIQCG1YBNZP1hfJnR/QLYblC+PUm2mJc3SLXgmVwTZrDBsgIhALQJSfm3E8tATuMXG0KAODOEEa39Us2ThCgJR+p17CAQKrcCCHsQAhoMNzk5ODgwNTk3MzAxIgzP+JWLGEKWqhmw8+YqlAIVykKaKDwTaBnFiQE3fkTg4w9K1W9p4+S/rgg15uB53LPBJ3EnhrN8Ck4dsjxQPKNhnbQpXKEIBMztN7WoGxWeZhTGQJ6ST3LzJ90k8WVBeTc3pofinyb7Xupb+8C3rfEJ2RolyFW1G3wNH86sNCabZ/JnOzSju6CyWtSS6+3SXRz48JJSlSCPBCATQOYYinK5xGYztdBBCiOMFJPE5A1cNEYYPYPaXOVuTbIKAib/Rr56PyZq/pbFY1U/8ndwPh3BD0aDrInqQem71fqp+60RGv/hUQE5rkpMULOM0+Wt9Al6+Q3zkMdJk/Es6ljUrFFhXhXuUtK4ct9gaFwNqDBY/cBCGDhQSByHXzUcAnIGjsjKVmwwhpDdwQY6nAHSzdQk/ugJ/SsB6UOlyWWmYElqYThk31uokGmSFw77jRBOmYltv7q1FnYoAmGDdu1Qe/21zdBEiM2WqLfb74nJCGld505pwOJEyCzb0MSvHHcPWCJYM7QGDb7kIRF4Cp80/qe8WmcGG+bnKGV8l/a10NoW1RNq9yIhRJ0yyqJ5yb6gQN01hZFmPCiLEnLhFi0/O42xagpsD84QH7k="


# 2) Sincronización
aws s3 sync /mnt/efs/mysql  s3://carlosbullejos-copiasdeseguridad/backups/mysql/
aws s3 sync /mnt/efs/ftp    s3://carlosbullejos-copiasdeseguridad/backups/ftp/
aws s3 sync /mnt/efs/pagina s3://carlosbullejos-copiasdeseguridad/backups/pagina/
SYNC

chmod +x /usr/local/bin/efs-to-s3.sh

# Añadir al crontab de root sin duplicados
CRON_JOB="* * * * * /usr/local/bin/efs-to-s3.sh"
if ! crontab -l 2>/dev/null | grep -Fq "$CRON_JOB"; then
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    echo "Cron job añadido: $CRON_JOB"
else
    echo "Cron job ya existe: $CRON_JOB"
fi

echo "Configuración de Cron finalizada."

echo "Bloque sudo su finalizado."
EOF_SUDO_BLOCK

echo "Script principal finalizado."
