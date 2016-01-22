<?php

namespace App\Controller\Api;

use Tipsy\Tipsy;

class Reset extends \Tipsy\Controller {
	public function init($args = null) {
		$this->inject(function($Request, $Mail, $View) {
			if ($Request->method() == 'POST' && $Request->link && $Request->password) {
				$user = \App\User::byReset($Request->link);
				if (!$user->email) {
					http_response_code(403);
					return;
				}

				$user->password = password_hash($Request->password, PASSWORD_BCRYPT);
				$user->reset = null;
				$user->save();
				$_SESSION['user'] = $user->id;

				echo $user->json();
			}
			http_response_code(403);
		});
	}
}
