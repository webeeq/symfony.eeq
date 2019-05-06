<?php declare(strict_types=1);

// src/Controller/AddUserController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin, Html};
use App\Entity\User;
use App\Form\AddUserForm;
use App\Form\Type\AddUserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddUserController extends Controller
{
    /**
     * @Route("/rejestracja")
     */
    public function addUserAction(Request $request): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $cookieLogin = new CookieLogin($em, $config);
        $cookieLogin->setCookieLogin($session);

        $message = '';
        $ok = false;

        $addUserForm = new AddUserForm($em, '');
        $form = $this->createForm(AddUserFormType::class, $addUserForm);
        $form->handleRequest($request);
        $login = $addUserForm->getLogin() ?? '';
        unset($addUserForm);
        unset($form);

        $addUserForm = new AddUserForm($em, $login);
        $form = $this->createForm(AddUserFormType::class, $addUserForm);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $validator = $this->get('validator');
            $errors = $validator->validate($addUserForm);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $message .= $error->getMessage() . "\r\n";
                }
            } else {
                $user = new User();
                $user->setProvince(null);
                $user->setCity(null);
                $user->setAdmin(false);
                $user->setActive(false);
                $user->setName($addUserForm->getName());
                $user->setSurname($addUserForm->getSurname());
                $user->setLogin($addUserForm->getLogin());
                $user->setPassword(md5($addUserForm->getPassword()));
                $user->setKey(
                    md5(date('Y-m-d H:i:s') . $addUserForm->getPassword())
                );
                $user->setEmail($addUserForm->getEmail());
                $user->setUrl('');
                $user->setPhone('');
                $user->setStreet('');
                $user->setPostcode('');
                $user->setDescription('');
                $user->setShow(0);
                $user->setIpAdded($config->getRemoteAddress());
                $user->setDateAdded($config->getDateTimeNow());
                $user->setIpUpdated('');
                $user->setDateUpdated(
                    $dateTime = new \DateTime('1970-01-01 00:00:00')
                );
                $user->setIpLoged('');
                $user->setDateLoged($dateTime);
                $em->persist($user);
                try {
                    $em->flush();
                    $message .= 'Konto użytkownika '
                        . 'zostało utworzone.' . "\r\n";
                    $ok = true;
                    $activationEmail = $this->sendActivationEmail(
                        $config,
                        $addUserForm,
                        $user
                    );
                    if ($activationEmail) {
                        $message .= 'Sprawdź pocztę w celu '
                            . 'aktywacji konta.' . "\r\n";
                    } else {
                        $message .= "Wysłanie e-mail'a aktywacyjnego "
                            . 'nie powiodło się.' . "\r\n";
                        $ok = false;
                    }
                    unset($addUserForm);
                    unset($form);
                    $addUserForm = new AddUserForm($em, '');
                    $form = $this->createForm(
                        AddUserFormType::class,
                        $addUserForm
                    );
                } catch (\Exception $e) {
                    $message .= 'Utworzenie konta użytkownika '
                        . 'nie powiodło się.' . "\r\n";
                }
            }
        }

        return $this->render('add-user/add-user.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'add-user',
            'form' => $form->createView(),
            'message' => Html::prepareMessage($message, $ok)
        ));
    }

    /**
     * @Route("/rejestracja,{user},{code}")
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

        $addUserForm = new AddUserForm($em, '');
        $form = $this->createForm(AddUserFormType::class, $addUserForm);

        $userLogin = $em->getRepository('App:User')
            ->isUserLogin($user);
        if ($userLogin && $code == $userLogin->getKey()) {
            if ($userLogin->getActive()) {
                $message .= 'Konto użytkownika '
                    . 'jest już aktywne.' . "\r\n";
            } else {
                $userActive = $em->getRepository('App:User')
                    ->setUserActive($userLogin->getId());
                if ($userActive) {
                    $message .= 'Konto użytkownika '
                        . 'zostało aktywowane.' . "\r\n";
                    $ok = true;
                } else {
                    $message .= 'Aktywacja konta użytkownika '
                        . 'nie powiodła się.' . "\r\n";
                }
            }
        } else {
            $message .= 'Podany kod aktywacyjny '
                . 'jest niepoprawny.' . "\r\n";
        }

        return $this->render('add-user/add-user.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'add-user',
            'form' => $form->createView(),
            'message' => Html::prepareMessage($message, $ok)
        ));
    }

    private function sendActivationEmail(
        object $config,
        object $addUserForm,
        object $user
    ): int {
        $emailFrom = $config->getAdminEmail();
        $emailTo = $addUserForm->getEmail();
        $subject = 'Aktywacja konta ' . $addUserForm->getLogin()
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
}
