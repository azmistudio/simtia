# SIMTIA

<p align="center"><a href="https://simtia.org" target="_blank"><img src="https://simtia.org/images/welcome.png" alt="" width="100%" height="100%"></a></p>

## Tentang SIMTIA

<b>[SIMTIA](https://simtia.org) (Sistem Informasi Ma'had Tahfidz dan Ilmu Al Qur'an)</b>, merupakan aplikasi berbasis web yang dibuat untuk mengelola kegiatan dan administrasi di Lembaga Tahfidz Al Qur'an, seperti Pondok/Ma'had Tahfidz dan Rumah Qur'an.

[SIMTIA](https://simtia.org) terinspirasi dari aplikasi <a href="http://www.jibas.net" target="_blank">JIBAS</a>, dan dibangun dengan dukungan teknologi:

-   PHP Framework: <a href="https://laravel.com" target="_blank">Laravel v9.x</a>
-   Database: <a href="https://www.enterprisedb.com/downloads/postgres-postgresql-downloads" target="_blank">PostgreSQL v10.x</a>
-   User Interface: <a href="https://www.jeasyui.com/" target="_blank">jQuery EasyUI</a>

Library - library pendukung:

-   Ekspor ke Excel & Word: <a href="https://github.com/PHPOffice" target="_blank">PHP Office</a>
-   Ekspor ke PDF: <a href="https://wkhtmltopdf.org/" target="_blank">wkhtmltopdf</a>
-   Laravel Modules: <a href="https://nwidart.com/laravel-modules/v6/introduction" target="_blank">nwidart</a>
-   Laravel Permission: <a href="https://spatie.be/docs/laravel-permission/v5/introduction" target="_blank">SPATIE</a>


Demo aplikasi dapat dilihat <a href="https://demo.simtia.org" target="_blank">disini.</a>

Video tutorial penggunaan aplikasi: <a href="https://www.youtube.com/channel/UCdr9YsgZ_RHNcqkZW0yqR5w/videos" target="_blank">Link Video</a>

## Install Aplikasi

### &raquo; Sistem Operasi Windows

-   Unduh installer untuk Windows 7,8,10,11 <a href="https://drive.google.com/file/d/1RHBpFq8EhACZAjZc84WeioOI87GzHz6c/view?usp=sharing" target="_blank">disini.</a>
-   Akses aplikasi di http://localhost:8080
-   Login Admin:
    -   Email: admin@simtia.org
    -   Password: 123456

### &raquo; Sistem Operasi Linux

Pra instalasi aplikasi:

-   Install web server Apache v2.x
-   Install database PostgreSQL v10.x
-   Install php v8.x
-   Install wkhtmltopdf
-   Install git

Instalasi aplikasi:

-   Clone aplikasi di folder <b>/var/www/html</b>,
    ```
    git clone https://github.com/azmistudio/simtia.git
    ```
-   Buat file .env dari .env.example
    ```
    mv .env.example .env
    ```
-   Ubah konfigurasi file .env

    -   Database postgresql, sesuai dengan konfigurasi saat instalasi database PostgreSQL:
        ```
        DB_CONNECTION=pgsql
        DB_HOST=127.0.0.1
        DB_PORT=5435 <-- sesuaikan dengan konfigurasi saat install
        DB_DATABASE=db_master_azmi_app <-- sesuaikan dengan konfigurasi saat install
        DB_USERNAME=postgres <-- sesuaikan dengan konfigurasi saat install
        DB_PASSWORD=123456 <-- sesuaikan dengan konfigurasi saat install
        ```
    -   Path aplikasi wkhtmltopdf:
        ```
        WKHTML_PATH="/usr/local/bin/wkhtmltopdf"
        ```

-   Ubah hak akses folder storage:
    ```
    sudo chmod -R 777 /var/www/html/simtia/storage
    ```

-   Jalankan perintah - perintah berikut di dalam folder <b>/var/www/html/simtia</b>:
    ```
    php artisan key:generate
    php artisan migrate --force
    php artisan db:seed --class=DatabaseSeeder --force
    php artisan db:seed --class=ReferenceSeeder --force
    php artisan db:seed --class=EmployeeSeeder --force
    php artisan db:seed --class=UserSeeder --force
    php artisan db:seed --class=PermissionSeeder --force
    php artisan storage:link
    ```
-   Konfigurasi web server apache (apache.conf/httpd.conf), untuk dapat diakses melalui localhost.

## Lisensi

Aplikasi SIMTIA merupakan <i>open-sourced software</i> yang memiliki lisensi <a href="https://opensource.org/licenses/MIT" target="_blank">MIT license</a>.
