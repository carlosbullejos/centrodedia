#!/usr/bin/env bash
# Instala cliente EFS
yum install -y amazon-efs-utils

# Monta el EFS en /mnt/efs

mkdir -p ${efs_mount_point}
mount -t efs ${efs_id}:/ ${efs_mount_point}

# Añade al fstab para que persista tras reboot
echo "${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0" >> /etc/fstab
mkdir /mnt/efs/ftp
mkdir /mnt/efs/mysql
mkdir /mnt/efs/pagina
# Instala y arranca el demonio SSH (para que puedas conectarte)
yum install -y openssh-server
systemctl enable sshd
systemctl start sshd
