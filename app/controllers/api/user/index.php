<?php

namespace App\Controller\Api;

class User extends \App\Rest {
	public function init() {
		$this->inject(function($Request){
			$user = new \App\User($Request->loc(2));
			if ($user->dbId()) {
				echo $user->json();
			} else {
				http_response_code(404);
			}
		});
	}
}
