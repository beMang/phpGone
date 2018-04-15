<?php
/**
 * Fichier de la classe CoreMiddleware
 *
 * PHP Version 5
 *
 * @license MIT
 * @copyright 2017 Antonutti Adrien
 * @author Antonutti Adrien <antonuttiadrien@email.com>
 */
namespace phpGone\Middlewares;

/**
 * Class CoreMiddleware
 * Middleware qui execute les controleurs des modules
 */
class CoreMiddleware extends Middleware
{
    public function __invoke(\Psr\Http\Message\ServerRequestInterface $request, $next)
    {
        $response = new \GuzzleHttp\Psr7\Response();
        $controller = $this->getController(
            new \phpGone\Router\Routeur(),
            $request
        );
        if (is_null($controller)) {
            return $next($request);
        }
        $controller->execute();
        $response->getBody()->write($controller->getMainRender()->render());
        //$this->getApp()->getContainer()->get(\phpGone\Log\Logger::class)
        //                               ->info('Requête traitée par le core(Middleware)');
        return $response;
    }

    /**
     * Récupère le controlleur correspondant à la requête
     *
     * @param \phpGone\Router\Routeur $router Routeur à utiliser
     * @return void
     */
    public function getController($router, $request)
    {
        $xml = new \DOMDocument;
        $xml->load($this->getApp()->getConfig()->get('routesConfigFiles'));

        $routes = $xml->getElementsByTagName('route');
        
        //Parcours des routes du fichier xml de config
        foreach ($routes as $route) {
            $vars = [];

            if ($route->hasAttribute('vars')) {
                $vars = explode(',', $route->getAttribute('vars'));
            }

            $router->addRoute(new \phpGone\Router\Route(
                $route->getAttribute('url'),
                $route->getAttribute('module'),
                $route->getAttribute('action'),
                $vars
            ));
        }

        try {
            $matchedRoute = $router->getRoute($request->getUri()->getPath());
        } catch (\RuntimeException $e) {
            if ($e->getCode() == \phpGone\Router\Routeur::NO_ROUTE) {
                return null; //Permet de traiter la requête NotFound (Middleware)
            }
        }

        $_GET = array_merge($_GET, $matchedRoute->getVars());

        $controllerClass = '\\app\\Modules\\'
                            . $matchedRoute->getModule() . '\\'
                            . $matchedRoute->getModule() . '' . 'Controller';
                            
        return new $controllerClass($this->getApp(), $matchedRoute->getModule(), $matchedRoute->getAction());
    }
}
