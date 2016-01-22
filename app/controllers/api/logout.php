<?php

namespace App\Controller\Api;

class Logout extends \Tipsy\Controller {
	public function init($args = null) {
		session_destroy();
		echo json_encode(['status' => true]);
	}
}
