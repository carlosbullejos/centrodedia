#!/bin/bash
sudo su
cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ427PBTPC23"
export AWS_SECRET_ACCESS_KEY="atKLBUxDk+yGO1O+IEa44ctLQnQQYwsIa6ISmGxv"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEIb//////////wEaCXVzLXdlc3QtMiJIMEYCIQDoV8jnrCd0JXoYluj1P493i0kHZJyry9B8Ym4D9bXaEwIhAMkbEptx/x/+62ykLOS/ZehPOzJOT40MKfdwtDlu47RtKrcCCD8QAhoMNzk5ODgwNTk3MzAxIgz+/2rpFOJwLxoYb5cqlAKq/B1lOI2cG/NIxKAbGJ+FjR45DbEUuRc+RuydL3s2O19446FOAud2S/BM2ooysHKW9/8puiu73nqBdIMesHFpHPZwF90oDRCNLb5EUG20n0SXOC/N147dxGRauTZbfo6G0ow1TP1tkYkYG7MuJucI3Qj3kbeRAifeTb2MeZB0ipeJVQoPzO40Bdgso/tHHb3y6J2FpsByViwHI1Nm5dfmst3M0dLMDVE6YBlWi0a1BWqiR5/HJAOAx6tq3jXB0U/BVs7dmY5kiD3twK5vfuZhnqL5mpkbJrA+fF54hoG30s1bVJQWjycYyJILNtWIvB9eyoVVf2DW1FJ5Jp4jwtzcLZxQZQGkLueajRuwRR1c6zdAWrkwkqqbwQY6nAE0yIOUQ+W2TvMzjpw0Gqr31yk8LR1JYp5Ht6myMaFu5r17gEdoFOJxqdxTPtsa9YDX7N70KovGT2xuhOn6RH8VttTAbUFweMWjGAaKRZIRItoHdz7pM6VZ45y2LB7LDXT4Xls9jM1KC5WqyxMrAKA6aE55lvQVWFHh5C2rmQc8ZMqNnwfxQ9r+bgsD0w5z1NqJeo7hYYqC4zfreNE="
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
