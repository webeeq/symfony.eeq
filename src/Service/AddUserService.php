<?php declare(strict_types=1);

// src/Service/AddUserService.php
namespace App\Service;

use App\Bundle\Config;
use App\Controller\AddUserController;
use App\Entity\User;
use App\Form\AddUserForm;
use App\Form\Type\AddUserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AddUserService extends Controller
{
    protected $controller;
    protected $config;

    public function __construct(
        AddUserController $controller,
        Config $config
    ) {
        $this->controller = $controller;
        $this->config = $config;
    }

    public function formAction(Request $request): array
    {
        $em = $this->controller->getDoctrine()->getManager();

        $addUserForm = new AddUserForm($em, '');
        $form = $this->controller->createForm(
            AddUserFormType::class,
            $addUserForm
        );
        $form->handleRequest($request);
        $login = $addUserForm->getLogin() ?? '';
        unset($addUserForm);
        unset($form);

        $addUserForm = new AddUserForm($em, $login);
        $form = $this->controller->createForm(
            AddUserFormType::class,
            $addUserForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();
            $user->setProvince(null);
            $user->setCity(null);
            $user->setAdmin(false);
            $user->setActive(false);
            $user->setName($addUserForm->getName());
            $user->setSurname($addUserForm->getSurname());
            $user->setLogin($addUserForm->getLogin());
            $user->setPassword(password_hash(
                $addUserForm->getPassword(),
                PASSWORD_ARGON2I
            ));
            $user->setKey($em->getRepository('App:User')->generateKey());
            $user->setEmail($addUserForm->getEmail());
            $user->setUrl('');
            $user->setPhone('');
            $user->setStreet('');
            $user->setPostcode('');
            $user->setDescription('');
            $user->setShow(0);
            $user->setIpAdded($this->config->getRemoteAddress());
            $user->setDateAdded($this->config->getDateTimeNow());
            $user->setIpUpdated('');
            $user->setDateUpdated(
                $dateTime = new \DateTime('1970-01-01 00:00:00')
            );
            $user->setIpLoged('');
            $user->setDateLoged($dateTime);
            $em->persist($user);
            try {
                $em->flush();
                $activationEmail = $this->sendActivationEmail($user);

                return array(
                    'add-user/account-created-info.html.twig',
                    array(
                        'activeMenu' => 'add-user',
                        'activationEmail' => $activationEmail
                    )
                );
            } catch (\Exception $e) {
                return array(
                    'add-user/account-not-created-info.html.twig',
                    array('activeMenu' => 'add-user')
                );
            }
        }

        return array('add-user/add-user.html.twig', array(
            'activeMenu' => 'add-user',
            'form' => $form->createView()
        ));
    }

    public function codeAction(string $user, string $code): array
    {
        $em = $this->controller->getDoctrine()->getManager();

        $userLogin = $em->getRepository('App:User')
            ->isUserLogin($user);
        if ($userLogin && $code == $userLogin->getKey()) {
            if ($userLogin->getActive()) {
                return array(
                    'add-user/account-is-active-info.html.twig',
                    array('activeMenu' => 'add-user')
                );
            } else {
                $userActive = $em->getRepository('App:User')
                    ->setUserActive($userLogin->getId());

                return array(
                    'add-user/account-activation-info.html.twig',
                    array(
                        'activeMenu' => 'add-user',
                        'userActive' => $userActive
                    )
                );
            }
        } else {
            return array(
                'add-user/code-not-valid-info.html.twig',
                array('activeMenu' => 'add-user')
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
}
