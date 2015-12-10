<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tipsy\Tipsy;

Tipsy::config('../config/*.ini');
Tipsy::config('../config/*.yml');

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
