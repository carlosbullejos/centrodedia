#!/bin/bash
sudo su
cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42UVOI62KO"
export AWS_SECRET_ACCESS_KEY="47CDUdrqhYKA1kuoGlF66MgF9SF+l7zc0VA7Hi85"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEHAaCXVzLXdlc3QtMiJHMEUCIQCFg8l5pNZd303OYbavBbd91LjFkK3tVIgahtbyXDIsLwIgWgpTbdlBfcGtB0B1sdSA4SVT3n3tyyf41wlGnXBEi6AqtwIIKRACGgw3OTk4ODA1OTczMDEiDC6Ts4ZIkR6NAxCoYCqUAqmYUUVokKbAJ+vxmDmlwwEcA3wSTeFlSm1oQgby6mko18tm5qlkrU02mT3Cq3alV7jHCQzRqQCjpa+WRVjy6R3WUkqVgS27B8+j2s+oK+oL3cKba4z4g+pK7cgslt6+5M8jX1laeAMjqFLjo5ff5Z8bbCrC5QiAkbMkMHVSYYhKtGSPs48tX8S4nmSJ5yOuR/hpNrWA86ip8FSY2lXEFR/qxrbxSM6g2o+dYueI69ZQBEWcMwDa92S7s/6nNFP5/GB9sRWliDJH5RDL/5rT7KX3cP/FAcNPgMlFj4Mf7FrWOwaW8mqVPwajeITPpyPe+c4fOIu/3JGMHDqbSD6BHwuFE9ytLmCCy0hOkqbfdG3TM/3J7TDmvJbBBjqdAQZcPd0eP3O/e5wLkJXKOL1v3GiU8gGUuXZB4+4euCF88xgOhWFCUeV/5OVAUn1St5D6YI1gvaUXwjwJNjxlsuf0vfta5F1aTAeC53j1to5X8qtiIJfSi8yWY7R1ZDayNcazQwiTLebjCwGA1erqoDfds5sRM0SFQqJ3z8hxLFCzCf/Uy9S156NC1sQfCFxDqWBy/tJQ5+pLwJO2mFQ="
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
kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"

mkdir -p ${efs_mount_point}
mount -t efs -o tls ${efs_id}:/ ${efs_mount_point}
bash -c "echo \"${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0\" >> /etc/fstab"
mkdir -p ${efs_mount_point}/ftp ${efs_mount_point}/mysql ${efs_mount_point}/pagina
chmod -R 777 /mnt/efs

yum install -y openssh-server
systemctl enable sshd
systemctl start sshd
