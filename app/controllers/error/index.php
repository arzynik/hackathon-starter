<?php

namespace App\Controller;

class Home extends \Tipsy\Controller {
	public function init() {
		$this->tipsy()->view()->display('error');
	}
}
