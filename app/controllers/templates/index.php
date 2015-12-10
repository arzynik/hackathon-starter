<?php

namespace App\Controller;

class Templates extends \Tipsy\Controller {
	public function init($args = null) {
		$this->inject(function($Request, $View) {
			$View->config(['layout' => 'templates/template']);
			$View->display('templates/'.preg_replace('/\.html$/', '', $Request->loc(1)));
		});
	}
}
