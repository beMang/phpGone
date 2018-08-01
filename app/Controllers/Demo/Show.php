<?php

namespace app\Controllers\Demo;

use bemang\Config;
use phpGone\Helpers\Url;
use GuzzleHttp\Psr7\Response;
use bemang\renderer\PHPRender;
use bemang\renderer\TwigRender;

/**
 * Controller basique
 */
class Show extends \phpGone\Core\BackController
{
    protected $mainView;

    public function setUp()
    {
        $this->mainView = 'Demo/index.twig';
    }

    public function index()
    {
        $url = new Url();
        $render = new TwigRender($url->getAppPath('views'), $url->getTmpPath('cache/twig'));
        $render->addTwigExtensions(Config::getInstance()->get('TwigExtensions'));
        return new Response('200', [], $render->render($this->mainView, []));
    }

    public function doc()
    {
        $url = new Url();
        $render = new PHPRender($url->getAppPath('views'), $url->getTmpPath('cache/twig'));
        return new Response('200', [], $render->render('Demo/doc', []));
    }

    /**
     * Voici un example de récupération d'url
     *
     * @param int $num
     * @return void
     */
    public function demonum($num)
    {
        return new Response('200', [], 'ça marche, voici le numéro de l\'url : ' . $num);
    }
}
