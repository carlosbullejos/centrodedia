#!/bin/bash
sudo su
cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42WGL4BLG3"
export AWS_SECRET_ACCESS_KEY="svQaIom6QVfWn5M6EPukv2szwzalHsGN33HgrQVz"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEKb//////////wEaCXVzLXdlc3QtMiJHMEUCIBBeErDAC+shRN7NFB1JxVVc36X9YsSIicWWAYqxLu5EAiEAiFQxfLsARiih4JAQNNcTEN9jIcqtL+3gbGfoXlUMjwUqtwIIbxACGgw3OTk4ODA1OTczMDEiDPXv9fqOT22lR7s1VyqUAt0okNuC684Hu/ctPsIp2Wwd1Bwp5hwN1Ffjj0JQizxRtZ6LkX5n+QCovIJ0zds7j8CX6FcoJOkXjTPzVBa4vDS82lWJTpkfpFIa/Ev3tw1AK78iNi08B2E+M+wwmUAI/cTOPSSNGwOPlTRI1TsdtiwpiXW/Wrqr5ew1I3s+qHDrpwp/WW+e/vAJ6OYg0yfuUbKARnxcLGe0CdCa/cYYdfKh+LTqfQpBKWzQSVBt//YRmO9x65Dcuy5eXYQ4W9qm10ksBn5UfoYDGB1g8rAPOxBJMpwoLdP8eIDc8wNP4m6FYhVLVVyHGif/hAHVAjHd+5jAC7xogS0XKCcB8x5kVPDV0C81d9tcALzpDy6zhCzvdkpuFDCzz9rBBjqdAQz79JDCoGCOgCbJNMrRwHJT/VpznDMQ4vkloCsU2FpbHRwPTdSGMG1PBQkObnU9V0tZWHIIwTs68qxN29nHUdT6ObMrwpMNH3j10FK5h2DKugXA7G8FRhPOBeIvLn7oAcYAWVtdGVuCI0b7WGGiVUr+5foDEePatz7oP9mEUKfndAZCn5KaM/lnJsZFU+fl4JnKB5boN6P/gvxEFcE="
EOF
source ~/.bashrc
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o /tmp/awscliv2.zip
unzip -q /tmp/awscliv2.zip -d /tmp
/tmp/aws/install --install-dir /usr/aws-cli --bin-dir /usr/bin --update
rm -rf /tmp/awscliv2.zip /tmp/aws

yum install -y amazon-efs-utils jq git cronie

curl -fsSL "https://dl.k8s.io/release/$(curl -fsSL https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl" \
     -o /usr/local/bin/kubectl
chmod +x /usr/local/bin/kubectl

cp /usr/local/bin/kubectl /usr/bin/kubectl
chmod 755 /usr/bin/kubectl
chown root:root /usr/bin/kubectl

aws eks update-kubeconfig --name "centrodedia-cluster" --region "us-east-1"

mkdir -p ${efs_mount_point}
mkdir -p ${efs_mount_point}/ftp ${efs_mount_point}/mysql ${efs_mount_point}/pagina
mount -t efs -o tls ${efs_id}:/ ${efs_mount_point}
bash -c "echo \"${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0\" >> /etc/fstab"
chmod -R 777 /mnt/efs


yum install -y openssh-server
systemctl enable sshd
systemctl start sshd

sudo kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"


# Crear el script de sync
cat << 'SYNC' > /usr/local/bin/efs-to-s3.sh
#!/bin/bash
set -euo pipefail

aws s3 sync /mnt/efs/mysql    s3://carlosbullejos-copiasdeseguridad/backups/mysql/
aws s3 sync /mnt/efs/ftp      s3://carlosbullejos-copiasdeseguridad/backups/ftp/
aws s3 sync /mnt/efs/pagina   s3://carlosbullejos-copiasdeseguridad/backups/pagina/
SYNC

chmod +x /usr/local/bin/efs-to-s3.sh

# Programar cron cada 6 horas
# (0 * * * * sería cada hora, */6 cada 6 horas)
# Programar cron cada minuto
(crontab -l 2>/dev/null; echo "* * * * * /usr/local/bin/efs-to-s3.sh") | crontab -

