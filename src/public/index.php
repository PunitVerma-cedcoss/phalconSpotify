<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Mvc\Micro;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;


$config = new Config([]);


// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require BASE_PATH . '/vendor/autoload.php';

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH . '/components',
        'App\Listeners' => APP_PATH . '/listeners'
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

//setting listeners
$eventsManager = new EventsManager();
$eventsManager->attach('application:refreshToken', new App\Listeners\Tokenlistener());
$eventsManager->attach('application:beforeHandleRequest', new App\Listeners\Authlistener());
$container->set(
    'EventsManager',
    $eventsManager
);
//cache container
$container->setShared(
    'cache',
    function () {
        $cache = new \App\Components\CacheComponent();
        $cache = $cache->initCache();
        return $cache;
    }
);


$application = new Application($container);

$application->setEventsManager($eventsManager);
// setting session #️⃣
$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp'
            ]
        );
        $session->setAdapter($files)->start();
        return $session;
    }
);

// variables
$container->set(
    "CLIENT_ID",
    function () {
        return "43d9a244f9394275b5b773e18a999a54";
    }
);
$container->set(
    "STATE",
    function () {
        return "generateRandomString";
    }
);
$container->set(
    "SCOPE",
    function () {
        return "user-read-email user-read-private playlist-read-collaborative playlist-modify-public playlist-read-private playlist-modify-private user-modify-playback-state user-read-recently-played user-read-playback-position user-read-playback-state user-read-currently-playing ";
    }
);
$container->set(
    "REDIRECT_URI",
    function () {
        return "http://localhost:8080/auth/connectBack";
    }
);

$container->set(
    'CLIENT_ID_SEC',
    function () {
        return "Basic NDNkOWEyNDRmOTM5NDI3NWI1Yjc3M2UxOGE5OTlhNTQ6Njk5OWQ5Y2I2MDMzNDI0ZGEwNGIzYzkzMDBhMWIyNTg=";
    }
);

// setting Guzzle client
$container->set(
    "guzzle",
    function () {
        return "http://localhost:8080/auth/connectBack";
    }
);

$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'spotify',
            ]
        );
    }
);

// $container->set(
//     'mongo',
//     function () {
//         $mongo = new MongoClient();

//         return $mongo->selectDB('phalt');
//     },
//     true
// );


try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}

// $di = new FactoryDefault();
// $app = new Micro($di);
// $app->get(
//     '/api/robots',
//     function () {
//         return json_encode(array("status" => 200));
//     }
// );
// $app->handle($_GET["_url"]);
