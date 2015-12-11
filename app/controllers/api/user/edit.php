<?php

namespace App\Controller\Api\User;

class Edit extends \App\Rest {
	public function init($args = null) {
		die('edit');
		print_r($this->service('Params'));
	}
}
