<?php

declare(strict_types=1);

namespace App\Controller;

use App\Bundle\Config;
use App\Service\ShowSiteService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ShowSiteController extends Controller
{
    /**
     * @Route("/pokaz")
     */
    public function showSiteAction(): object
    {
        $config = new Config($this);

        $showSiteService = new ShowSiteService($this, $config);
        $array = $showSiteService->urlAction();

        return $this->render($array[0], $array[1]);
    }
}
