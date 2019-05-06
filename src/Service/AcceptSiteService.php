<?php declare(strict_types=1);

// src/Service/AcceptSiteService.php
namespace App\Service;

use App\Bundle\Config;
use App\Controller\AcceptSiteController;
use App\Form\AcceptSiteForm;
use App\Form\Type\AcceptSiteFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AcceptSiteService extends Controller
{
    protected $controller;
    protected $config;

    public function __construct(
        AcceptSiteController $controller,
        Config $config
    ) {
        $this->controller = $controller;
        $this->config = $config;
    }

    public function formAction(Request $request, int $site): array
    {
        $em = $this->controller->getDoctrine()->getManager();

        $acceptSiteForm = new AcceptSiteForm();
        $form = $this->controller->createForm(
            AcceptSiteFormType::class,
            $acceptSiteForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($acceptSiteForm->getDelete()) {
                $acceptUserData = $em->getRepository('App:Site')
                    ->getAcceptUserData($site);
                $siteData = $em->getRepository('App:Site')
                    ->deleteSiteData($site);
                if ($siteData) {
                    $acceptationEmail = $this->sendAcceptationEmail(
                        $acceptSiteForm,
                        $acceptUserData
                    );

                    return array(
                        'accept-site/site-rejected-info.html.twig',
                        array(
                            'activeMenu' => 'accept-site',
                            'acceptationEmail' => $acceptationEmail
                        )
                    );
                } else {
                    return array(
                        'accept-site/data-not-deleted-info.html.twig',
                        array('activeMenu' => 'accept-site')
                    );
                }
            } elseif ($form->isValid()) {
                $acceptSiteData = $em->getRepository('App:Site')
                    ->setAcceptSiteData(
                        $site,
                        $acceptSiteForm->getActive(),
                        $acceptSiteForm->getVisible(),
                        $acceptSiteForm->getName(),
                        $acceptSiteForm->getUrl(),
                        $this->config->getRemoteAddress(),
                        $this->config->getDateTimeNow()
                    );
                if ($acceptSiteData) {
                    $active = $acceptSiteForm->getActive();
                    $acceptUserData = $em->getRepository('App:Site')
                        ->getAcceptUserData($site);
                    $acceptationEmail = $this->sendAcceptationEmail(
                        $acceptSiteForm,
                        $acceptUserData
                    );

                    return array(
                        'accept-site/site-accepted-info.html.twig',
                        array(
                            'activeMenu' => 'accept-site',
                            'active' => $active,
                            'acceptationEmail' => $acceptationEmail
                        )
                    );
                } else {
                    return array(
                        'accept-site/data-not-saved-info.html.twig',
                        array('activeMenu' => 'accept-site')
                    );
                }
            }
        } else {
            $acceptSiteData = $em->getRepository('App:Site')
                ->getAcceptSiteData($site);
            $acceptSiteForm->setName($acceptSiteData->getName());
            $acceptSiteForm->setUrl($acceptSiteData->getUrl());
            $acceptSiteForm->setActive($acceptSiteData->getActive());
            $acceptSiteForm->setVisible($acceptSiteData->getVisible());
            $form = $this->controller->createForm(
                AcceptSiteFormType::class,
                $acceptSiteForm
            );
            $form->handleRequest($request);
        }

        return array('accept-site/accept-site.html.twig', array(
            'activeMenu' => 'accept-site',
            'form' => $form->createView(),
            'name' => $acceptSiteForm->getName(),
            'url' => $acceptSiteForm->getUrl()
        ));
    }

    private function sendAcceptationEmail(
        object $form,
        object $user
    ): ?int {
        if ($form->getDelete()) {
            $accept = 'Strona www podana poniżej została odrzucona.';
        } elseif ($form->getActive()) {
            $accept = 'Strona www podana poniżej została zaakceptowana.';
        } else {
            return null;
        }
        $emailFrom = $this->config->getAdminEmail();
        $emailTo = $user->getEmail();
        $message = (new \Swift_Message('Akceptacja strony www konta'))
            ->setFrom($emailFrom)
            ->setTo($emailTo)
            ->setBody(
                $this->controller->renderView(
                    'send-email/send-acceptation-email.html.twig',
                    array(
                        'serverDomain' => $this->config->getServerDomain(),
                        'adminEmail' => $this->config->getAdminEmail(),
                        'accept' => $accept,
                        'url' => $form->getUrl(),
                        'login' => $user->getLogin()
                    )
                ),
                'text/html'
            )
        ;

        return $this->controller->get('mailer')->send($message);
    }
}
