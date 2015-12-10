<?php

namespace App\Controller;

class Error extends \Tipsy\Controller {
	public function init() {
		$this->tipsy()->view()->display('error');
	}
}
