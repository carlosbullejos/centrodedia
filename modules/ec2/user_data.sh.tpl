#!/usr/bin/env bash
# Instala cliente EFS
yum install -y amazon-efs-utils

# Monta el EFS en /mnt/efs
mkdir /mnt/ftp
mkdir /mnt/mysql
mkdir /mnt/pagina
mkdir -p ${efs_mount_point}
mount -t efs ${efs_id}:/ ${efs_mount_point}

# Añade al fstab para que persista tras reboot
echo "${efs_id}:/ ${efs_mount_point} efs defaults,_netdev 0 0" >> /etc/fstab

# Instala y arranca el demonio SSH (para que puedas conectarte)
yum install -y openssh-server
systemctl enable sshd
systemctl start sshd
