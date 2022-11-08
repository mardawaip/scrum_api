# Metronic - Bootstrap 5 HTML, React, Angular, VueJS & Laravel Admin Dashboard Theme

- For a quick start please check [Online documentation page](//preview.keenthemes.com/metronic8/laravel/documentation/getting-started/build)


- All demos assets are included in the package. To switch the demo please check [Switch demo documentation](//https://preview.keenthemes.com/metronic8/laravel/documentation/getting-started/multi-demo/build)


- The offline documentation is available within the theme [Offline documentation page](//localhost:8000/documentation/getting-started/build)


- For any theme related questions please contact our [Theme Support](//keenthemes.com/support/)


- Using Metronic in a new project or for a new client? Purchase a new license https://1.envato.market/EA4JP or watch https://youtu.be/HJ3RNhoI24A to find out more information about licenses.


- Stay tuned for updates via [Twitter](//www.twitter.com/keenthemes) and [Instagram](//www.instagram.com/keenthemes) and 
  check our marketplace for more amazing products: [Keenthemes Marketplace](//keenthemes.com/)


Happy coding with Metronic!



### Laravel Quick Start

1. Download the latest theme source from the Marketplace.


2. Download and install `Node.js` from Nodejs. The suggested version to install is `14.16.x LTS`.


3. Start a command prompt window or terminal and change directory to [unpacked path]:


4. Install the latest `NPM`:
   
        npm install --global npm@latest


5. To install `Composer` globally, download the installer from https://getcomposer.org/download/ Verify that Composer in successfully installed, and version of installed Composer will appear:
   
        composer --version


6. Install `Composer` dependencies.
   
        composer install


7. Install `NPM` dependencies.
   
        npm install


8. The below command will compile all the assets(sass, js, media) to public folder:
   
        npm run dev


9. Copy `.env.example` file and create duplicate. Use `cp` command for Linux or Max user.

        cp .env.example .env

    If you are using `Windows`, use `copy` instead of `cp`.
   
        copy .env.example .env
   

10. Create a table in MySQL database and fill the database details `DB_DATABASE` in `.env` file.


12. The below command will create tables into database using Laravel migration and seeder.

        php artisan migrate:fresh --seed


13. Generate your application encryption key:

        php artisan key:generate


14. Start the localhost server:
    
        php artisan serve


Langkah-langkah menginstall JWT auth
1. instal laravel dengan link
        
        https://drive.google.com/file/d/1nmqGa4-9daENGXC2P4WNAQVUCN0wWvyk/view
2. Buat file `.env`
3. Buka gitbash di folder sebelumnya, lalu instal komposer dengan kode
        
        composer install
4. Selanjutnya instal jwt dengan kode
        
        composer require php-open-source-saver/jwt-auth
5. Selanjutnya publis vendor dari package jwt nya dengan kode
        
        php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
6. Sekarang supaya menghasilkan token, masukan kode 
        
        php artisan jwt:secret
Dengan kode itu, akan memunculkan kunci jwt_secret di file `.env`

7. Install NPM terbaru dengan kode
        
        npm install --global npm@latest
8. Install NPM dependencies
        
        npm install
9. Create a table in MySQL database and fill the database details DB_DATABASE in `.env` file.
10. Memigrasi tabel yang sudah di buat dengan kode 
        
        php artisan migrate:fresh --seed
11. Hasilkan kunci enkrip aplikasi dengan kode 
        
        php artisan key:generate
12. Di file config/auth.php, tambahkan kode di bawah ini
       
        'defaults' => [
            'guard' => 'api',
            'passwords' => 'users',
        ],

        'guards' => [
            'web' => [
                'driver' => 'session',
                'provider' => 'users',
            ],

            'api' => [
                'driver' => 'jwt',
                'provider' => 'users',
            ],
        ],
13. Buka file app/Models/User.php tambahkan kode di bawah ini
        
        use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
        
        class User extends Authenticatable implements JWTSubject
        {
        
        .............................................
        
            public function getJWTIdentifier()
            {
                return $this->getKey();
            }

            public function getJWTCustomClaims()
            {
                return [];
            }
        }
12. Mulai lokalhost server dengan kode
    
        php artisan serve
13. Masukan kode di bawah untuk bisa memfungsikan upload image
        
        php artisan storage:link
 









