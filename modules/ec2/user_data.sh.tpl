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
git fetch --depth 1 origin "pagina"

# Activa sparse checkout y especifica la carpeta
git config core.sparseCheckout true
echo "pagina/" > .git/info/sparse-checkout

# Trae sólo esa carpeta
git checkout "pagina"



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

cat << SYNC > /usr/local/bin/efs-to-s3.sh
#!/usr/bin/env bash

# 1) Credenciales y PATH para cron
export AWS_ACCESS_KEY_ID="${aws_access_key_id}"
export AWS_SECRET_ACCESS_KEY="${aws_secret_access_key}"
export AWS_SESSION_TOKEN="${aws_session_token}"
export AWS_DEFAULT_REGION="${aws_region}"

# Definimos explícitamente el PATH para que cron encuentre el comando 'aws'
export PATH="/usr/local/bin:/usr/bin:/bin"

# 2) Sincronización
echo "Iniciando sincronización a S3 en \$(date)"

# --- ¡¡¡CAMBIO IMPORTANTE!!! ---
# Excluimos el fichero .sock de la sincronización de MySQL
aws s3 sync /mnt/efs/mysql/ s3://carlosbullejos-copiasdeseguridad/backups/mysql/ --exclude "*.sock"

# El resto de las sincronizaciones se quedan igual
aws s3 sync /mnt/efs/ftp/    s3://carlosbullejos-copiasdeseguridad/backups/ftp/
aws s3 sync /mnt/efs/pagina/ s3://carlosbullejos-copiasdeseguridad/backups/pagina/

echo "Sincronización finalizada en \$(date)"
SYNC

chmod +x /usr/local/bin/efs-to-s3.sh

# Añadir al crontab de root con logging
CRON_JOB="* * * * * /usr/local/bin/efs-to-s3.sh >> /var/log/s3-sync.log 2>&1"
if ! sudo crontab -l 2>/dev/null | grep -Fq "/usr/local/bin/efs-to-s3.sh"; then
    (sudo crontab -l 2>/dev/null; echo "$CRON_JOB") | sudo crontab -
    echo "Cron job añadido para root: $CRON_JOB"
else
    echo "Cron job ya existe para root."
fi

echo "Configuración de Cron finalizada."
EOF_SUDO_BLOCK

echo "Script principal finalizado."
