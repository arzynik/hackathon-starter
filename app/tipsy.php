<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tipsy\Tipsy;

Tipsy::config('../config/*.ini');
Tipsy::config('../config/*.yml');
Tipsy::config(['path' => __DIR__ . '/../']);

if (getenv('DATABASE_URL')) {
	Tipsy::config(['db' => ['url' => getenv('DATABASE_URL')]]);
	// CLEARDB_DATABASE_URL
}


// define routes here for anything that uses route params
Tipsy::router()
	->when('api/user/:id', '\App\Controller\Api\User')
	->when('auth/:service', '\App\Controller\Auth')
	->when('/\.scss$/i', '\App\Controller\Scss');


// initilize config from database, config files, and env variables
Tipsy::service('cfgr', '\App\Cfgr');
Tipsy::service('Mail', '\App\Mail');
Tipsy::service('User', '\App\User');

//echo Tipsy::service('cfgr')->get('auth-facebook-key');
//echo Tipsy::service('cfgr')->set('auth-facebook-key', 'test');

// simple session management using redis
Tipsy::middleware('Session', [
	'run' => function() {
		$redis = getenv('REDIS_URL');

		if ($redis) {
			$client = new \Predis\Client($redis);
			$handler = new App\Session($client);
			session_set_save_handler($handler);
		}

		session_start();
	},
	'user' => function() {
		return $_SESSION['user'] ? $this->tipsy()->service('User')->load($_SESSION['user']) : null;
	}
]);


// pointless headers
header('X-Powered-By: PHP/'.phpversion().'; arzynik/hackathon-starter');

Tipsy::run();
