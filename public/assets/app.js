angular.module('App', ['ngRoute', 'ngResource'])
	.config(function($routeProvider, $locationProvider){
		$routeProvider
			.when('/', {
				action: 'home',
				controller: 'Home',
				templateUrl: 'templates/home.html'
			})
			.when('/contact', {
				action: 'contact',
				controller: 'Contact',
				templateUrl: 'templates/contact.html'
			})
			.when('/login', {
				action: 'login',
				controller: 'Login',
				templateUrl: 'templates/login.html'
			})
			.when('/signup', {
				action: 'signup',
				controller: 'Signup',
				templateUrl: 'templates/signup.html'
			})
			.when('/account', {
				action: 'account',
				controller: 'Account',
				templateUrl: 'templates/account.html'
			})
			.when('/forgot', {
				action: 'forgot',
				controller: 'Forgot',
				templateUrl: 'templates/forgot.html'
			})
			.otherwise({
				redirectTo: '/'
			});

		$locationProvider.html5Mode({
			enabled: true,
			requireBase: false
		});
	})

	.run(function($rootScope, $location, $route) {
		$rootScope.$on('$routeChangeSuccess', function ($event, $currentRoute, $previousRoute) {
			$rootScope.route = $route.current.action;
		});
		$rootScope.title = function(title) {
			document.title = title ? title + ' | ' + 'Hackathon Starter' : 'Hackathon Starter';
		};
	})

	.service('UploadService', function($resource, $routeParams, $location, $rootScope) {

		var up = $resource('/upload', {}, {
			'upload': { 'method': 'POST', params : { 'action' : 'upload' }}
		});

		var file = $resource('/get/:id', {id: '@id'});

		this.upload = function(d) {
			var max = 20000000;

			if (d.data.length > max) {
				$rootScope.$broadcast('upload-error', 'File too big');
			} else {
				up.upload({}, d, function(f) {
					console.log(f);
					if (f.uid) {
						$rootScope.$broadcast('uploaded', f);
					} else {
						$rootScope.$broadcast('upload-error', f);
					}
				}, function() {
					$rootScope.$broadcast('upload-error', '500');
				});
			}
		}

		this.get = function(id, callback) {
			var f = file.get({id: id}, function() {
				if (f.uid) {
					callback(f);
				} else {
					$rootScope.$broadcast('file-error');
				}
			}, function() {
				$rootScope.$broadcast('file-error');
			});
		}

		this.uploadFile = function(file) {

			if (file.type) {
				var type = file.type.split('/');

				if (type[0] != 'text' && type[0] != 'image') {
					$rootScope.$broadcast('upload-error', 'Unsupported file type');
					return;
				}
			}

			var paste = {};
			var self = this;

			var fileReader = new FileReader();

			fileReader.onloadend = function(e) {
				paste.type = file.type;
				paste.data = this.result;
				self.upload(paste);
			};

			fileReader.onerror = function(e) {
				$rootScope.$broadcast('upload-error', 'Could not read file');
			};

			fileReader.readAsDataURL(file);
		}
	})

	.controller('Home', function ($rootScope) {
		$rootScope.title();
	})

	.controller('Contact', function ($rootScope) {
		$rootScope.title('Contact');
	});
