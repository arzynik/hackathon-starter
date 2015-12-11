<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tipsy\Tipsy;

Tipsy::config('../config/*.ini');
Tipsy::config('../config/*.yml');
Tipsy::config(['path' => __DIR__ . '/../']);

/*
if (getenv('CLEARDB_DATABASE_URL')) {
	$tipsy->config(['db' => ['url' => getenv('CLEARDB_DATABASE_URL')]]);
}
CLEARDB_DATABASE_URL
DATABASE_URL
MONGOLAB_URI
REDIS_URL
*/

//die($_SERVER['REQUEST_URI']);
//die($_REQUEST['__url']);

Tipsy::router()
	->when('api/user/:id', '\App\Controller\Api\User')
	->when('api/user/:id/edit', '\App\Controller\Api\User\Edit')
	->when('auth/:service', '\App\Controller\Auth')
	->when('scss/assets/:file', '\App\Controller\Scss');


Tipsy::run();
