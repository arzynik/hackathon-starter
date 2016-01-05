<?php

namespace App\Controller\Api;

class Login extends \Tipsy\Controller {
	public function init($args = null) {
		$this->inject(function($Request) {
			if ($Request->method() == 'POST' && $Request->email && $Request->password) {
				$user = \App\User::byEmail($Request->email);
				if (password_verify($Request->password, $user->password)) {
					echo $user->json();
				} else {
					http_response_code(403);
				}
				return;
			}
			http_response_code(403);
		});
	}
}
