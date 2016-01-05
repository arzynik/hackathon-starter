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
				templateUrl: 'templates/login.html',
				auth: false
			})
			.when('/signup', {
				action: 'signup',
				controller: 'Signup',
				templateUrl: 'templates/signup.html',
				auth: false
			})
			.when('/account', {
				action: 'account',
				controller: 'Account',
				templateUrl: 'templates/account.html',
				auth: true
			})
			.when('/forgot', {
				action: 'forgot',
				controller: 'Forgot',
				templateUrl: 'templates/forgot.html',
				auth: false
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
	})

	.service('Auth', function($resource, User) {
		var res = $resource('/api/', {}, {
			auth: { url: '/api/login', 'method': 'POST', params : {}}
		});

		return {
			user: function() {
				return User.get();
			},
			login: function(email, password, cb) {
				res.auth({email: email, password: password}, cb);
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

	.controller('Login', function ($rootScope, $scope, Auth) {
		$rootScope.title('Login');
		$scope.login = function() {
			Auth.login($scope.email, $scope.password, function(res) {
				alert('asd');
				if (res.id) {

				}
			});
		}
	})

	.controller('Account', function ($rootScope, $scope, $location, User) {
		$rootScope.title('Account');
		$scope.message = {};
		$scope.save = function() {
			User.save($rootScope.user, function(user) {
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
				$rootScope.user = null;
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
