#!/bin/bash
sudo su
cat << 'EOF' >> ~/.bashrc
export AWS_ACCESS_KEY_ID="ASIA3UPEZJ42XOUQXXIT"
export AWS_SECRET_ACCESS_KEY="M4lMOa0KGQGgT5cfYvkPB9ofY1ozxvgGyxPkWeVM"
export AWS_DEFAULT_REGION="us-east-1"
export AWS_SESSION_TOKEN="IQoJb3JpZ2luX2VjEIr//////////wEaCXVzLXdlc3QtMiJHMEUCIQD2oloAV+7oZfoMgGfKGUfa4P/BOdxgHyNAWdCdChfsZgIgDcwVyWlVSq7kFrZFk7gvf0Nf21nTOcNSgRepWQrx+UQqtwIIQxACGgw3OTk4ODA1OTczMDEiDLSYAmda9Vv8o686piqUAt/hwwzoANh/B8UA1elFgqx/nNiIZuS0YER2OzFqYvEjjZ6NISDvJ/hcRhjLmtDR3Oea7LqHqC36DwbKiytwOR66AlYp8ewLbRxvWDMYTXUCEUpH6M5Le3prxkRisWRi0eT2hzwevcJybMLjwDphzzUfnuBA89XqXC69BoMEvsvf5QvBp0wAtMLN4YIng0lq4kjiMRSSlbbfpU6Lr0OlqA2rJ0PItSgU7C+AMSaTZQZaGs++PTlTnZxYIyMsPrFJHJt/wQgElxnN+UPj57oolczsYgDSzXNXVWiWvOwP7Wsjuzw1IpaZqAWtMvNKTKJwtwLssfIMRfp0PnVVm8J2se66vnmD7pDQeK2KDO0HmKBQEM2oCDCeoZzBBjqdAVGdag/mNJ0emZPBTVe2/JyRlo1NEqSYm6Jvh3SGDKZHQvv8MPaB1IBmeMxXoh/3tQSqu3M0WLz3oRXGi7mw0Ks7fL1tG88gBG8tX6IVElrNErW2wAxci7DuhEFAohWKt26oMLSXgrCNr3rTf0CdifeNF8t0DDpbflPjB3N1MTC2rrHc6FcdHCKZMHuzqValtOM9vF3Uqx1zs0ErFTM="
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
