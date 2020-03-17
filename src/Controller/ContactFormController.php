<?php

declare(strict_types=1);

// src/Controller/ContactFormController.php
namespace App\Controller;

use App\Bundle\Config;
use App\Service\ContactFormService;
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
        $config = new Config($this);

        $contactFormService = new ContactFormService($this, $config);
        $array = $contactFormService->formAction($request);

        return $this->render($array[0], $array[1]);
    }
}
