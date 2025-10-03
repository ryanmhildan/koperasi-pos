#!/bin/bash
# Script untuk memperbaiki permission seluruh project Laravel
# Simpan di root project Laravel kamu

# Ganti path ini sesuai lokasi project
PROJECT_DIR="/var/www/koperasi-pos"

echo "🔧 Memperbaiki permission untuk Laravel di $PROJECT_DIR ..."

# Masuk ke project
cd $PROJECT_DIR || { echo "❌ Folder project tidak ditemukan!"; exit 1; }

# Ubah owner semua file & folder ke user aktif + group webserver (www-data)
sudo chown -R $USER:www-data $PROJECT_DIR

# Atur permission untuk semua folder = 775 (baca/tulis/eksekusi)
find $PROJECT_DIR -type d -exec chmod 775 {} \;

# Atur permission untuk semua file = 664 (baca/tulis)
find $PROJECT_DIR -type f -exec chmod 664 {} \;

# Khusus folder storage dan bootstrap/cache harus bisa ditulis
chmod -R 775 storage bootstrap/cache

echo "✅ Semua permission Laravel sudah diperbaiki!"
