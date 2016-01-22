<?php

namespace App;

class Config extends \Tipsy\Resource {
	public function __construct($id = null) {
		$this->idVar('id')->table('config')->load($id);
	}

	public static function all() {
		return \Tipsy\Tipsy::db()->get('select * from config');
	}

	public static function read($key) {
		if (!$key) {
			return false;
		}
		return self::q('select * from config where `key`=? limit 1', [$key])->get(0);
	}

	public static function write($key, $value = null) {
		if (!$key) {
			return false;
		}

		$config = self::read($key);
		if ($value === null && $config->id) {
			$config->delete();
			return;
		}

		if (!$config) {
			$config = new Config;
		}

		$config->key = $key;
		$config->value = $value;
		$config->save();

		return $config;
	}
}
