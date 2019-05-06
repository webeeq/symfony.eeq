<?php declare(strict_types=1);

// src/Controller/EditSiteController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin, Html};
use App\Form\EditSiteForm;
use App\Form\Type\EditSiteFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EditSiteController extends Controller
{
    /**
     * @Route("/strona,{site},edycja", requirements={"site": "\d+"})
     */
    public function editSiteAction(Request $request, int $site): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $cookieLogin = new CookieLogin($em, $config);
        $cookieLogin->setCookieLogin($session);

        if ($session->get('user') == '') {
            return $this->redirectToRoute('loginpage');
        }

        $message = '';
        $ok = false;

        $userSite = $em->getRepository('App:Site')
            ->isUserSite($session->get('id'), $site);
        if (!$userSite) {
            return $this->redirectToRoute('loginpage');
        }

        $editSiteForm = new EditSiteForm();
        $form = $this->createForm(
            EditSiteFormType::class,
            $editSiteForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($editSiteForm->getDelete()) {
                $siteData = $em->getRepository('App:Site')
                    ->deleteSiteData($site);
                if ($siteData) {
                    $message .= 'Dane strony www zostały usunięte.' . "\r\n";
                    $ok = true;
                    unset($editSiteForm);
                    unset($form);
                    $editSiteForm = new EditSiteForm();
                    $form = $this->createForm(
                        EditSiteFormType::class,
                        $editSiteForm
                    );
                } else {
                    $message .= 'Usunięcie danych strony www nie powiodło się.'
                        . "\r\n";
                }
            } else {
                $validator = $this->get('validator');
                $errors = $validator->validate($editSiteForm);
                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        $message .= $error->getMessage() . "\r\n";
                    }
                } else {
                    $siteData = $em->getRepository('App:Site')->setSiteData(
                            $site,
                            $editSiteForm->getVisible(),
                            $editSiteForm->getName(),
                            $config->getRemoteAddress(),
                            $config->getDateTimeNow()
                        );
                    if ($siteData) {
                        $message .= 'Dane strony www zostały zapisane.'
                            . "\r\n";
                        $ok = true;
                    } else {
                        $message .= 'Zapisanie danych strony www '
                            . 'nie powiodło się.' . "\r\n";
                    }
                }
            }
        } else {
            $siteData = $em->getRepository('App:Site')->getSiteData($site);
            $editSiteForm->setName($siteData->getName());
            $editSiteForm->setUrl($siteData->getUrl());
            $editSiteForm->setVisible($siteData->getVisible());
            $form = $this->createForm(EditSiteFormType::class, $editSiteForm);
            $form->handleRequest($request);
        }

        return $this->render('edit-site/edit-site.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'edit-site',
            'form' => $form->createView(),
            'message' => Html::prepareMessage($message, $ok),
            'name' => $editSiteForm->getName(),
            'url' => $editSiteForm->getUrl()
        ));
    }
}
