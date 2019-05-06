<?php declare(strict_types=1);

// src/Controller/AcceptSiteController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin, Html};
use App\Form\AcceptSiteForm;
use App\Form\Type\AcceptSiteFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AcceptSiteController extends Controller
{
    /**
     * @Route("/strona,{site},akceptacja", requirements={"site": "\d+"})
     */
    public function acceptSiteAction(Request $request, int $site): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $cookieLogin = new CookieLogin($em, $config);
        $cookieLogin->setCookieLogin($session);

        if ($session->get('admin') == false) {
            return $this->redirectToRoute('loginpage');
        }

        $message = '';
        $ok = false;

        $acceptSite = $em->getRepository('App:Site')->isAcceptSiteId($site);
        if (!$acceptSite) {
            return $this->redirectToRoute('loginpage');
        }

        $acceptSiteForm = new AcceptSiteForm();
        $form = $this->createForm(
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
                    $message .= 'Strona www została odrzucona.' . "\r\n";
                    $message .= 'Dane strony www zostały usunięte.' . "\r\n";
                    $ok = true;
                    $acceptationEmail = $this->sendAcceptationEmail(
                        $config,
                        $acceptSiteForm,
                        $acceptUserData
                    );
                    if ($acceptationEmail) {
                        $message .= 'E-mail akceptacyjny został wysłany.'
                            . "\r\n";
                    } else {
                        $message .= "Wysłanie e-mail'a akceptacyjnego "
                            . 'nie powiodło się.' . "\r\n";
                        $ok = false;
                    }
                    unset($acceptSiteForm);
                    unset($form);
                    $acceptSiteForm = new AcceptSiteForm();
                    $form = $this->createForm(
                        AcceptSiteFormType::class,
                        $acceptSiteForm
                    );
                } else {
                    $message .= 'Usunięcie danych strony www nie powiodło się.'
                        . "\r\n";
                }
            } else {
                $validator = $this->get('validator');
                $errors = $validator->validate($acceptSiteForm);
                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        $message .= $error->getMessage() . "\r\n";
                    }
                } else {
                    $acceptSiteData = $em->getRepository('App:Site')
                        ->setAcceptSiteData(
                            $site,
                            $acceptSiteForm->getActive(),
                            $acceptSiteForm->getVisible(),
                            $acceptSiteForm->getName(),
                            $acceptSiteForm->getUrl(),
                            $config->getRemoteAddress(),
                            $config->getDateTimeNow()
                        );
                    if ($acceptSiteData) {
                        if ($active = $acceptSiteForm->getActive()) {
                            $message .= 'Strona www została zaakceptowana.'
                                . "\r\n";
                        }
                        $message .= 'Dane strony www zostały zapisane.'
                            . "\r\n";
                        $ok = true;
                        if ($active) {
                            $acceptUserData = $em->getRepository('App:Site')
                                ->getAcceptUserData($site);
                            $acceptationEmail = $this->sendAcceptationEmail(
                                $config,
                                $acceptSiteForm,
                                $acceptUserData
                            );
                            if ($acceptationEmail) {
                                $message .= 'E-mail akceptacyjny został '
                                    . 'wysłany.' . "\r\n";
                            } else {
                                $message .= "Wysłanie e-mail'a akceptacyjnego "
                                    . 'nie powiodło się.' . "\r\n";
                                $ok = false;
                            }
                        }
                    } else {
                        $message .= 'Zapisanie danych strony www '
                            . 'nie powiodło się.' . "\r\n";
                    }
                }
            }
        } else {
            $acceptSiteData = $em->getRepository('App:Site')
                ->getAcceptSiteData($site);
            $acceptSiteForm->setName($acceptSiteData->getName());
            $acceptSiteForm->setUrl($acceptSiteData->getUrl());
            $acceptSiteForm->setActive($acceptSiteData->getActive());
            $acceptSiteForm->setVisible($acceptSiteData->getVisible());
            $form = $this->createForm(
                AcceptSiteFormType::class,
                $acceptSiteForm
            );
            $form->handleRequest($request);
        }

        return $this->render('accept-site/accept-site.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'accept-site',
            'form' => $form->createView(),
            'message' => Html::prepareMessage($message, $ok),
            'name' => $acceptSiteForm->getName(),
            'url' => $acceptSiteForm->getUrl()
        ));
    }

    private function sendAcceptationEmail(
        object $config,
        object $acceptSiteForm,
        object $user
    ): ?int {
        if ($acceptSiteForm->getDelete()) {
            $accept = 'Strona www podana poniżej została odrzucona.';
        } elseif ($acceptSiteForm->getActive()) {
            $accept = 'Strona www podana poniżej została zaakceptowana.';
        } else {
            return null;
        }
        $emailFrom = $config->getAdminEmail();
        $emailTo = $user->getEmail();
        $subject = 'Akceptacja strony www konta ' . $user->getLogin()
            . ' w serwisie ' . $config->getServerDomain();
        $text = $accept . "\r\n\r\n" . $acceptSiteForm->getUrl() . "\r\n\r\n"
            . '--' . "\r\n" . $config->getAdminEmail();
        $message = (new \Swift_Message($subject))
            ->setFrom($emailFrom)
            ->setTo($emailTo)
            ->setBody(
                $this->renderView('send-email/send-email.html.twig', array(
                    'emailFrom' => $emailFrom,
                    'emailTo' => $emailTo,
                    'subject' => $subject,
                    'text' => $text
                )),
                'text/html'
            )
        ;

        return $this->get('mailer')->send($message);
    }
}
