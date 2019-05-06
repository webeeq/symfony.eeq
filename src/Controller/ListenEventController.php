<?php declare(strict_types=1);

// src/Controller/ListenEventController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ListenEventController extends Controller
{
    protected $em;
    protected $config;

    public function __construct(EntityManagerInterface $em, Config $config)
    {
        $this->em = $em;
        $this->config = $config;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $cookieLogin = new CookieLogin($this->em, $this->config);
        $cookieLogin->setCookieLogin($session);
    }
}
