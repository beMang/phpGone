<?php

/**
 * Fichier de la classe MiddlewaresDispatcher
 *
 * PHP Version 5
 *
 * @license MIT
 * @copyright 2017 Antonutti Adrien
 * @author Antonutti Adrien <antonuttiadrien@email.com>
 */
namespace phpGone\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * class MiddlewaresDispatcher
 *
 * Gère les middlewares et les execute
 * @package adriRoot
 */
class MiddlewaresDispatcher implements RequestHandlerInterface
{
    /**
     * Contient les différents middlewares à utiliser
     *
     * @var string[]
     */
    protected $middlewares = [
        \phpGone\Middlewares\CoreMiddleware::class,
        \phpGone\Middlewares\NotFoundMiddleware::class
    ];

    /**
     * Index à utiliser pour parcourir les middlwares
     *
     * @var int
     */
    protected $middlewaresIndex = 0;

    /**
     * Ajoute un middleware à l'application (Au début du tableau)
     *
     * @param string $middleware Nom du middleware à utiliser
     * @return void
     */
    public function pipe($middleware)
    {
        array_unshift($this->middlewares, $middleware);
    }

    /**
     * Parcours les middlwares et les execute
     *
     * @param ServerRequestInterface $request Requête à envoyer au middlware
     * @return ResponseInterface Réponse de l'ensemble des middelwares
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new \RuntimeException('Aucun middleware a été défini');
        }
        return call_user_func_array([$middleware, 'process'], [$request, $this]);
    }

    /**
     * Récupère le prochain middleware à utiliser
     *
     * @return \phpGone\Middlewares\MiddlewareInterface/null Middleware ou null si pas de middleware
     */
    private function getMiddleware()
    {
        if (array_key_exists($this->middlewaresIndex, $this->middlewares)) {
            $middleWareClass = $this->middlewares[$this->middlewaresIndex];
            $middleware = new $middleWareClass();
            $this->middlewaresIndex++;
            return $middleware;
        }
        return null;
    }

    public function resetMiddlewares()
    {
        $this->middlewares = [];
    }
}
