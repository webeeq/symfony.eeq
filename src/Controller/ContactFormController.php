<?php declare(strict_types=1);

// src/Controller/ContactFormController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin, Html};
use App\Form\ContactFormForm;
use App\Form\Type\ContactFormFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactFormController extends Controller
{
    /**
     * @Route("/kontakt")
     */
    public function contactFormAction(Request $request): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $cookieLogin = new CookieLogin($em, $config);
        $cookieLogin->setCookieLogin($session);

        $message = '';
        $ok = false;

        $contactFormForm = new ContactFormForm();
        $form = $this->createForm(
            ContactFormFormType::class,
            $contactFormForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $validator = $this->get('validator');
            $errors = $validator->validate($contactFormForm);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $message .= $error->getMessage() . "\r\n";
                }
            } else {
                $contactEmail = $this->sendContactEmail(
                    $config,
                    $contactFormForm
                );
                if ($contactEmail) {
                    $message .= 'Wiadomość została wysłana.' . "\r\n";
                    $ok = true;
                    unset($contactFormForm);
                    unset($form);
                    $contactFormForm = new ContactFormForm();
                    $form = $this->createForm(
                        ContactFormFormType::class,
                        $contactFormForm
                    );
                } else {
                    $message .= 'Wysłanie wiadomości nie powiodło się.'
                        . "\r\n";
                }
            }
        }

        return $this->render('contact-form/contact-form.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'contact-form',
            'form' => $form->createView(),
            'message' => Html::prepareMessage($message, $ok)
        ));
    }

    private function sendContactEmail(
        object $config,
        object $contactFormForm
    ): int {
        $emailFrom = $contactFormForm->getEmail();
        $emailTo = $config->getAdminEmail();
        $subject = $contactFormForm->getSubject();
        $text = $contactFormForm->getMessage();
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
