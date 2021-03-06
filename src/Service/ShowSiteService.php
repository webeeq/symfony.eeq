<?php

declare(strict_types=1);

namespace App\Service;

use App\Bundle\Config;
use App\Controller\ShowSiteController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ShowSiteService extends Controller
{
    protected ShowSiteController $showSiteController;
    protected Config $config;

    public function __construct(
        ShowSiteController $showSiteController,
        Config $config
    ) {
        $this->showSiteController = $showSiteController;
        $this->config = $config;
    }

    public function urlAction(): array
    {
        $em = $this->showSiteController->getDoctrine()->getManager();
        $id = $this->showSiteController->getUser()->getId();
        $url = '';

        $userMaxShow = $em->getRepository('App:User')
            ->isUserMaxShow($id);
        if (!$userMaxShow) {
            $siteRandomUrl = $em->getRepository('App:Site')
                ->getSiteRandomUrl($id);
            if ($siteRandomUrl) {
                $user = $siteRandomUrl->getUser();
                $userShow = $em->getRepository('App:User')
                    ->setUserShow($id, $user);
                if ($userShow) {
                    $url = $siteRandomUrl->getUrl();
                }
            } else {
                $siteRandomUrl = $em->getRepository('App:Site')
                    ->getSiteRandomUrl($id, 0);
                if ($siteRandomUrl) {
                    $user = $siteRandomUrl->getUser();
                    $userShow = $em->getRepository('App:User')
                        ->setUserShow($id, $user, 0);
                    if ($userShow) {
                        $url = $siteRandomUrl->getUrl();
                    }
                }
            }
        } else {
            $url = $this->config->getUrl() . '/info';
        }

        return array('show-site/show-site.html.twig', array(
            'activeMenu' => 'show-site',
            'url' => $url
        ));
    }
}
