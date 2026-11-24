<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$router = app('router');
$routes = [
    'setoran.iqro.edit',
    'setoran.quran.edit',
    'hafalan.doa.edit',
    'hafalan.hadist.edit',
    'hafalan.surat.edit',
    'baca-buku.edit',
    'katalog.alfabet-latin.edit',
    'katalog.iqro.edit',
    'katalog.surat.edit',
    'katalog.doa.edit',
    'katalog.hadist.edit',
    'katalog.alfabet.edit',
    'master.guru.edit',
    'master.kelas.edit',
    'master.siswa.edit',
    'spp.edit',
];

foreach ($routes as $name) {
    $route = $router->getRoutes()->getByName($name);
    if ($route) {
        echo str_pad($name, 30) . " | URI: " . str_pad($route->uri(), 40) . " | Params: " . implode(',', $route->parameterNames()) . PHP_EOL;
    } else {
        echo str_pad($name, 30) . " | NOT FOUND" . PHP_EOL;
    }
}
