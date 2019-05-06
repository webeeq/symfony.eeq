<?php declare(strict_types=1);

// src/Service/UserAccountService.php
namespace App\Service;

use App\Bundle\{Config, Html};
use App\Controller\UserAccountController;
use App\Entity\Site;
use App\Form\Type\UserAccountFormType;
use App\Form\UserAccountForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserAccountService extends Controller
{
    protected $controller;
    protected $config;
    protected $html;

    public function __construct(
        UserAccountController $controller,
        Config $config,
        Html $html
    ) {
        $this->controller = $controller;
        $this->config = $config;
        $this->html = $html;
    }

    public function formAction(
        Request $request,
        object $userData,
        int $id,
        int $level
    ): array {
        $em = $this->controller->getDoctrine()->getManager();

        $userAccountForm = new UserAccountForm();
        $form = $this->controller->createForm(
            UserAccountFormType::class,
            $userAccountForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $site = new Site();
            $site->setUser($userData);
            $site->setActive(false);
            $site->setVisible(true);
            $site->setName($userAccountForm->getName());
            $site->setUrl($userAccountForm->getUrl());
            $site->setIpAdded($this->config->getRemoteAddress());
            $site->setDateAdded($this->config->getDateTimeNow());
            $site->setIpUpdated('');
            $site->setDateUpdated(new \DateTime('1970-01-01 00:00:00'));
            $em->persist($site);
            try {
                $em->flush();

                return array(
                    'user-account/site-added-info.html.twig',
                    array('activeMenu' => 'user-account')
                );
            } catch (\Exception $e) {
                return array(
                    'user-account/site-not-added-info.html.twig',
                    array('activeMenu' => 'user-account')
                );
            }
        }

        $siteList = $em->getRepository('App:Site')->getSiteList(
            $id,
            $level,
            $listLimit = 10
        );
        $pageNavigator = $em->getRepository('App:Site')->pageNavigator(
            $this->config,
            $this->html,
            $id,
            $level,
            $listLimit
        );

        return array('user-account/user-account.html.twig', array(
            'activeMenu' => 'user-account',
            'form' => $form->createView(),
            'userData' => $userData,
            'siteList' => $siteList,
            'pageNavigator' => $pageNavigator
        ));
    }
}
