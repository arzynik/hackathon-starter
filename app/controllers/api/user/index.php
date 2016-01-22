<?php

namespace App\Controller\Api;

class User extends \App\Rest {
	public function init($args = null) {
		$this->inject(function($Request) {

			if ($Request->method() == 'POST' && $Request->loc(2)) {
				return http_response_code(403);
			}

			$u = $Request->loc(2) ? $Request->loc(2) : $_SESSION['user'];
			$user = new \App\User($u);

			if ($Request->method() == 'DELETE') {
				if ($Request->loc(2)) {
					return http_response_code(403);
				}
				$user->delete();
				echo json_encode(['status' => 'success']);
				return;
			}

			if ($user->dbId()) {
				if ($Request->method() == 'POST') {
					$props = $Request->request();
					unset($props['id']);
					$user->serialize($props);
					try {
						$user->save();
					} catch (\Exception $e) {
						echo json_encode(['status' => false, 'error' => 'Email already exists']);
						return;
					}
				}
				echo $user->json();
			} else {
				http_response_code(404);
			}
		});
	}
}
