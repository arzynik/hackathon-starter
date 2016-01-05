<?php

namespace App;

class Auth extends \Tipsy\Resource {
	public function __construct($id = null) {
		$this->idVar('id')->table('auth')->load($id);
	}
}
