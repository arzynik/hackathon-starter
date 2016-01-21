<?php

// a cascading config grabber

namespace App;

class Cfgr extends \Tipsy\Resource {
	protected $_config;
	public function run() {
		foreach (Config::all() as $config) {
			$this->_config[$config->key] =$config->value;
		}
	}
	public function get($id = null) {
		// checks the database for a value
		//$c = Tipsy::db()->
	}
	public function set() {
		// sets the config in the database
	}
}
