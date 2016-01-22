<?php

namespace App;

use Tipsy\Tipsy;

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

	public static function byReset($link) {
		$user = self::query('select * from user where reset=? limit 1', [$link])->get(0);
		return $user ? $user : false;
	}

	public function exports() {
		$props = $this->properties();

		// properties viewable by anyone
		$public = ['id', 'name', 'location', 'website', 'image'];

		// properties viewable only by the logged in user
		$private = ['email', 'gender', 'auth'];

		$props['image'] = $props['avatar'] ? $props['avatar'] : 'https://gravatar.com/avatar/'.md5(strtolower(trim($props['email']))).'?s=200';

		$user = Tipsy::middleware('Session')->user();

		if ($user && $user->id == $this->id) {
			$auths = $this->db()->get('
				select auth.* from auth
				where
					user=?
				limit 1
			', [$this->id]);
			foreach ($auths as $auth) {
				$props['auth'][$auth->type] = $auth->value;
			}

			$public = array_merge($public, $private);
		}

		foreach ($props as $key => $prop) {
			if (!in_array($key, $public)) {
				unset($props[$key]);
			}
		}

		return $props;
	}

	public function __construct($id = null) {
		$this->idVar('id')->table('user')->load($id);
	}
}
