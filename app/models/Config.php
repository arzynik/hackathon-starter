<?php

namespace App;

class Config extends \Tipsy\Resource {
	public function __construct($id = null) {
		$this->idVar('id')->table('config')->load($id);
	}
	public function all() {
		$config  = $this->get('select * from config');
		print_r($config);
	}
}
