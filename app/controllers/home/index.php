<?php

namespace App\Controller;

class Home extends \Tipsy\Controller {
	public function init() {
		$templates = null;
		if ($this->tipsy()->config()['view']['bundle']) {

		}
		$this->tipsy()->view()->display('default', [
			templates => $templates,
			theme => $this->tipsy()->config()['bootswatch']['theme']
		]);
	}
}
