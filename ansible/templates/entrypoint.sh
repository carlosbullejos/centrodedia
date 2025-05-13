#!/usr/bin/env bash
set -e

# Crea el usuario usando las variables de entorno
useradd -d /home/"$FTP_USER" -s /bin/false "$FTP_USER"
echo "${FTP_USER}:${FTP_PASS}" | chpasswd

# Prepara directorios
mkdir -p /home/"$FTP_USER" \
         /home/ftpusers/shared \
         /var/run/vsftpd/empty \
         /var/run/vsftpd
chown -R "$FTP_USER":"$FTP_USER" /home/"$FTP_USER" /home/ftpusers /var/run/vsftpd
chmod 755 /home/"$FTP_USER" /var/run/vsftpd 
chmod 775 /home/ftpusers /home/ftpusers/shared

# Ejecuta el demonio
exec "$@"
