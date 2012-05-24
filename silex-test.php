// index.php
<?php

$app = require __DIR__.'/../src/app.php';

require __DIR__.'/../src/controllers.php';

if ($app['debug']) {
    return $app->run();
}

$app['http_cache']->run();

// cat app.php 
<?php

    require_once __DIR__.'/../vendor/autoload.php';
    
    use Silex\Provider\HttpCacheServiceProvider;
    use Silex\Provider\SessionServiceProvider;
    use Silex\Provider\TwigServiceProvider;
    
    $app = new Silex\Application();
    
    $app['debug'] = true;
    
    $app->register(new SessionServiceProvider());
    
    $app->register(new TwigServiceProvider(), array(
        'twig.options'          => array('cache' => false, 'strict_variables' => true),
        'twig.path'             => array(__DIR__ . '/../view')
    ));
    
    $app->before(function() use ($app) {
        $app['session']->start();
    });

    return $app;
    
//  cat controllers.php 
<?php

    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    
    use Silex\ControllerCollection;
    
    
    $controllers = array();
    
    $files = new DirectoryIterator( __DIR__ . '/../controllers/');
    
    foreach($files as $item){
        
        if( false == $item->isFile() )
            continue;
        
        $controllerName = basename($item->getFilename(), '.class.php');
        
        $controllers[$controllerName] = function() use ($controllerName, $app){
        
            $ptr = new ControllerCollection();
            $ptr->get('/{method}', function() use ($app){
            
                require __DIR__ . '../controllers/' . $controllerName;
            
            });
            $app->mount('/' . $controllerName, $ptr);
        };
        
    }
    
    var_dump($controllers);
    
    /*
    $user = new ControllerCollection();
    $user->get('/{method}', function use ($app) {
    
        $controller = require __DIR__ . '../controllers/user/' . $method;
        
    
    });
    $app->mount('/user', $user);
    */
    /*
    $app->match('/', function() use ($app) {
        
        $app['session']->setFlash('warning', 'Warning flash message');
        $app['session']->setFlash('info', 'Info flash message');
        $app['session']->setFlash('success', 'Success flash message');
        $app['session']->setFlash('error', 'Error flash message');
        
        return $app['twig']->render('index.html.twig');


    
    })->bind('homepage');
    
    $app->match('/{url}/{name}', function($url,$name) use ($app) {
    
        echo sprintf("<pre>%s</pre>\n", print_r($url,true));
        echo sprintf("<pre>%s</pre>\n", print_r($name,true));
    
    
    });
    */
    
    
        