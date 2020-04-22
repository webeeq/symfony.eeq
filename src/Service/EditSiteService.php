<?php

declare(strict_types=1);

namespace App\Service;

use App\Bundle\Config;
use App\Controller\EditSiteController;
use App\Form\EditSiteForm;
use App\Form\Type\EditSiteFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EditSiteService extends Controller
{
    protected EditSiteController $controller;
    protected Config $config;

    public function __construct(
        EditSiteController $controller,
        Config $config
    ) {
        $this->controller = $controller;
        $this->config = $config;
    }

    public function formAction(Request $request, int $site): array
    {
        $em = $this->controller->getDoctrine()->getManager();

        $editSiteForm = new EditSiteForm();
        $form = $this->controller->createForm(
            EditSiteFormType::class,
            $editSiteForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($editSiteForm->getDelete()) {
                $siteData = $em->getRepository('App:Site')
                    ->deleteSiteData($site);

                return array(
                    'edit-site/data-deletion-info.html.twig',
                    array(
                        'activeMenu' => 'user-account',
                        'siteData' => $siteData
                    )
                );
            }
            if ($form->isValid()) {
                $siteData = $em->getRepository('App:Site')->setSiteData(
                    $site,
                    $editSiteForm->getVisible(),
                    $editSiteForm->getName(),
                    $this->config->getRemoteAddress(),
                    $this->config->getDateTimeNow()
                );

                return array(
                    'edit-site/data-record-info.html.twig',
                    array(
                        'activeMenu' => 'user-account',
                        'siteData' => $siteData
                    )
                );
            }
        } else {
            $siteData = $em->getRepository('App:Site')->getSiteData($site);
            $editSiteForm->setName($siteData->getName());
            $editSiteForm->setUrl($siteData->getUrl());
            $editSiteForm->setVisible($siteData->getVisible());
            $form = $this->controller->createForm(
                EditSiteFormType::class,
                $editSiteForm
            );
            $form->handleRequest($request);
        }

        return array('edit-site/edit-site.html.twig', array(
            'activeMenu' => 'user-account',
            'form' => $form->createView(),
            'name' => $editSiteForm->getName(),
            'url' => $editSiteForm->getUrl()
        ));
    }
}
