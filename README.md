

## Tes Coding Concatenate.id

- Clone Project Ini.
- lalu cd tes-concatenate.
## no1

- cd no1.
- php main.php.
- inputkan angka yang menyatakan banyak nya inputan lalu tekan enter.
- inputkan angka sebanyak inputan yang diinisiasi sebelumnya (setiap kali selesai satu input tekan enter).
- output akan dimunculkan.
- cd ...

## no2


- cp .env.example .env 
- composer install 
- composer du
- php artisan key:generate
- setup settingan koneksi database di .env dengan variabel sebagai berikut (DB_CONNECTION,DB_HOST,DB_PORT,DB_USERNAME,
  DB_PASSWORD,DB_DATABASE).
- setup settingan rajaongkir di .env dengan variabel sebagai beriku (RAJAONGKIR_KEY, RAJAONGKIR_URL).
- php artisan migrate
- php artisan db:seed
- data master provinsi akan tersimpan di tabel master_provinsi
- data master kota/kabupaten akan tersimpan di tabel master_city, dengan provinsi_id adalah foreign key ke tabel 
  master_provinsi
- data master ekspedisi akan tersimpan di tabel master_ekspedisi
- data ongkir dari Yogyakarta ke seluruh kota/kabupaten akan tersimpan di tabel ongkir dengan ketentuan sebagai
  berikut origin_id dan destination_id merupakan penanda asal pengiriman dan tujuan pengiriman yang juga merupakan foreign key ke tabel master_city, courier_id merupakan foreign key ke tabel master_ekspedisi, weight merupakan berat barang yang dikirim per gramnya (default 1 gram), service merupakan paket pengiriman, description merupakan deskripsi dari paket pengiriman, cost_value merupakan ongkos pengiriman, cost_etd merupakan estimasi durasi pengiriman dalam satuan hari. 

