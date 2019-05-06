<?php declare(strict_types=1);

// src/Controller/EditUserController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin, Html};
use App\Form\EditUserForm;
use App\Form\Type\EditUserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EditUserController extends Controller
{
    /**
     * @Route("/uzytkownik,{user},edycja", requirements={"user": "\d+"})
     */
    public function editUserAction(Request $request, int $user): object
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

        $userUser = $em->getRepository('App:User')
            ->isUserId($session->get('id'), $user);
        if (!$userUser) {
            return $this->redirectToRoute('loginpage');
        }

        $userData = $em->getRepository('App:User')->getUserData($user);
        $editUserForm = new EditUserForm($em, $user);
        EditUserFormType::init($em, 0);
        $form = $this->createForm(EditUserFormType::class, $editUserForm);
        $form->handleRequest($request);
        $province = $editUserForm->getProvince()
            ?? (($userData->getProvince() !== null)
            ? $userData->getProvince()->getId() : 0);
        unset($editUserForm);
        unset($form);

        $editUserForm = new EditUserForm($em, $user);
        EditUserFormType::init($em, $province);
        $form = $this->createForm(
            EditUserFormType::class,
            $editUserForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $validator = $this->get('validator');
            $errors = $validator->validate($editUserForm);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $message .= $error->getMessage() . "\r\n";
                }
            } else {
                if ($userData->getLogin() != $editUserForm->getLogin()) {
                    $message = 'Powstrzymano próbę zapisu danych '
                        . 'z innego konta.' . "\r\n";
                    unset($editUserForm);
                    unset($form);
                    $editUserForm = new EditUserForm($em, $user);
                    $form = $this->createForm(
                        EditUserFormType::class,
                        $editUserForm
                    );
                } else {
                    $key = $em->getRepository('App:User')->generateKey();
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
                        $config->getRemoteAddress(),
                        $config->getDateTimeNow()
                    );
                    if ($userData) {
                        $message .= 'Dane użytkownika zostały zapisane.'
                            . "\r\n";
                        $ok = true;
                        if ($editUserForm->getNewPassword() != '') {
                            $message .= 'Hasło użytkownika zostało zapisane.'
                                . "\r\n";
                            setcookie('login', '', 0, '/');
                        }
                        if ($editUserForm->getNewEmail() != '') {
                            $message .= 'E-mail użytkownika został zapisany.'
                                . "\r\n";
                            $session->invalidate();
                            setcookie('login', '', 0, '/');
                            $activationEmail = $this->sendActivationEmail(
                                $config,
                                $editUserForm,
                                $key
                            );
                            if ($activationEmail) {
                                $message .= 'Sprawdź pocztę w celu '
                                    . 'aktywacji konta.' . "\r\n";
                            } else {
                                $message .= "Wysłanie e-mail'a aktywacyjnego "
                                    . 'nie powiodło się.' . "\r\n";
                                $ok = false;
                            }
                            unset($editUserForm);
                            unset($form);
                            $editUserForm = new EditUserForm($em, $user);
                            $form = $this->createForm(
                                EditUserFormType::class,
                                $editUserForm
                            );
                        }
                    } else {
                        $message .= 'Zapisanie danych użytkownika '
                            . 'nie powiodło się.' . "\r\n";
                    }
                }
            }
        } else {
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
            $editUserForm->setLogin($userData->getLogin());
            $form = $this->createForm(EditUserFormType::class, $editUserForm);
            $form->handleRequest($request);
        }

        return $this->render('edit-user/edit-user.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'edit-user',
            'form' => $form->createView(),
            'message' => Html::prepareMessage($message, $ok),
            'selectedCity' => $editUserForm->getCity()
        ));
    }

    private function sendActivationEmail(
        object $config,
        object $editUserForm,
        string $key
    ): int {
        $emailFrom = $config->getAdminEmail();
        $emailTo = $editUserForm->getNewEmail();
        $subject = 'Aktywacja konta ' . $editUserForm->getLogin()
            . ' w serwisie ' . $config->getServerDomain();
        $text = 'Aby aktywować konto, otwórz w oknie przeglądarki url poniżej.'
            . "\r\n\r\n" . $config->getUrl() . '/rejestracja,'
            . $editUserForm->getLogin() . ',' . $key
            . "\r\n\r\n" . '--' . "\r\n" . $config->getAdminEmail();
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
