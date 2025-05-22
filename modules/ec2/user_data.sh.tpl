#!/bin/bash
sudo su
cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42XK5IQXJP"
export AWS_SECRET_ACCESS_KEY="ab2ufyCCXhH1l0CuX0makCi9kWR6mf1+oWCFzIRv"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEBYaCXVzLXdlc3QtMiJGMEQCIDl7OQr7Meah/LtxRZ+7XD3dV1XibN2BamGeZmxYsuN6AiA0Kq18LsiPNq6f05m8XRch31j7M036aWBjEe/5s+W/4irAAgjP//////////8BEAIaDDc5OTg4MDU5NzMwMSIMCfRSpUapQm0Pmq/jKpQCVWL6yv/1EExn6UBSPJLAavRKsiAKr//FHWHY6U3jCm2HvD1iNnPj5GPDDBI3i+xo28Wy9kSQTiYmKZU+pQCZ37qNrzuJ+Odo4Vxop5+rZ20N7Tg1WQZtsVSUcUUo5C5JA2mnvB9c8jJ8Hq+KTlbrIi5V5o1OMXPZe4nIodz+GcEBgRuW7l9dYctVgXcJjP/dGIAP154XaaHnlvWJlHfFrL8vrmMS4FMyokvhusnSw4FFMt1lrp2caT8sboAzVN8QuvgNzjwyHmD9oU1uojdANyKSTllrYzw9Ib6498oKFRLta8HnOfnJyJLkg6aK8uzgb/YyT/QyQZ5c+jl2m2atlYECRYNrFA/wRCDulY5UNZ3zQRkfMOr7usEGOp4Bd1214EVugH19X9hNSno9vvsYblG2qfj514veEazrxuYkqCB4hiX2CzO6wfD+ZBiKTDyPr2+R/hAj+8kkjEeWiL2lVWBi7/L1FRozEqsZeeLoYxSqwcGOyb3pDZhSTvh8VJi4lodz0Jg3S10k8JakLHQVXI78pTUKT9ql+4rRmPpYxZUxeQwyJ/LIXMPy+YMYQdjPu7x3ecRY1txKQ0g="
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
mount -t efs -o tls ${efs_id}:/ ${efs_mount_point}
bash -c "echo \"${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0\" >> /etc/fstab"
chmod -R 777 /mnt/efs


yum install -y openssh-server
systemctl enable sshd
systemctl start sshd

# ────────────────────────────────────────────────────────────────────
# Sincronización automática con S3 cada 6 horas
# ────────────────────────────────────────────────────────────────────
kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"
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

