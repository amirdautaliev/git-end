<?


require "../vendor/autoload.php";
use Delight\Auth\Auth;
use League\Plates\Engine;
$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions([
	Engine::class=>function()
	{
		return new Engine('../app/views');
	},
	PDO::class=>function(){
$driver = "mysql";
$host ="localhost";
$databasename='graduation';
$username = "mysql";
$password = "mysql";
return new PDO("$driver:host=$host;dbname=$databasename",$username,$password);
},	
Auth::class => function($container)
{
return new Auth($container->get('PDO'));
}	
]);
$container = $containerBuilder->build();
$dispatcher = \FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
	$r->addRoute('GET', '/page_register', ["app\controllers\HomeController","views_register"]);
	$r->addRoute('POST', '/page_register', ["app\controllers\HomeController","page_register"]);
	$r->addRoute('GET', '/page_login', ["app\controllers\HomeController","views_login"]);
	$r->addRoute('POST', '/page_login', ["app\controllers\HomeController","page_login"]);
	$r->addRoute('GET', '/users', ["app\controllers\HomeController","views_users"]);
	$r->addRoute('GET', '/logout', ['app\controllers\HomeController', 'logout']); 
	$r->addRoute('GET', '/create_user', ['app\controllers\HomeController', 'views_create']);
	$r->addRoute('POST', '/create_user', ["app\controllers\HomeController","create_user"]);
	$r->addRoute('GET', '/edit/{id}', ['app\controllers\HomeController', 'views_edit']);
	$r->addRoute('POST', '/edit/{id}', ['app\controllers\HomeController', 'update_information']);
	$r->addRoute('GET', '/security/{id}', ['app\controllers\HomeController', 'views_security']);
	$r->addRoute('POST', '/security/{id}', ['app\controllers\HomeController', 'update_security']);
	$r->addRoute('GET', '/status/{id}', ['app\controllers\HomeController', 'views_status']);
	$r->addRoute('POST', '/status/{id}', ['app\controllers\HomeController', 'set_status']);
	$r->addRoute('GET', '/media/{id}', ['app\controllers\HomeController', 'views_media']);
	$r->addRoute('POST', '/media/{id}', ['app\controllers\HomeController', 'avatar_upload']);
	$r->addRoute('GET', '/delete_user/{id}', ['app\controllers\HomeController', 'delete_user']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
	$uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
	case FastRoute\Dispatcher::NOT_FOUND:
		 // ... 404 Not Found
		 break;
	case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
		 $allowedMethods = $routeInfo[1];
		 // ... 405 Method Not Allowed
		 break;
	case FastRoute\Dispatcher::FOUND:
		 $handler = $routeInfo[1];
		 $vars = $routeInfo[2];
		//  d($routeInfo[2]);die;
		$container->call($routeInfo[1],$routeInfo[2]);

		//  ... call $handler with $vars
		 break;
}


?>