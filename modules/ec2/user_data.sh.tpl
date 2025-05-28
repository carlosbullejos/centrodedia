#!/bin/bash

sudo su <<'EOF_SUDO_BLOCK'


cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42WGL4BLG3"
export AWS_SECRET_ACCESS_KEY="svQaIom6QVfWn5M6EPukv2szwzalHsGN33HgrQVz"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEKb//////////wEaCXVzLXdlc3QtMiJHMEUCIBBeErDAC+shRN7NFB1JxVVc36X9YsSIicWWAYqxLu5EAiEAiFQxfLsARiih4JAQNNcTEN9jIcqtL+3gbGfoXlUMjwUqtwIIbxACGgw3OTk4ODA1OTczMDEiDPXv9fqOT22lR7s1VyqUAt0okNuC684Hu/ctPsIp2Wwd1Bwp5hwN1Ffjj0JQizxRtZ6LkX5n+QCovIJ0zds7j8CX6FcoJOkXjTPzVBa4vDS82lWJTpkfpFIa/Ev3tw1AK78iNi08B2E+M+wwmUAI/cTOPSSNGwOPlTRI1TsdtiwpiXW/Wrqr5ew1I3s+qHDrpwp/WW+e/vAJ6OYg0yfuUbKARnxcLGe0CdCa/cYYdfKh+LTqfQpBKWzQSVBt//YRmO9x65Dcuy5eXYQ4W9qm10ksBn5UfoYDGB1g8rAPOxBJMpwoLdP8eIDc8wNP4m6FYhVLVVyHGif/hAHVAjHd+5jAC7xogS0XKCcB8x5kVPDV0C81d9tcALzpDy6zhCzvdkpuFDCzz9rBBjqdAQz79JDCoGCOgCbJNMrRwHJT/VpznDMQ4vkloCsU2FpbHRwPTdSGMG1PBQkObnU9V0tZWHIIwTs68qxN29nHUdT6ObMrwpMNH3j10FK5h2DKugXA7G8FRhPOBeIvLn7oAcYAWVtdGVuCI0b7WGGiVUr+5foDEePatz7oP9mEUKfndAZCn5KaM/lnJsZFU+fl4JnKB5boN6P/gvxEFcE="
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
sleep 2 # Pequeña pausa

echo "Configurando EFS..."
mkdir -p /mnt/efs
mkdir -p /mnt/efs/ftp /mnt/efs/mysql /mnt/efs/pagina
mount -t efs -o tls fs-0e52682f3035d15e4:/ /mnt/efs
bash -c "echo \"fs-0e52682f3035d15e4:/ /mnt/efs efs defaults,_netdev 0 0\" >> /etc/fstab"
chmod -R 777 /mnt/efs

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
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42WGL4BLG3"
export AWS_SECRET_ACCESS_KEY="svQaIom6QVfWn5M6EPukv2szwzalHsGN33HgrQVz"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEKb//////////wEaCXVzLXdlc3QtMiJHMEUCIBBeErDAC+shRN7NFB1JxVVc36X9YsSIicWWAYqxLu5EAiEAiFQxfLsARiih4JAQNNcTEN9jIcqtL+3gbGfoXlUMjwUqtwIIbxACGgw3OTk4ODA1OTczMDEiDPXv9fqOT22lR7s1VyqUAt0okNuC684Hu/ctPsIp2Wwd1Bwp5hwN1Ffjj0JQizxRtZ6LkX5n+QCovIJ0zds7j8CX6FcoJOkXjTPzVBa4vDS82lWJTpkfpFIa/Ev3tw1AK78iNi08B2E+M+wwmUAI/cTOPSSNGwOPlTRI1TsdtiwpiXW/Wrqr5ew1I3s+qHDrpwp/WW+e/vAJ6OYg0yfuUbKARnxcLGe0CdCa/cYYdfKh+LTqfQpBKWzQSVBt//YRmO9x65Dcuy5eXYQ4W9qm10ksBn5UfoYDGB1g8rAPOxBJMpwoLdP8eIDc8wNP4m6FYhVLVVyHGif/hAHVAjHd+5jAC7xogS0XKCcB8x5kVPDV0C81d9tcALzpDy6zhCzvdkpuFDCzz9rBBjqdAQz79JDCoGCOgCbJNMrRwHJT/VpznDMQ4vkloCsU2FpbHRwPTdSGMG1PBQkObnU9V0tZWHIIwTs68qxN29nHUdT6ObMrwpMNH3j10FK5h2DKugXA7G8FRhPOBeIvLn7oAcYAWVtdGVuCI0b7WGGiVUr+5foDEePatz7oP9mEUKfndAZCn5KaM/lnJsZFU+fl4JnKB5boN6P/gvxEFcE="
export PATH=/usr/local/bin:/usr/bin:/bin

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
