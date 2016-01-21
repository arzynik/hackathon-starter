<?php

namespace App\Controller\Api;

class Signup extends \Tipsy\Controller {
	public function init($args = null) {
		$this->inject(function($Request) {
			if ($Request->method() == 'POST' && $Request->email && $Request->password) {
				$user = \App\User::byEmail($Request->email);
				if ($user->id) {
					echo json_encode(['error' => 'Email is already in use']);
					return;
				}
				$user = new \App\User([
					'email' => $Request->email,
					'password' => password_hash($Request->password, PASSWORD_BCRYPT)
				]);
				$user->save();
				$_SESSION['user'] = $user->id;
				echo $user->json();
				return;
			}
			http_response_code(403);
		});
	}
}
