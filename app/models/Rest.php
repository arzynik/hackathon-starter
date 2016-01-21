<?php

namespace App;

class Rest extends \Tipsy\Controller {
	public function init($params = null) {
		header('Content-Type: application/json');
		parent::init();
	}
}
