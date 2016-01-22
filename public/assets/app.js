angular.module('App', ['ngRoute', 'ngResource'])
	.config(function($routeProvider, $locationProvider){
		$routeProvider
			.when('/', {
				action: 'home',
				controller: 'Home',
				templateUrl: '/templates/home.html'
			})
			.when('/contact', {
				action: 'contact',
				controller: 'Contact',
				templateUrl: '/templates/contact.html'
			})
			.when('/login', {
				action: 'login',
				controller: 'Login',
				templateUrl: '/templates/login.html',
				auth: false
			})
			.when('/signup', {
				action: 'signup',
				controller: 'Signup',
				templateUrl: '/templates/signup.html',
				auth: false
			})
			.when('/account', {
				action: 'account',
				controller: 'Account',
				templateUrl: '/templates/account.html',
				auth: true
			})
			.when('/forgot-password', {
				action: 'forgot',
				controller: 'Forgot',
				templateUrl: '/templates/forgot.html',
				auth: false
			})
			.when('/reset-password/:link', {
				action: 'reset',
				controller: 'Reset',
				templateUrl: '/templates/reset.html',
				auth: false
			})
			.when('/apis', {
				action: 'apis',
				controller: 'Apis',
				templateUrl: '/templates/apis.html'
			})
			.otherwise({
				redirectTo: '/'
			});

		$locationProvider.html5Mode({
			enabled: true,
			requireBase: false
		});
	})

	.run(function($rootScope, $location, $route, $location, Auth) {
		$rootScope.$on('$routeChangeStart', function ($event, $currentRoute) {
			if ($currentRoute.auth === true && (!$rootScope.user || !$rootScope.user.id)) {
				// needs a promise
				//$location.path('/login');
				//return;
			}

			if ($currentRoute.auth === false && $rootScope.user && $rootScope.user.id) {
				$location.path('/account');
				return;
			}
		});
		$rootScope.$on('$routeChangeSuccess', function ($event, $currentRoute, $previousRoute) {
			$rootScope.route = $route.current.action;
		});
		$rootScope.title = function(title) {
			document.title = title ? title + ' | ' + 'Hackathon Starter' : 'Hackathon Starter';
		};

		// to specify a specific color, change random to something like RdYlBu or RdBu
		// see http://colorbrewer2.org/ and http://qrohlf.com/trianglify/ for more info
		var p = Trianglify({variance: '0.77', seed: null, x_colors: 'random', y_colors: 'match_x', cell_size: 30});
		$rootScope.triangles = p.svg({includeNamespace: true});
		$rootScope.triangles = '<svg style="-webkit-filter: brightness(.5) contrast(200%)" xmlns="http://www.w3.org/2000/svg" width="600" height="400">' + $rootScope.triangles.innerHTML + '</svg>';

		$rootScope.user = Auth.user();
		$rootScope.logout = Auth.logout;
	})

	.service('Auth', function($location, $rootScope, $resource, User) {
		var res = $resource('/api/', {}, {
			login: { url: '/api/login', 'method': 'POST', params : {}},
			logout: { url: '/api/logout', params : {}},
			signup: { url: '/api/signup', 'method': 'POST', params : {}},
			forgot: { url: '/api/forgot', 'method': 'POST', params : {}},
			reset: { url: '/api/reset', 'method': 'POST', params : {}}
		});

		return {
			user: function() {
				return User.get();
			},
			resource: res,
			logout: function() {
				res.logout({}, function() {
					$rootScope.user = {};
					$location.path('/');
				});
			}
		};
	})

	.service('User', function($resource) {
		 return $resource('/api/user', {}, {
			passwd: { url: '/api/passwd', 'method': 'POST', params : {}}
		});
	})

	.controller('Home', function ($rootScope) {
		$rootScope.title();
		$('.splash').css('background-image', 'url(\'data:image/svg+xml;utf8,'+$rootScope.triangles+'\')');
	})

	.controller('Contact', function ($rootScope) {
		$rootScope.title('Contact');
	})

	.controller('Login', function ($location, $rootScope, $scope, Auth) {
		$rootScope.title('Login');
		$scope.login = function() {
			$scope.loginError = false;
			Auth.resource.login({email: $scope.email, password: $scope.password}, function(res) {
				$rootScope.user = res;
				$location.path('/account');
			}, function() {
				$scope.loginError = true;
			});
		}
	})

	.controller('Reset', function ($rootScope, $routeParams, $scope, Auth) {
		$rootScope.title('Reset Password');
		$scope.reset = function() {

			if (!$scope.password || $scope.password != $scope.confirm) {
				$scope.resetError = true;
				return;
			}

			$scope.resetError = false;

			Auth.resource.reset({link: $routeParams.link, password: $scope.password}, function(res) {
				$scope.resetComplete = true;
				$rootScope.user = res;
			}, function() {
				$scope.resetError = true;
			});
		}
	})

	.controller('Forgot', function ($rootScope, $scope, Auth) {
		$rootScope.title('Forgot Password');
		$scope.forgot = function() {
			$scope.forgotError = false;
			Auth.resource.forgot({email: $scope.email}, function(res) {
				$scope.forgotComplete = true;
			}, function() {
				$scope.forgotError = true;
			});
		}
	})

	.controller('Signup', function ($location, $rootScope, $scope, Auth) {
		$rootScope.title('Signup');
		$scope.signup = function() {
			if (!$scope.email || !$scope.password || $scope.password != $scope.confirm) {
				$scope.signupError = true;
				return;
			}
			$scope.signupError = false;
			Auth.resource.signup({email: $scope.email, password: $scope.password}, function(res) {
				if (res.error) {
					$scope.signupError = res.error;
					return;
				}
				$rootScope.user = res;
				$location.path('/account');

			}, function() {
				$scope.signupError = true;
			});
		}
	})

	.controller('Account', function ($rootScope, $scope, $location, User) {
		$rootScope.title('Account');

		$scope.message = {};
		$scope.save = function() {
			$scope.message = {};
			User.save($rootScope.user, function(user) {
				if (user.error) {
					$scope.message.profileError = user.error;
					return;
				}

				$rootScope.user = user;
				$scope.message.profile = 1;
			});
		};
		$scope.passwd = function() {
			if (!$scope.pass1 || $scope.pass1 != $scope.pass2) {
				$scope.message.password = 2;
				return;
			}
			User.passwd({pass: $scope.pass1}, function(user) {
				$scope.password = '';
				$scope.message.password = 1;
			});
		};
		$scope.delete = function() {
			User.delete(function(user) {
				$rootScope.user = {};
				// location.href = location.href;
			});
		};
	});


$(window).scroll(function() {
	if ($(document).scrollTop() > 100) {
		$('.navbar').addClass('shrink');
	} else {
		$('.navbar').removeClass('shrink');
	}
});
