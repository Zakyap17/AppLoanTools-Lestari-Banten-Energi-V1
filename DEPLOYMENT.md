# Panduan Deployment Terupdate - System Gudang LBE

Panduan ini berisi langkah-langkah untuk melakukan deployment aplikasi Laravel ini ke server produksi (Ubuntu) menggunakan Nginx, PHP, dan PostgreSQL.

## 1. Persiapan Server (Ubuntu)

### Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### Install PHP & Extensions (PHP 8.2+)
```bash
sudo apt install -y php8.2-fpm php8.2-cli php8.2-pgsql php8.2-curl php8.2-xml php8.2-mbstring php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl
```

### Install PostgreSQL
```bash
sudo apt install -y postgresql postgresql-contrib
```

### Install Nginx
```bash
sudo apt install -y nginx
```

### Install Composer & Node.js
```bash
# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js & NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## 2. Konfigurasi Database (PostgreSQL)

1. Masuk ke prompt PostgreSQL:
   ```bash
   sudo -u postgres psql
   ```
2. Buat database dan user:
   ```sql
   CREATE DATABASE warehouse_db;
   CREATE USER warehouse_user WITH PASSWORD 'password_anda';
   GRANT ALL PRIVILEGES ON DATABASE warehouse_db TO warehouse_user;
   \q
   ```

---

## 3. Cloning & Setup Aplikasi

1. Clone repositori ke `/var/www/html/`:
   ```bash
   cd /var/www/html
   sudo git clone [URL_REPOSITORI] warehouse-app
   cd warehouse-app
   ```

2. Instalasi Dependensi:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   ```

3. Konfigurasi `.env`:
   ```bash
   cp .env.example .env
   nano .env
   ```
   *Sesuaikan pengaturan DB_DATABASE, DB_USERNAME, DB_PASSWORD, dan APP_URL.*

4. Generate App Key & Jalankan Migrasi:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

---

## 4. Perizinan File (Permissions)

Penting agar Nginx dapat membaca dan menulis ke folder yang diperlukan:
```bash
sudo chown -R www-data:www-data /var/www/html/warehouse-app
sudo chmod -R 775 /var/www/html/warehouse-app/storage
sudo chmod -R 775 /var/www/html/warehouse-app/bootstrap/cache
```

---

## 5. Konfigurasi Nginx

Buat file konfigurasi server block:
```bash
sudo nano /etc/nginx/sites-available/warehouse-app
```

Isi dengan konfigurasi berikut:
```nginx
server {
    listen 80;
    server_name [ALAMAT_IP_ATAU_DOMAIN];
    root /var/www/html/warehouse-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan konfigurasi:
```bash
sudo ln -s /etc/nginx/sites-available/warehouse-app /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 6. Optimasi Produksi

Jalankan perintah berikut untuk meningkatkan performa:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7. Penjadwalan (Cron Job)

Tambahkan scheduler ke crontab server:
```bash
crontab -e
```
Tambahkan baris berikut:
```bash
* * * * * cd /var/www/html/warehouse-app && php artisan schedule:run >> /dev/null 2>&1
```

---

## 8. Testing di Oracle VirtualBox (On-Premise)

Jika Anda melakukan deployment di VirtualBox agar sistem bisa diakses dari laptop Host (Windows/macOS), ikuti langkah detail berikut:

### A. Pengaturan Network di VirtualBox
1. Matikan VM Ubuntu Anda.
2. Buka **Settings** VM > **Network**.
3. Pada **Adapter 1**, ubah **Attached to** menjadi **Bridged Adapter**.
4. Pilih **Name** sesuai network interface yang Anda gunakan (misal: Wi-Fi atau Ethernet).
5. Klik OK dan jalankan kembali VM.

### B. Konfigurasi Static IP (Ubuntu Server)
Agar IP server tidak berubah-ubah, kita perlu mengaturnya secara statis.

1. Cek nama interface network Anda:
   ```bash
   ip add
   ```
   *(Biasanya bernama `enp0s3` atau `eth0`)*.

2. Edit file netplan (nama file mungkin berbeda, cek di `/etc/netplan/`):
   ```bash
   sudo nano /etc/netplan/00-installer-config.yaml
   ```

3. Ubah isinya menjadi seperti ini (Sesuaikan dengan subnet network Anda):
   ```yaml
   network:
     version: 2
     renderer: networkd
     ethernets:
       enp0s3: # Ubah sesuai nama interface Anda
         dhcp4: no
         addresses:
           - 192.168.1.100/24 # IP statis yang diinginkan
         gateway4: 192.168.1.1 # IP Router/Gateway Anda
         nameservers:
           addresses: [8.8.8.8, 8.8.4.4]
   ```

4. Terapkan konfigurasi:
   ```bash
   sudo netplan apply
   ```

### C. Menghubungkan Aplikasi dengan IP Statis
Setelah mendapatkan IP statis (misal: `192.168.1.100`), lakukan langkah terakhir:

1. **Update .env**:
   ```bash
   nano /var/www/html/warehouse-app/.env
   ```
   Ubah `APP_URL=http://192.168.1.100`

2. **Update Nginx**:
   ```bash
   sudo nano /etc/nginx/sites-available/warehouse-app
   ```
   Ubah `server_name 192.168.1.100;`

3. **Restart Nginx**:
   ```bash
   sudo systemctl restart nginx
   ```

### D. Akses dari Host
Buka browser di laptop Anda (bukan di dalam VM) dan ketikkan IP tersebut:
`http://192.168.1.100`

Selesai! Sistem sekarang dapat diakses secara lokal.

