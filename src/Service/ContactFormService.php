<?php declare(strict_types=1);

// src/Service/ContactFormService.php
namespace App\Service;

use App\Bundle\Config;
use App\Controller\ContactFormController;
use App\Form\ContactFormForm;
use App\Form\Type\ContactFormFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContactFormService extends Controller
{
    protected $controller;
    protected $config;

    public function __construct(
        ContactFormController $controller,
        Config $config
    ) {
        $this->controller = $controller;
        $this->config = $config;
    }

    public function formAction(Request $request): array
    {
        $contactFormForm = new ContactFormForm();
        $form = $this->controller->createForm(
            ContactFormFormType::class,
            $contactFormForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contactEmail = $this->sendContactEmail($contactFormForm);

            return array(
                'contact-form/send-message-info.html.twig',
                array(
                    'activeMenu' => 'contact-form',
                    'contactEmail' => $contactEmail
                )
            );
        }

        return array('contact-form/contact-form.html.twig', array(
            'activeMenu' => 'contact-form',
            'form' => $form->createView()
        ));
    }

    private function sendContactEmail(object $form): int
    {
        $emailFrom = $form->getEmail();
        $emailTo = $this->config->getAdminEmail();
        $subject = $form->getSubject();
        $text = $form->getMessage();
        $message = (new \Swift_Message($subject))
            ->setFrom($emailFrom)
            ->setTo($emailTo)
            ->setBody(
                $this->controller->renderView(
                    'send-email/send-email.html.twig',
                    array(
                        'emailFrom' => $emailFrom,
                        'emailTo' => $emailTo,
                        'subject' => $subject,
                        'text' => $text
                    )
                ),
                'text/html'
            )
        ;

        return $this->controller->get('mailer')->send($message);
    }
}
