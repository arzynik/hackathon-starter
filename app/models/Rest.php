<?php

namespace App;

class Rest extends \Tipsy\Controller {
	public function init() {
		header('Content-Type: application/json');
		parent::init();
	}
}
