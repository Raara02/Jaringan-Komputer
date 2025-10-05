# Tutorial Install FreeRadius + DaloRadius di Ubuntu 24

## 1. Perbarui Sistem
```bash
sudo apt update && sudo apt -y upgrade
```

---

## 2. Instal Apache dan PHP

### Install Apache
```bash
sudo apt -y install apache2
```

Cek status Apache:
```bash
sudo systemctl status apache2
```
> Pastikan statusnya `active (running)`

### Instalasi PHP
```bash
sudo apt -y install vim php libapache2-mod-php php-{gd,common,mail,mail-mime,mysql,pear,db,mbstring,xml,curl,zip}
```

Cek versi PHP:
```bash
php -v
```
> Pastikan versi PHP muncul sebagai tanda instalasi berhasil.

---

## 3. Instal MariaDB dan Buat Database

### Instal MariaDB
```bash
sudo apt update && sudo apt install mariadb-server
```

Cek status MariaDB:
```bash
sudo systemctl status mariadb
```
> Pastikan statusnya `active (running)`

Masuk ke MariaDB:
```bash
sudo mysql -u root -p
```

Buat database:
```sql
CREATE DATABASE radius;
GRANT ALL ON radius.* TO radius@localhost IDENTIFIED BY "Str0ngR@diusPass";
FLUSH PRIVILEGES;
```

Cek database:
```sql
SHOW DATABASES;
```
> Pastikan ada database bernama `radius`.

Keluar dari MariaDB:
```sql
QUIT;
```

---

## 4. Instal dan Konfigurasi FreeRADIUS

Cek versi FreeRADIUS yang tersedia:
```bash
sudo apt policy freeradius
```

Install FreeRADIUS:
```bash
sudo apt -y install freeradius freeradius-mysql freeradius-utils
```

Cek versi:
```bash
freeradius -v
```

Cek apakah database sudah terhubung:
```bash
apt list --installed | grep freeradius
```
> Jika muncul `freeradius-mysql`, berarti sudah terhubung.

### Import Database Schema
```bash
cd ~
wget https://raw.githubusercontent.com/FreeRADIUS/freeradius-server/v3.2.x/raddb/mods-config/sql/main/mysql/schema.sql
ls -l schema.sql
mysql -u radius -p radius < schema.sql
```
> Masukkan password `Str0ngR@diusPass` saat diminta.

---

## 5. Aktifkan Modul SQL

Cek versi folder FreeRADIUS:
```bash
sudo ls /etc/freeradius/
```

Jika output-nya `3.0`, jalankan:
```bash
sudo ln -s /etc/freeradius/3.0/mods-available/sql /etc/freeradius/3.0/mods-enabled/
```
> Jika versinya berbeda (misal 3.2), sesuaikan angkanya.

---

## 6. Konfigurasi Modul SQL

Edit file modul SQL:
```bash
sudo nano /etc/freeradius/3.0/mods-enabled/sql
```
> Pastikan konfigurasi `sql { ... }` dan pengaturan MySQL sudah sesuai kebutuhan.

---

## 7. Atur Permission Modul SQL
```bash
sudo chgrp -h freerad /etc/freeradius/3.0/mods-available/sql
sudo chown -R freerad:freerad /etc/freeradius/3.0/mods-enabled/sql
```

Restart FreeRADIUS dan cek statusnya:
```bash
sudo systemctl restart freeradius
sudo systemctl status freeradius
```
> Pastikan `active (running)`.

Jika error, jalankan mode debug:
```bash
sudo freeradius -X
```

---

## 8. Install & Konfigurasi daloRADIUS

### Install Git dan Clone Repo
```bash
sudo apt -y install git
git clone https://github.com/lirantal/daloradius.git
```

### Import Database Schema daloRADIUS
```bash
cd daloradius
sudo mysql -u radius -p radius < contrib/db/fr3-mariadb-freeradius.sql
sudo mysql -u radius -p radius < contrib/db/mariadb-daloradius.sql
```

### Pindahkan ke Direktori Web
```bash
sudo mv ~/daloradius /var/www/
```

### Konfigurasi File
```bash
cd /var/www/daloradius/app/common/includes/
sudo cp daloradius.conf.php.sample daloradius.conf.php
sudo chown www-data:www-data daloradius.conf.php
sudo nano daloradius.conf.php
```
> Pastikan konfigurasi database sesuai (nama DB, user, dan password).

### Buat Folder Log & Set Permission
```bash
cd /var/www/daloradius/
sudo mkdir -p var/{log,backup}
sudo chown -R www-data:www-data var
```

---

## 9. Konfigurasi Apache

### Tambah Port
```bash
sudo tee /etc/apache2/ports.conf <<EOF
Listen 80
Listen 8000

<IfModule ssl_module>
    Listen 443
</IfModule>

<IfModule mod_gnutls.c>
    Listen 443
</IfModule>
EOF
```

### Buat Virtual Host untuk Operator (Port 8000)
```bash
sudo tee /etc/apache2/sites-available/operators.conf <<EOF
<VirtualHost *:8000>
    ServerAdmin operators@localhost
    DocumentRoot /var/www/daloradius/app/operators

    <Directory /var/www/daloradius/app/operators>
        Options -Indexes +FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>

    <Directory /var/www/daloradius>
        Require all denied
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/daloradius/operators/error.log
    CustomLog \${APACHE_LOG_DIR}/daloradius/operators/access.log combined
</VirtualHost>
EOF
```

### Buat Virtual Host untuk User (Port 80)
```bash
sudo tee /etc/apache2/sites-available/users.conf <<EOF
<VirtualHost *:80>
    ServerAdmin users@localhost
    DocumentRoot /var/www/daloradius/app/users

    <Directory /var/www/daloradius/app/users>
        Options -Indexes +FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>

    <Directory /var/www/daloradius>
        Require all denied
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/daloradius/users/error.log
    CustomLog \${APACHE_LOG_DIR}/daloradius/users/access.log combined
</VirtualHost>
EOF
```

---

## 10. Aktifkan Site & Restart Apache
```bash
sudo a2ensite users.conf operators.conf
sudo mkdir -p /var/log/apache2/daloradius/{operators,users}
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
sudo systemctl restart apache2 freeradius
```

---

## 11. Tes Akses Web

Buka browser dan akses:

- Panel Operator/Admin → `http://<IP_SERVER>:8000/`
- Portal User → `http://<IP_SERVER>/`

Akun default:
- **Username:** administrator  
- **Password:** radius
