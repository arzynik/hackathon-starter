<?php

namespace App;

class User extends \Tipsy\Resource {
	public function __construct($id = null) {
		$this->idVar('id')->table('user')->load($id);
	}
}
