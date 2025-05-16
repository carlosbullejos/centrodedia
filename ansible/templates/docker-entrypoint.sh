# docker-entrypoint.sh
#!/usr/bin/env bash
set -euo pipefail

# Si es la primera inicialización (no existe /var/lib/mysql/mysql)
if [ ! -d "/var/lib/mysql/mysql" ]; then
  echo ">>> Generando init SQL para usuario admin..."

  # Leer el hash generado en build
  ADMIN_HASH=$(cat /tmp/admin_hash)

  # Crear script idempotente en /docker-entrypoint-initdb.d
  cat > /docker-entrypoint-initdb.d/01-admin-user.sql <<EOF
USE \`${MYSQL_DATABASE}\`;

INSERT INTO users (nombre, email, password, ftp_password, rol)
SELECT 'admin', '${ADMIN_EMAIL}', '${ADMIN_HASH}', '${ADMIN_HASH}', 'admin'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM users WHERE email='${ADMIN_EMAIL}'
);
EOF
fi

# Delegamos al entrypoint oficial de MySQL
exec docker-entrypoint.sh "$@"
