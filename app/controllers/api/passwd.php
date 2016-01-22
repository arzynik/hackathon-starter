<?php

namespace App\Controller\Api;

class Passwd extends \App\Rest {
	public function init($args = null) {
		$this->inject(function($Request) {
			if ($Request->method() != 'POST') {
				http_response_code(403);
				exit;
			}

			$user = Tipsy::middleware('Session')->user();
			if ($user->dbId()) {
				$user->password = password_hash($Request->pass, PASSWORD_DEFAULT);
				$user->save();
				echo json_encode(['status' => 'Password successfully changed']);
			} else {
				http_response_code(404);
			}
		});
	}
}
