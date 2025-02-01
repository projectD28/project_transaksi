# project_transaksi

1. git clone repository
2. Import database nama file nya 'database_transaksi.sql'
3. Copy ".env.example" menjadi ".env"
4. Ubah didalam ".env" yaitu koneksi database sebagai berikut
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=project_transaksi //sesuaikan nama database
   DB_USERNAME=root // jika mysql username nya root
   DB_PASSWORD= // jika mysql tidak ada password

5. jalankan perintah diterminal "composer install"
6. jalankan perintah "php artisan key:generate"
7. setelah untuk menjalankan program menggunakan perintah "php artisan serve"
