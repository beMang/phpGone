<?php
/**
 * Fichier de la classe Routeur
 *
 * PHP Version 5
 *
 * @license MIT
 * @copyright 2017 Antonutti Adrien
 * @author Antonutti Adrien <antonuttiadrien@email.com>
 */
namespace phpGone\Router;

 /**
  * class Routeur
  *
  * Permet de choisir la bonne vue en fonction de l'HTTPRequest et du fichier de configuration
  * @package adriRoot
  */
class Routeur
{
    protected $routes = [];
    const NO_ROUTE = 1;

    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }

    public function getRoute($url)
    {
        foreach ($this->routes as $route) {
            $varsValues = $route->match($url);

            //Si la route correspond à l'url
            if ($varsValues !== false) {
                if ($route->hasVars() == true) {
                    $varsNames = $route->getVarsNames();
                    $listVars = [];

                    foreach ($varsValues as $key => $match) {
                        if ($key !== 0) {
                            $listVars[$varsNames[$key -1]] = $match;
                        }
                        unset($match);
                    }
                    $route->setVars($listVars);
                }
                return $route;
            }
            unset($route);
        }
        throw new \RuntimeException('Aucune route ne correspond à l\'url', self::NO_ROUTE);
    }
}
