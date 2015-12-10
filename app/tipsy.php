<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tipsy\Tipsy;

Tipsy::config('../config/*');
//Tipsy::config(['db' => ['url' => 'mongodb://heroku_c28b9nv8:i2j6pvgoh8b9kgqil1norb427i@ds063124.mongolab.com:63124/heroku_c28b9nv8']]);
//Tipsy::config(['db' => ['url' => 'mongodb://127.0.0.1']]);
/*
if (getenv('CLEARDB_DATABASE_URL')) {
	$tipsy->config(['db' => ['url' => getenv('CLEARDB_DATABASE_URL')]]);
}
CLEARDB_DATABASE_URL
DATABASE_URL
MONGOLAB_URI
REDIS_URL
*/


Tipsy::router()
	->when('api/user/:id', '\App\Controller\Api\User')
	->when('api/user/:id/edit', '\App\Controller\Api\User\Edit')
	->when('auth/:service', '\App\Controller\Auth');


Tipsy::run();
