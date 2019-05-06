<?php declare(strict_types=1);

// src/Controller/LoginUserController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin, Html};
use App\Form\LoginUserForm;
use App\Form\Type\LoginUserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginUserController extends Controller
{
    /**
     * @Route("/logowanie", name="loginpage")
     */
    public function loginUserAction(Request $request): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $cookieLogin = new CookieLogin($em, $config);
        $cookieLogin->setCookieLogin($session);

        $message = '';
        $ok = false;

        $loginUserForm = new LoginUserForm();
        $form = $this->createForm(LoginUserFormType::class, $loginUserForm);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $validator = $this->get('validator');
            $errors = $validator->validate($loginUserForm);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $message .= $error->getMessage() . "\r\n";
                }
            } else {
                if ($loginUserForm->getForget()) {
                    $userLogin = $em->getRepository('App:User')
                        ->isUserLogin($loginUserForm->getLogin());
                    if ($userLogin) {
                        if ($userLogin->getActive()) {
                            $passwordChangeEmail = $this
                                ->sendPasswordChangeEmail(
                                    $config,
                                    $loginUserForm,
                                    $userLogin
                                );
                            if ($passwordChangeEmail) {
                                $message .= 'Sprawdź pocztę w celu '
                                    . 'poznania dalszych instrukcji.'
                                    . "\r\n";
                                $ok = true;
                                unset($loginUserForm);
                                unset($form);
                                $loginUserForm = new LoginUserForm();
                                $form = $this->createForm(
                                    LoginUserFormType::class,
                                    $loginUserForm
                                );
                            } else {
                                $message .= "Wysłanie e-mail'a "
                                    . 'z dalszymi instrukcjami'
                                    . ' nie powiodło się.' . "\r\n";
                            }
                        } else {
                            $message .= 'Konto o podanym loginie '
                                . 'nie jest aktywne.' . "\r\n";
                            $activationEmail = $this->sendActivationEmail(
                                $config,
                                $loginUserForm,
                                $userLogin
                            );
                            if ($activationEmail) {
                                $message .= 'Sprawdź pocztę w celu '
                                    . 'aktywacji konta.' . "\r\n";
                            } else {
                                $message .= "Wysłanie e-mail'a "
                                    . 'aktywacyjnego nie powiodło się.'
                                    . "\r\n";
                            }
                        }
                    } else {
                        $message .= 'Konto o podanym loginie '
                            . 'nie istnieje.' . "\r\n";
                    }
                } else {
                    $userPassword = $em->getRepository('App:User')
                        ->isUserPassword(
                            $loginUserForm->getLogin(),
                            $loginUserForm->getPassword()
                        );
                    if ($userPassword) {
                        if ($userPassword->getActive()) {
                            $em->getRepository('App:User')
                                ->setUserLoged(
                                    $userPassword->getId(),
                                    $config->getRemoteAddress(),
                                    $config->getDateTimeNow()
                                );
                            $session->set('id', $userPassword->getId());
                            $session->set(
                                'admin',
                                $userPassword->getAdmin()
                            );
                            $session->set(
                                'user',
                                $userPassword->getLogin()
                            );
                            if ($loginUserForm->getRemember()) {
                                setcookie(
                                    'login',
                                    $loginUserForm->getLogin() . ';'
                                        . $userPassword->getPassword(),
                                    time() + 365 * 24 * 60 * 60, '/'
                                );
                            }
                            header(
                                'Location: ' . $config->getUrl() . '/konto'
                            );
                            exit;
                        } else {
                            $message .= 'Konto o podanym loginie i haśle '
                             . 'nie jest aktywne.' . "\r\n";
                            $activationEmail = $this->sendActivationEmail(
                                $config,
                                $loginUserForm,
                                $userPassword
                            );
                            if ($activationEmail) {
                                $message .= 'Sprawdź pocztę w celu '
                                    . 'aktywacji konta.' . "\r\n";
                            } else {
                                $message .= "Wysłanie e-mail'a "
                                    . 'aktywacyjnego nie powiodło się.'
                                    . "\r\n";
                            }
                        }
                    } else {
                        $message .= 'Konto o podanym loginie i haśle '
                         . 'nie istnieje.' . "\r\n";
                    }
                }
            }
        }

        return $this->render('login-user/login-user.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'login-user',
            'form' => $form->createView(),
            'message' => Html::prepareMessage($message, $ok)
        ));
    }

    /**
     * @Route("/logowanie,{user},{code}")
     */
    public function sendEmailAction(
        Request $request,
        string $user,
        string $code
    ): object {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $cookieLogin = new CookieLogin($em, $config);
        $cookieLogin->setCookieLogin($session);

        $message = '';
        $ok = false;

        $loginUserForm = new LoginUserForm();
        $form = $this->createForm(LoginUserFormType::class, $loginUserForm);

        $userLogin = $em->getRepository('App:User')
            ->isUserLogin($user);
        if ($userLogin && $code == $userLogin->getKey()) {
            if (!$userLogin->getActive()) {
                $message .= 'Konto użytkownika nie jest aktywne.' . "\r\n";
            } else {
                $userPassword = $em->getRepository('App:User')
                    ->setUserPassword(
                        $userLogin->getId(),
                        $password = $em->getRepository('App:User')
                            ->generatePassword(),
                        $config->getRemoteAddress(),
                        $config->getDateTimeNow()
                    );
                if ($userPassword) {
                    $message .= 'Hasło konta użytkownika '
                        . 'zostało zmienione.' . "\r\n";
                    $ok = true;
                    $newPasswordEmail = $this->sendNewPasswordEmail(
                        $config,
                        $userLogin,
                        $password
                    );
                    if ($newPasswordEmail) {
                        $message .= 'Sprawdź pocztę w celu '
                            . 'zapoznania z hasłem.' . "\r\n";
                    } else {
                        $message .= "Wysłanie e-mail'a z hasłem "
                            . 'nie powiodło się.' . "\r\n";
                        $ok = false;
                    }
                    unset($loginUserForm);
                    unset($form);
                    $loginUserForm = new LoginUserForm();
                    $form = $this->createForm(
                        LoginUserFormType::class,
                        $loginUserForm
                    );
                } else {
                    $message .= 'Zmiana hasła konta użytkownika '
                        . 'nie powiodła się.' . "\r\n";
                }
            }
        } else {
            $message .= 'Podany kod zmiany hasła jest niepoprawny.' . "\r\n";
        }

        return $this->render('login-user/login-user.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'login-user',
            'form' => $form->createView(),
            'message' => Html::prepareMessage($message, $ok)
        ));
    }

    private function sendActivationEmail(
        object $config,
        object $loginUserForm,
        object $user
    ): int {
        $emailFrom = $config->getAdminEmail();
        $emailTo = $user->getEmail();
        $subject = 'Aktywacja konta ' . $loginUserForm->getLogin()
            . ' w serwisie ' . $config->getServerDomain();
        $text = 'Aby aktywować konto, otwórz w oknie przeglądarki url poniżej.'
            . "\r\n\r\n" . $config->getUrl() . '/rejestracja,'
            . $user->getLogin() . ',' . $user->getKey()
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

    private function sendPasswordChangeEmail(
        object $config,
        object $loginUserForm,
        object $user
    ): int {
        $emailFrom = $config->getAdminEmail();
        $emailTo = $user->getEmail();
        $subject = 'Zmiana hasła konta ' . $loginUserForm->getLogin()
            . ' w serwisie ' . $config->getServerDomain();
        $text = 'Aby zmienić hasło konta, otwórz w oknie przeglądarki url '
            . 'poniżej.' . "\r\n\r\n" . $config->getUrl() . '/logowanie,'
            . $user->getLogin() . ',' . $user->getKey()
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

    private function sendNewPasswordEmail(
        object $config,
        object $user,
        string $password
    ): int {
        $emailFrom = $config->getAdminEmail();
        $emailTo = $user->getEmail();
        $subject = 'Nowe hasło konta ' . $user->getLogin()
            . ' w serwisie ' . $config->getServerDomain();
        $text = 'Nowe hasło konta: ' . $password . "\r\n\r\n" . '--' . "\r\n"
            . $config->getAdminEmail();
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
