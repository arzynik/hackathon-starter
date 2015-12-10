<?php

namespace App\Controller;

class Error extends \Tipsy\Controller {
	public function init($args = null) {
		$this->tipsy()->view()->display('error');
	}
}
