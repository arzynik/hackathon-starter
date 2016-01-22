<?php

namespace App\Controller;

use OAuth\OAuth2\Service\Facebook;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use Tipsy\Tipsy;

class Auth extends \Tipsy\Controller {
	public function init($args = null) {
		$name = $this->tipsy()->request()->loc(1);

		if (!Tipsy::service('cfgr')->get('api-'.$name.'-key') || !Tipsy::service('cfgr')->get('api-'.$name.'-secret')) {
			die('no auth config for '.$name);
		}

		$storage = new Session();
		$credentials = new Credentials(
			Tipsy::service('cfgr')->get('api-'.$name.'-key'),
			Tipsy::service('cfgr')->get('api-'.$name.'-secret'),
			$this->tipsy()->request()->url()
		);

		$serviceFactory = new \OAuth\ServiceFactory();
		$scope = [];
		$email = Tipsy::service('cfgr')->get('apiconfig-email');

		if ($email) {
			switch ($name) {
				case 'facebook':
					$scope = ['public_profile', 'email'];
					break;
				case 'linkedin':
					$scope = ['r_basicprofile', 'r_emailaddress'];
					break;
				case 'github':
					$scope = ['user:email'];
					break;
				case 'google':
					$scope = ['userinfo_email', 'userinfo_profile'];
					break;
				case 'instagram':
					$scope = ['basic'];
					break;
			}
		} else {
			switch ($name) {
				case 'instagram':
					$scope = ['basic'];
					break;
			}
		}

		$service = $serviceFactory->createService($name, $credentials, $storage, $scope);

		$code = $name == 'twitter' ? 'oauth_token' : 'code';


		if (!empty($_GET[$code])) {

			switch ($name) {
				case 'twitter':
					$token = $storage->retrieveAccessToken('Twitter');

					$service->requestAccessToken(
						$_GET['oauth_token'],
						$_GET['oauth_verifier'],
						$token->getRequestTokenSecret()
					);
					break;

				default:
					$state = isset($_GET['state']) ? $_GET['state'] : null;
					$token = $service->requestAccessToken($_GET[$code], $state);
					break;
			}


			switch ($name) {
				case 'facebook':
					$data = json_decode($service->request('/me?fields=name,gender'.($email ? ',email' : '')), true);

					$result = [
						id => $data['id'],
						name => $data['name'],
						email => $data['email'],
						gender => $data['gender']
					];
					break;

				case 'twitter':
					$data = json_decode($service->request('account/verify_credentials.json'), true);
					$result = [
						id => $data['id'],
						name => $data['name']
					];
					break;

				case 'instagram':
					$data = json_decode($service->request('users/self'), true);
					$result = [
						id => $data['data']['id'],
						name => $data['data']['full_name'],
						avatar => $data['data']['profile_picture'],
						website => $data['data']['website']
					];
					break;

				case 'google':
					$data = json_decode($service->request('userinfo'), true);
					$result = [
						id => $data['id'],
						name => $data['name'],
						email => $data['email'],
						gender => $data['gender'],
						avatar => $data['picture']
					];
					break;

				case 'linkedin':
					$emailQ = Tipsy::service('cfgr')->get('apiconfig-email') ? ':(id,firstName,lastName,email-address)' : ':(id,firstName,lastName)';
					$data = json_decode($service->request('/people/~'.$emailQ.'?format=json'), true);
					$result = [
						id => $data['id'],
						name => $data['firstName'].' '.$data['lastName'],
						email => $data['emailAddress']
					];
					break;

				case 'github':
					$data = json_decode($service->request('user'), true);
					$result = [
						id => $data['id'],
						name => $data['name'],
						location => $data['location'],
						website => $data['blog'],
						email => $data['email'],
						avatar => $data['avatar_url']
					];
					if ($email && !$result['email']) {
						$data = json_decode($service->request('user/emails'), true);
						$result['email'] = $data[0];
					}

					break;
			}

			if ($result['id']) {
				$user = \App\User::byAuth($result['id'], $name);
				if (!$user) {
					if (!Tipsy::middleware('Session')->user()) {
						$user = new \App\User;
						foreach ($result as $key => $value) {
							if ($key == 'id') {
								continue;
							}
							$user->{$key} = $value;
						}
						$user->save();
					} else {
						$user = Tipsy::middleware('Session')->user();
					}

					$auth = new \App\Auth([
						'value' => $result['id'],
						'type' => $name,
						'user' => $user->id
					]);
					$auth->save();
				}

				$_SESSION['user'] = $user->id;
				header('Location: /account');
			}

		} else {
			$request = [];
			if ($name == 'twitter') {
				$request = ['oauth_token' => $service->requestRequestToken()->getRequestToken()];
			}

			$url = $service->getAuthorizationUri($request);
			header('Location: ' . $url);
		}
	}
}
