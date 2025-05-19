#!/bin/bash
sudo su
cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42UP3WAJ66"
export AWS_SECRET_ACCESS_KEY="twSfYNmSS7SZgFPV32bNDL0q0z3H3v0qAoBbfqZY"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEM7//////////wEaCXVzLXdlc3QtMiJHMEUCIQC0wqXYlCtCSs70Hl/SPk4gTuQVOizB2bpp8evpkm9JfwIgbwbpI5sNjiYjKtEHeiQqGCH69IJO123wbU8EgYH5hSsqwAIIh///////////ARACGgw3OTk4ODA1OTczMDEiDJQtwGzv8sF+lXDsbyqUAokN5EZ8XvXsZdNwODp9JQWvfrFSVqFeHc8K7/5MNYZBgwBO6/A20H7TrvVJLv2Ib8xuEu+cOduto7/s8TC9dYsRgfwLdoAv5RZ0T5zF/PVe8ZLa01CrVMZimKciMpmUzTXA37xEaaA18AY1nXHwLa5Xrxg194V04u7WZKoWzFa0lD9AImWlR/RNsjRsDWQTZ91X2vtg+cds9bYymWADwiK6TYsEQ/4btm2zzOyKHafazWaCUgMJ0/jvdMKBz2kHIy/VqHkV4jkKbsBwxDZNY3VkXIcOhNI6cSFndylmRF+ck3Xh4gKcnNJHGRYN17S6/wvSkPp4V6A7a3/nZhQX1gzfD4smA51TA1XNuDf+nJY8CstADTDOkavBBjqdAczl3dmfdgq5sGvTfqDcVIAVBiF5gUrYVFGjedbzMehQ8dvd80rV9ReiFToYEAI1NMi2UKylEKCTch/IRQAX96TbqQlMFtUVg/ms8DZB0pv18JU+yco7FnPd4+sr3ydwrz1KTFtK8UYO3RXxbWra/1ShkJNKEVYu6iJtejQjTt2MrwXHzAzoEmA+3iXx08tJkCgGTV/M+lBDP4ZB+60="
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
sleep 10
kubectl apply -k "github.com/kubernetes-sigs/aws-efs-csi-driver/deploy/kubernetes/overlays/stable/ecr/?ref=release-1.7"

mkdir -p ${efs_mount_point}
mount -t efs -o tls ${efs_id}:/ ${efs_mount_point}
bash -c "echo \"${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0\" >> /etc/fstab"
mkdir -p ${efs_mount_point}/ftp ${efs_mount_point}/mysql ${efs_mount_point}/pagina
chmod -R 777 /mnt/efs

yum install -y openssh-server
systemctl enable sshd
systemctl start sshd
