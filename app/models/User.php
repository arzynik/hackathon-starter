<?php

namespace App;

class User extends \Tipsy\Resource {
	public static function byAuth($value, $type) {
		$user = self::query('
			select user.* from user
			left join auth on user.id=auth.user
			where
				auth.value=?
				and auth.type=?
			limit 1
		', [$value, $type])->get(0);
		return $user ? $user : false;
	}

	public static function byEmail($email) {
		$user = self::query('select * from user where email=? limit 1', [$email])->get(0);
		return $user ? $user : false;
	}

	public function exports() {
		$props = $this->properties();
		unset($props['password']);
		$props['image'] = 'https://gravatar.com/avatar/'.md5(strtolower(trim($props['email']))).'?s=200';
		return $props;
	}

	public function __construct($id = null) {
		$this->idVar('id')->table('user')->load($id);
	}
}
