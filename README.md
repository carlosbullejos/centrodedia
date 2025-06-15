Plataforma de Gestión Integral para Centro de Día "Bullejos"

Este repositorio contiene el código fuente, la infraestructura como código y los manifiestos de despliegue para la plataforma web de gestión del Centro de Día "Bullejos".
El proyecto ha sido desarrollado como parte del Proyecto Final de Grado Superior de Administración de Sistemas Informáticos en Red (ASIR).

Descripción

La plataforma permite la gestión centralizada de usuarios, trabajadores, alumnos, cursos, tareas, inventario y documentación del centro.
Está diseñada para ser desplegada en la nube de AWS utilizando tecnologías modernas como Kubernetes (EKS), almacenamiento compartido con EFS, y automatización de la infraestructura mediante Terraform y Ansible.
Estructura del repositorio

/pagina/
Código fuente de la aplicación web (PHP, Nginx, MySQL).


/terraform/
Ficheros de infraestructura como código (Terraform) para desplegar la red, EKS, EFS, EC2, S3, etc.


/kubernetes/
Manifiestos YAML para el despliegue de los servicios (Nginx, PHP-FPM, MySQL, FTP, volúmenes, etc.) en Kubernetes.


/ansible/
Playbooks y roles para la creación de las imágenes que posteriormente se usarán con kubernetes.


Tecnologías utilizadas
Lenguajes: PHP, Bash, YAML, HCL (Terraform)
Infraestructura: AWS (EKS, EFS, EC2, S3)
Contenerización: Docker, Kubernetes
Automatización: Terraform, Ansible, GitHub Actions
Base de datos: MySQL
Servidor web: Nginx
FTP: vsftpd

Despliegue rápido
Clona este repositorio:
git clone https://github.com/carlosbullejos/centrodedia.git


Configura las variables y credenciales necesarias en los archivos de Terraform y los workflows de GitHub Actions.
Despliega la infraestructura con Terraform.
Construye y sube las imágenes Docker a ECR.
Aplica los manifiestos de Kubernetes en EKS.
Accede a la plataforma desde el navegador.
Consulta la documentación para una guía detallada de instalación y uso.

Autor
José Carlos Bullejos Gómez
Proyecto Final de Grado Superior ASIR

Contacto
Para dudas o sugerencias, puedes contactar en:
admin@centrodiabullejos.es
