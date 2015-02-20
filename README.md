# B-Comp

B-Comp adalah komponen [Bono](https://github.com/xinix-technology/bono) yang biasa sering saya gunakan di tiap project. Modul ini dibuat karena Bono tidak memiliki beberapa fitur, dan saya membutuhkannya di sebuah project. Untuk membantu teman-teman agar lebih cepat membangun aplikasi, saya ciptakan modul componen ini. Secara offical, [Viper Arch](https://github.com/krisanalfa/viper-arch) menggunakan BComp sebagai steroid aplikasi. Secara garis besar (sementara), B-Comp bisa menambahkan fitur:

- Logging
- Simple Versioning System
- Helper

## Instalasi

Cukup tambahkan di file `composer.json` kamu seperti berikut:

```
"require": {
    "krisanalfa/b-comp": "dev-master"
}
```

Lalu jalankan, `composer update`. Atau bisa juga melalui command `composer require krisanalfa/b-comp dev-master`.

### Logging
Logging dapat ditambahkan ke Bono dengan menambahkan provider `LogProvider`. Caranya, masukkan `LogProvider` ke dalam konfigurasi `bono.providers` kamu:

```php
'bono.providers' => array(
    'BComp\\Provider\\LogProvider' => array(
        'log.name' => 'My Awesome App',
    ),
),
```

#### Konfigurasi
Secara garis besar, berikut konfigurasi yang dapat diubah.

##### `log.name`
Nama aplikasi kamu sebagai penanda channel log. Default `APP LOGGER`.

##### `log.path`
Path dimana kamu menyimpan log aplikasi kamu. Default `logs`. Jika folder `logs` belum ada, maka Provider akan otomatis membuatkannya untuk kamu. Lokasinya relatif terhadap base path aplikasi Bono kamu.

##### `log.dateformat`
Format tanggal event log di dalam log file kamu. Default `Y-m-d H:i:s`.

##### `log.fileformat`
Format log file kamu. Default `Y-m-d`.

##### `log.outputformat`
Format output log. Default:

```
[%datetime%] - [%level_name% ON %channel%] - [%message%]
    [MESSAGE CONTEXT]        %context%
    [ADDITIONAL INFORMATION] %extra%\n
```

- `%datetime%` adalah tanggal / waktu event log ditulis.
- `%level_name%` adalah level log event (EMERGENCY|ALERT|CRITICAL|ERROR|WARN|NOTICE|INFO|DEBUG).
- `%message%` adalah pesan log ketika ditulis.
- `%context%` adalah konteks yang dituliskan di dalam event log, nilainya bisa array yang otomatis akan dikonversi ke dalam JSON.
- `%extra%` adalah informasi ekstra yang ditulis di dalam event log.

#### Cara Menggunakan

Setelah menambahkan provider, maka kita bisa mengakses `log` dari `Slim Container`.

```php
$log = App::getInstance()->log;

// Usage $log->{logLevel}($message, array $context = array());

$log->emergency('Foo', ['foo' => 'bar']);
$log->alert('Foo', ['foo' => 'bar']);
$log->critical('Foo', ['foo' => 'bar']);
$log->error('Foo', ['foo' => 'bar']);
$log->warn('Foo', ['foo' => 'bar']);
$log->notice('Foo', ['foo' => 'bar']);
$log->info('Foo', ['foo' => 'bar']);
$log->debug('Foo', ['foo' => 'bar']);
```

### Simple Versioning System

Simple Versioning System akan memudahkan kamu untuk memisahkan konfigurasi antar mesin, selain itu dia juga bisa mengatur konfigurasi yang mana yang digunakan di production dan development. Sebenarnya Bono memiliki kemampuan ini, namun bagi saya masih cukup sulit untuk di-maintain, sehingga saya memberikan cara me-manage konfigurasi yang lebih mudah di-maintain.

#### Instalasi

Tambahkan `VersionProvider` ke dalam konfigurasi `bono.providers` kamu.

```php
'bono.providers' => array(
    'BComp\\Provider\\VersionProvider' => array(
        // Enter the team hostnames computer here
        'local' => array(
            'farid.macbook',
            'ali.macbook',
            'alfa.macbook',
        ),

        // Enter the remote hostnames computer here
        'remote' => array(
            'semut',
            'lebah',
            'cicak',
        ),
    ),
),
```

Pada saat mesin mengetahui bahwa aplikasi berjalan di mesin dengan nama hostname `farid.macbook`, maka provider akan berusaha mencari file konfigurasi bernama `{base_path_bono_app}/config/env/local.php`, karena `farid.macbook` berada dalam list `local`.
Sebaliknya, pada saat mesin mengetahui bahwa aplikasi berjalan di mesin dengan nama `semut`, maka provider akan berusaha mencari file konfigurasi bernama `{base_path_bono_app}/config/env/remote.php`, karena `farid.macbook` berada dalam list `remote`.
Selain itu, jika hostname-nya adalah `alfa.macbook`, provider akan mencari file konfigurasi dengan nama `{base_path_bono_app}/config/host/alfa.macbook.php`.
Untuk mengubah state aplikasi dari `development` menjadi `production`, ubah konfigurasi instansiasi `Bono\App` seperti berikut:

```php
use Bono\App;

// Create bulb application
$app = new App(
    array(
        // Should application autostart after construction?
        'autorun'    => false,

        // The mode of application
        'mode'       => 'development',

        // Enable Slim debug
        'debug'      => true,

        // Enable Bono debug
        'bono.debug' => true,
    )
);

$app->run();
```

Ubah bagian `mode` menjadi `production` jika memang aplikasi sudah siap ke tahap production. Selain itu pastikan `debug` dan `bono.debug` bernilai `false`.
Jika aplikasi dalam mode production, maka provider akan mencari file konfigurasi bernama `{base_path_bono_app}/config/mode/production.php`.
Sebaliknya, jika aplikasi dalam mode development, maka provider akan mencari file konfigurasi bernama `{base_path_bono_app}/config/mode/development.php`.

### Helper
Hanya ada dua helper untuk saat ini, yaitu `Arr` (untuk array helper), dan `Str` (untuk string helper).

#### `Arr`

Berikut adalah snippet penggunaan helper:

```php
use BComp\Helper\Arr as A;

$array = [
    'name' => 'Alfa',
    'sex' => 'Male',
    'age' => 23,
];

A::except($array, ['sex']); // hasilnya ['name' => 'Alfa', 'sex' => 'Male']
A::isEmpty($array); // hasilnya false
A::isEmpty(['name' => '', 'age' => '']); // hasilnya true
A::only($array, ['name']); // hasilnya ['name' => 'Alfa']

$multi = [
    ':type_address'     => 'Foo',
    ':type_citizenship' => 'Bar',
    ':type_city'        => 'Baz',
    ':type_country'     => 'Qux',
];

A::replaceKey($multi, ':type', 'user');
// Hasilnya
// $array = [
//     'user_address'     => 'Foo',
//     'user_citizenship' => 'Bar',
//     'user_city'        => 'Baz',
//     'user_country'     => 'Qux',
// ]

$header = [
    ':type_address',
    ':type_citizenship',
    ':type_city',
    ':type_country',
];

A::replaceValue($header, ':type_', '');

// Hasilnya
// $header = [
//     'address',
//     'citizenship',
//     'city',
//     'country',
// ];

A::depth($multi); // hasilnya 2
A::depth($header); // hasilnya 1
```

Dan masih banyak lagi, untuk lebih jelasnya, lihat source code.

#### `Str`

Berikut adalah snippet penggunaan helper:

```php

use BComp\Helper\Str as S;

S::camel("my_method") // "myMethod"
S::contains('my_method', 'x'); // false
S::contains('my_method', 'd'); // true
S::contains('my_method', ['x', 'd']); // true
S::endsWith('my_method', 'd'); // true
S::endsWith('my_method', 'x'); // false
S::endsWith('my_method', ['x', 'd']); // true
S::is('*.php', 'myFile.php'); // true
S::is('php', 'myFile.php'); // false
S::is('*.php', 'myFile.php'); // true
S::is('php', 'myFile.php'); // false

S::limit("You see me because you haven't overriden templates yet or default routes. May be this is your fist journey through the world of Bono. I wish you will enjoy and get comfy to the world of productive application development.");
// output: "You see me because you haven't overriden templates yet or default routes. May be this is your fist j..."

S::words("You see me because you haven't overriden templates yet or default routes. May be this is your fist journey through the world of Bono. I wish you will enjoy and get comfy to the world of productive application development.", 10));
// output: "You see me because you haven't overriden templates yet or..."

S::parseCallback('Class@methodName', 'defaultMethod'); // ["Class", "methodName"]
S::parseCallback('ClassName', 'defaultMethod'); // ["ClassName", "defaultMethod"]
S::title('this is a title'); // "This Is A Title"
S::slug('mr. ganesha, this is a title'); // "mr-ganesha-this-is-a-title"
S::snake('theCamelCaseVariable'); // "the_camel_case_variable"
S::startsWith('theCamelCaseVariable', 't'); // true
S::startsWith('theCamelCaseVariable', 'z'); // false
S::startsWith('theCamelCaseVariable', ['t', 'z']); // true
S::studly('the_snake_case_class_name'); // "TheSnakeCaseClassName"
```

Dan masih banyak lagi, untuk lebih jelasnya, lihat source code.
