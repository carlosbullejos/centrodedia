#!/bin/bash

sudo su <<'EOF_SUDO_BLOCK'


cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42VTLLQWMO"
export AWS_SECRET_ACCESS_KEY="uMCJQUNT1udr8+RYYC7/I9yYeaglIyQ24XJsL88n"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEKr//////////wEaCXVzLXdlc3QtMiJHMEUCIFUp6CnFRWKln/lHGxSEcvhkaOihPVk9nZwwRV+UKg1hAiEAkPpWbo5151UjjTgaoLpREdjtJUzt+k/uaLvvO+eLqL4qtwIIcxACGgw3OTk4ODA1OTczMDEiDD9vo3rLNZDjpGwB4yqUApNBZnJ2JHNj0IM3W+eNBLlAWhq252NhQQsAaO6gWel0MO6kGeIvRISnSLWrAmSZsHw+I2qPGLpIh5BbgpoSr5DgJGxEpmF63dqmXqSPb3jXgRz+XKJLSnfMRIpdq7s6e+O6RqShNDfLtD6bCCrIzyy/5G1qklwTba808zuTNOUE1yWopUfsPe/xyAURrMhHAdJdfh4L4IOFozPXEzRj5n1DRRcPR/nRTN4qgYEp03X8iiF//Fw7pOKsFvrg7o7N5Jih7WGWb0JsXBEW0q26Kj7qV6oc0VJGxP3V8HVxfCN4m7KMOIl4QkU3dRxF/p9Ce0M79UvbovG5c2cFj33muZxUEPnmQJhJ2qUUyvcGF6I2Cw/DjzDmwNvBBjqdAYADjX/d2sbe0S/d95hBka1TPop1frXa7/ZJJkudwiic/DiAzUXvdCretR0popdMfwpNtHIdFpvYL/c3vtWIXVz1Sxd5xVnBuxsMAJoezLKOKraKV8tVUsr6H+PWDiVPh4w3n5OReUY8B4RzujtEPPjcSVYwR5vb9sZSWpCKj8VATX2f5quP9npa3wrdHVpNJfhy2FGjFDyCKKdH7kY="
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
mkdir -p /mnt/efs/ftp /mnt/efs/mysql
mount -t efs -o tls fs-0e52682f3035d15e4:/ /mnt/efs
bash -c "echo \"fs-0e52682f3035d15e4:/ /mnt/efs efs defaults,_netdev 0 0\" >> /etc/fstab"


# dentro de tu user_data, en lugar de git clone directa:
TOKEN=${var.git_token}
REPO="github.com/carlosbullejos/centrodedia.git"
BRANCH="kubernetes"
TARGET_DIR="/mnt/efs/"
SUBDIR="pagina"   # la carpeta que quieres

# Inicializa un repo vacío
git init "$TARGET_DIR"
cd "$TARGET_DIR"

# Añade el remoto y la rama que quieras
git remote add origin "https://${TOKEN}@${REPO}"
git fetch --depth 1 origin "$BRANCH"

# Activa sparse checkout y especifica la carpeta
git config core.sparseCheckout true
echo "${SUBDIR}/" > .git/info/sparse-checkout

# Trae sólo esa carpeta
git checkout "$BRANCH"



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
