<?php declare(strict_types=1);

// src/Service/LoginUserService.php
namespace App\Service;

use App\Bundle\Config;
use App\Controller\LoginUserController;
use App\Form\LoginUserForm;
use App\Form\Type\LoginUserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class LoginUserService extends Controller
{
    protected $controller;
    protected $config;

    public function __construct(
        LoginUserController $controller,
        Config $config
    ) {
        $this->controller = $controller;
        $this->config = $config;
    }

    public function formAction(Request $request): array
    {
        $session = $request->getSession();
        $em = $this->controller->getDoctrine()->getManager();

        $loginUserForm = new LoginUserForm();
        $form = $this->controller->createForm(
            LoginUserFormType::class,
            $loginUserForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($loginUserForm->getForget()) {
                $userLogin = $em->getRepository('App:User')
                    ->isUserLogin($loginUserForm->getLogin());
                if ($userLogin) {
                    if ($userLogin->getActive()) {
                        $passwordChangeEmail = $this->sendPasswordChangeEmail(
                            $userLogin
                        );

                        return array(
                            'login-user/more-instructions-info.html.twig',
                            array(
                                'activeMenu' => 'login-user',
                                'passwordChangeEmail' =>
                                    $passwordChangeEmail
                            )
                        );
                    } else {
                        $activationEmail = $this->sendActivationEmail(
                            $userLogin
                        );

                        return array(
                            'login-user/account-not-active-info.html.twig',
                            array(
                                'activeMenu' => 'login-user',
                                'activationEmail' => $activationEmail,
                                'password' => false
                            )
                        );
                    }
                } else {
                    $form->get('login')->addError(
                        new FormError('Konto o podanym loginie nie istnieje.')
                    );
                }
            } else {
                $userPassword = $em->getRepository('App:User')
                    ->getUserPassword($loginUserForm->getLogin());
                $passwordVerify = password_verify(
                    $loginUserForm->getPassword(),
                    ($userPassword) ? $userPassword->getPassword() : ''
                );
                if ($passwordVerify) {
                    if ($userPassword->getActive()) {
                        $em->getRepository('App:User')
                            ->setUserLoged(
                                $userPassword->getId(),
                                $this->config->getRemoteAddress(),
                                $this->config->getDateTimeNow()
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
                                $userPassword->getLogin() . ';'
                                    . $userPassword->getKey(),
                                time() + 365 * 24 * 60 * 60, '/'
                            );
                        }
                        header(
                            'Location: ' . $this->config->getUrl() . '/konto'
                        );
                        exit;
                    } else {
                        $activationEmail = $this->sendActivationEmail(
                            $userPassword
                        );

                        return array(
                            'login-user/account-not-active-info.html.twig',
                            array(
                                'activeMenu' => 'login-user',
                                'activationEmail' => $activationEmail,
                                'password' => true
                            )
                        );
                    }
                } else {
                    $form->addError(
                        new FormError(
                            'Konto o podanym loginie i haśle nie istnieje.'
                        )
                    );
                }
            }
        }

        return array('login-user/login-user.html.twig', array(
            'activeMenu' => 'login-user',
            'form' => $form->createView()
        ));
    }

    public function codeAction(string $user, string $code): array
    {
        $em = $this->controller->getDoctrine()->getManager();

        $userLogin = $em->getRepository('App:User')
            ->isUserLogin($user);
        if ($userLogin && $code == $userLogin->getKey()) {
            if (!$userLogin->getActive()) {
                return array(
                    'login-user/account-not-active-info.html.twig',
                    array('activeMenu' => 'login-user')
                );
            } else {
                $userPassword = $em->getRepository('App:User')
                    ->setUserPassword(
                        $userLogin->getId(),
                        $password = $em->getRepository('App:User')
                            ->generatePassword(),
                        $em->getRepository('App:User')->generateKey(),
                        $this->config->getRemoteAddress(),
                        $this->config->getDateTimeNow()
                    );
                if ($userPassword) {
                    $newPasswordEmail = $this->sendNewPasswordEmail(
                        $userLogin,
                        $password
                    );

                    return array(
                        'login-user/password-changed-info.html.twig',
                        array(
                            'activeMenu' => 'login-user',
                            'newPasswordEmail' => $newPasswordEmail
                        )
                    );
                } else {
                    return array(
                        'login-user/password-not-changed-info.html.twig',
                        array('activeMenu' => 'login-user')
                    );
                }
            }
        } else {
            return array(
                'login-user/code-not-valid-info.html.twig',
                array('activeMenu' => 'login-user')
            );
        }
    }

    private function sendActivationEmail(object $user): int
    {
        $emailFrom = $this->config->getAdminEmail();
        $emailTo = $user->getEmail();
        $message = (new \Swift_Message('Aktywacja konta'))
            ->setFrom($emailFrom)
            ->setTo($emailTo)
            ->setBody(
                $this->controller->renderView(
                    'send-email/send-activation-email.html.twig',
                    array(
                        'serverDomain' => $this->config->getServerDomain(),
                        'adminEmail' => $this->config->getAdminEmail(),
                        'url' => $this->config->getUrl(),
                        'login' => $user->getLogin(),
                        'key' => $user->getKey()
                    )
                ),
                'text/html'
            )
        ;

        return $this->controller->get('mailer')->send($message);
    }

    private function sendPasswordChangeEmail(object $user): int
    {
        $emailFrom = $this->config->getAdminEmail();
        $emailTo = $user->getEmail();
        $message = (new \Swift_Message('Zmiana hasła konta'))
            ->setFrom($emailFrom)
            ->setTo($emailTo)
            ->setBody(
                $this->controller->renderView(
                    'send-email/send-password-change-email.html.twig',
                    array(
                        'serverDomain' => $this->config->getServerDomain(),
                        'adminEmail' => $this->config->getAdminEmail(),
                        'url' => $this->config->getUrl(),
                        'login' => $user->getLogin(),
                        'key' => $user->getKey()
                    )
                ),
                'text/html'
            )
        ;

        return $this->controller->get('mailer')->send($message);
    }

    private function sendNewPasswordEmail(object $user, string $password): int
    {
        $emailFrom = $this->config->getAdminEmail();
        $emailTo = $user->getEmail();
        $message = (new \Swift_Message('Nowe hasło konta'))
            ->setFrom($emailFrom)
            ->setTo($emailTo)
            ->setBody(
                $this->controller->renderView(
                    'send-email/send-new-password-email.html.twig',
                    array(
                        'serverDomain' => $this->config->getServerDomain(),
                        'adminEmail' => $this->config->getAdminEmail(),
                        'login' => $user->getLogin(),
                        'password' => $password
                    )
                ),
                'text/html'
            )
        ;

        return $this->controller->get('mailer')->send($message);
    }
}
