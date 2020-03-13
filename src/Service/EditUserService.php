<?php declare(strict_types=1);

// src/Service/EditUserService.php
namespace App\Service;

use App\Bundle\Config;
use App\Controller\EditUserController;
use App\Form\EditUserForm;
use App\Form\Type\EditUserFormType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EditUserService extends Controller
{
    protected $controller;
    protected $config;

    public function __construct(
        EditUserController $controller,
        Config $config
    ) {
        $this->controller = $controller;
        $this->config = $config;
    }

    public function formAction(Request $request, int $user): array
    {
        $session = $request->getSession();
        $em = $this->controller->getDoctrine()->getManager();

        $userData = $em->getRepository('App:User')->getUserData($user);
        $province = $this->getProvince($request, $em, $user, $userData);

        $editUserForm = new EditUserForm($em, $user);
        EditUserFormType::init($em, $province);
        $form = $this->controller->createForm(
            EditUserFormType::class,
            $editUserForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($userData->getUsername() != $editUserForm->getLogin()) {
                return array(
                    'edit-user/record-stopped-info.html.twig',
                    array('activeMenu' => 'user-account')
                );
            }
            $key = $em->getRepository('App:User')->generateKey();
            $userData = $this->setUserData($em, $user, $key, $editUserForm);
            if ($userData) {
                $newPassword = $editUserForm->getNewPassword();
                if ($newPassword != '') {
                    setcookie('login', '', 0, '/');
                }
                $newEmail = $editUserForm->getNewEmail();
                if ($newEmail != '') {
                    $session->invalidate();
                    setcookie('login', '', 0, '/');
                    $activationEmail = $this->sendActivationEmail(
                        $editUserForm,
                        $key
                    );
                }

                return array(
                    'edit-user/data-saved-info.html.twig',
                    array(
                        'activeMenu' => 'user-account',
                        'newPassword' => $newPassword,
                        'newEmail' => $newEmail,
                        'activationEmail' => $activationEmail ?? null
                    )
                );
            } else {
                return array(
                    'edit-user/data-not-saved-info.html.twig',
                    array('activeMenu' => 'user-account')
                );
            }
        } else {
            $this->setEditUserForm($userData, $editUserForm);
            $form = $this->controller->createForm(
                EditUserFormType::class,
                $editUserForm
            );
            $form->handleRequest($request);
        }

        return array('edit-user/edit-user.html.twig', array(
            'activeMenu' => 'user-account',
            'form' => $form->createView(),
            'selectedCity' => $editUserForm->getCity()
        ));
    }

    private function getProvince(
        Request $request,
        EntityManager $em,
        int $user,
        object $userData
    ): int {
        $editUserForm = new EditUserForm($em, $user);
        EditUserFormType::init($em, 0);
        $form = $this->controller->createForm(
            EditUserFormType::class,
            $editUserForm
        );
        $form->handleRequest($request);
        $province = $editUserForm->getProvince()
            ?? (($userData->getProvince() !== null)
            ? $userData->getProvince()->getId() : 0);
        unset($editUserForm);
        unset($form);

        return $province;
    }

    private function setUserData(
        EntityManager $em,
        int $user,
        string $key,
        object $editUserForm
    ): int {
        $userData = $em->getRepository('App:User')->setUserData(
            $user,
            ($editUserForm->getProvince() >= 1)
                ? $editUserForm->getProvince() : null,
            ($editUserForm->getCity() >= 1)
                ? $editUserForm->getCity() : null,
            $editUserForm->getName(),
            $editUserForm->getSurname(),
            $editUserForm->getNewPassword() ?? '',
            $key,
            $editUserForm->getNewEmail() ?? '',
            $editUserForm->getUrl() ?? '',
            $editUserForm->getPhone() ?? '',
            $editUserForm->getStreet() ?? '',
            $editUserForm->getPostcode() ?? '',
            $editUserForm->getDescription() ?? '',
            $this->config->getRemoteAddress(),
            $this->config->getDateTimeNow()
        );

        return $userData;
    }

    private function setEditUserForm(
        object $userData,
        object &$editUserForm
    ): void {
        $editUserForm->setName($userData->getName());
        $editUserForm->setSurname($userData->getSurname());
        $editUserForm->setStreet($userData->getStreet());
        $editUserForm->setPostcode($userData->getPostcode());
        $editUserForm->setProvince(
            ($userData->getProvince() !== null) 
                ? $userData->getProvince()->getId() : 0
        );
        $editUserForm->setCity(
            ($userData->getCity() !== null) 
                ? $userData->getCity()->getId() : 0
        );
        $editUserForm->setPhone($userData->getPhone());
        $editUserForm->setEmail($userData->getEmail());
        $editUserForm->setUrl($userData->getUrl());
        $editUserForm->setDescription($userData->getDescription());
        $editUserForm->setLogin($userData->getUsername());
    }

    private function sendActivationEmail(
        object $form,
        string $key
    ): int {
        $senderName = $this->config->getAdminName();
        $emailFrom = $this->config->getAdminEmail();
        $emailTo = $form->getNewEmail();
        $message = (new \Swift_Message('Aktywacja konta'))
            ->setFrom($emailFrom, $senderName)
            ->setTo($emailTo)
            ->setBody(
                $this->controller->renderView(
                    'send-email/send-reactivation-email.html.twig',
                    array(
                        'login' => $form->getLogin(),
                        'url' => $this->config->getUrl(),
                        'key' => $key
                    )
                ),
                'text/html'
            )
        ;

        return $this->controller->get('mailer')->send($message);
    }
}
