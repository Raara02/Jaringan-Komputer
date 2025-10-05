Tutorial Install FreeRadius + DoloRadius di Ubuntu 24.

// **1.	Perbarui Sistem**
```sudo apt update && sudo apt -y upgrade```

2.	Instal Apache dan PHP
-	Install Apache dengan menjalankan
sudo apt -y install apache2

-	Cek Status Apache
sudo systemctl status apache2
Keterangan : pastikan sudah tertulis active (running)
 

-	Instalasi PHP di ubuntu
sudo apt -y install vim php libapache2-mod-php php-{gd,common,mail,mail-mime,mysql,pear,db,mbstring,xml,curl,zip}

-	Cek versi PHP
php -v
Keterangan : jika sudah terinstall maka akan muncul versi dari PHP
 

3.	Instal MariaDB dan Buat database
-	Install database MariaDB
sudo apt update && sudo apt install mariadb-server

-	Cek status MariaDB
sudo systemctl status mariadb
Keterangan : pastikan sudah tertulis active (running)
 
-	Masuk ke MariaDb
sudo mysql -u root -p

-	Buat database :
CREATE DATABASE radius;
GRANT ALL ON radius.* TO radius@localhost IDENTIFIED BY "Str0ngR@diusPass";
FLUSH PRIVILEGES;

-	Cek database sudah terinstall
SHOW DATABASES;
Keterangan : pastikan sudah ada database Bernama radius 
 

Keluar dari MariaDB :
QUIT

4.	Instal dan Konfigurasi FreeRADIUS
-	Cek Versi freeradius yang tersedia diubuntu anda :
sudo apt policy freeradius

-	Install FreeRadius dari  repositori APT resmi Ubuntu
sudo apt -y install freeradius freeradius-mysql freeradius-utils

-	Cek apakah freeradius sudah terintall
freeradius -v
Keterangan: jika sudah terintall maka akan muncul versi dari freeradius anda
 

-	Cek apakah database sudah terhubung dengan freeradius
apt list --installed | grep freeradius
Keterangan : jika muncul freeradius-mysql, maka sudah terhubung
 

-	Download file schema.sql
cd ~
wget https://raw.githubusercontent.com/FreeRADIUS/freeradius-server/v3.2.x/raddb/mods-config/sql/main/mysql/schema.sql 

-	Cek dan pastikan filenya ada
ls -l schema.sql

-	Import schme.sql ke database radius
mysql -u radius -p radius < schema.sql 
Masukkan passwordnya yaitu : Str0ngR@diusPass

5.	Aktifkan modul SQL
-	Cek folder versi freeradius
sudo ls /etc/freeradius/

-	Jika outputcodenya : 3.0, Maka ikuti perintah ini
sudo ln -s /etc/freeradius/3.0/mods-available/sql /etc/freeradius/3.0/mods-enabled/
Keterangan : jika bukan 3.0, maka Ganti saja dengan folder versi freeradius anda misalkan 3.2
6.	Konfigurasi Modul SQL
-	Edit file modul SQL
sudo nano /etc/freeradius/3.0/mods-enabled/sql

-	Pastikan isi dibagian sql seperti ini
 

-	Lalu pastikan bagian mysql ini di komentar (#)
  


//7.	**Atur Permission modul SQL**
```sudo chgrp -h freerad /etc/freeradius/3.0/mods-available/sql```
```sudo chown -R freerad:freerad /etc/freeradius/3.0/mods-enabled/sql```

-	Restart FreeRADIUS & Cek statusnya
sudo systemctl restart freeradius
sudo systemctl status freeradius
Keterangan : Pastikan sudah active (running)
 

-	Jika terjadi eror bisa cek melalui perintah ini
sudo freeradius -X

//8.	**Install & Konfigurasi daloRADIUS**
//-	Install git dan clone repo
```sudo apt -y install git```
```git clone https://github.com/lirantal/daloradius.git```

//-	Import database schema daloRadius
```cd daloradius```
```sudo mysql -u radius -p radius < contrib/db/fr3-mariadb-freeradius.sql```
```sudo mysql -u radius -p radius < contrib/db/mariadb-daloradius.sql```

//-	Pindahkan ke /var/www
```sudo mv ~/daloradius /var/www/```

//-	Masuk ke folder config
```cd /var/www/daloradius/app/common/includes/```
```sudo cp daloradius.conf.php.sample daloradius.conf.php```
```sudo chown www-data:www-data daloradius.conf.php```
```sudo nano daloradius.conf.php```

-	Isi dan pastikan seperti ini
 

//-	Buat folder var & set permission
```cd /var/www/daloradius/
sudo mkdir -p var/{log,backup}
sudo chown -R www-data:www-data var

//9.	Konfigurasi Apache
//-	Buat port
```sudo tee /etc/apache2/ports.conf<<EOF
Listen 80
Listen 8000

<IfModule ssl_module>
    Listen 443
</IfModule>

<IfModule mod_gnutls.c>
    Listen 443
</IfModule>
EOF```

//**Buat virtual host untuk operator (port 8000)***
```sudo tee /etc/apache2/sites-available/operators.conf<<EOF
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
EOF```

//-	Buat virtual host untuk user (port 80)
```sudo tee /etc/apache2/sites-available/users.conf<<EOF
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
EOF```

//10.	**Aktifkan Site & Restart Apache** 
```sudo a2ensite users.conf operators.conf```
```sudo mkdir -p /var/log/apache2/daloradius/{operators,users}```
```sudo a2dissite 000-default.conf```
```systemctl reload apache2```
```sudo systemctl restart apache2 freeradius```

//11.	Tes Akses Web
//Buka browser dan akses:
-	Panel operator/admin → http://<IP_SERVER>:8000/
-	Portal user → http://<IP_SERVER>/

Tampilan operator/admin akan seperti :
 
Isi dengan akun defaultnya :
-	Username: administrator
-	Password: radius
 
