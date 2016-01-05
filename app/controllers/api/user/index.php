<?php

namespace App\Controller\Api;

class User extends \App\Rest {
	public function init($args = null) {
		$this->inject(function($Request) {

			if ($Request->method() == 'POST' && $Request->loc(2)) {
				http_response_code(403);
				exit;
			}

			$u = $Request->loc(2) ? $Request->loc(2) : $_SESSION['user'];

			$user = new \App\User($u);
			if ($user->dbId()) {
				if ($Request->method() == 'POST') {
					$props = $Request->request();
					unset($props['id']);
					$user->serialize($props);
					$user->save();
				}
				echo $user->json();
			} else {
				http_response_code(404);
			}
		});
	}
}
