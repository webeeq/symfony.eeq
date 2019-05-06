<?php declare(strict_types=1);

// src/Controller/UserAccountController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin, Html};
use App\Entity\Site;
use App\Form\Type\UserAccountFormType;
use App\Form\UserAccountForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserAccountController extends Controller
{
    /**
     * @Route("/konto")
     */
    public function userAccountAction(Request $request): object
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

        $userData = $em->getRepository('App:User')
            ->getUserData($id = $session->get('id'));
        if (!$userData) {
            return $this->redirectToRoute('loginpage');
        }

        $form = $this->userAccountForm(
            $config,
            $request,
            $userData,
            $message,
            $ok
        );

        $siteList = $em->getRepository('App:Site')
            ->getSiteList($id, 1, $listLimit = 10);
        $pageNavigator = $em->getRepository('App:Site')
            ->pageNavigator($config->getUrl(), $id, 1, $listLimit);

        return $this->render('user-account/user-account.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'user-account',
            'form' => $form->createView(),
            'message' => Html::prepareMessage($message, $ok),
            'userData' => $userData,
            'siteList' => $siteList,
            'pageNavigator' => $pageNavigator
        ));
    }

    /**
     * @Route(
     *     "/konto,{account},strona,{level}",
     *     requirements={"account": "\d+", "level": "\d+"}
     * )
     */
    public function userAccountLevelAction(
        Request $request,
        int $account,
        int $level
    ): object {
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

        if ($account != $session->get('id')) {
            return $this->redirectToRoute('loginpage');
        }

        $userData = $em->getRepository('App:User')
            ->getUserData($account);
        if (!$userData) {
            return $this->redirectToRoute('loginpage');
        }

        $form = $this->userAccountForm(
            $config,
            $request,
            $userData,
            $message,
            $ok
        );

        $siteList = $em->getRepository('App:Site')
            ->getSiteList($account, $level, $listLimit = 10);
        $pageNavigator = $em->getRepository('App:Site')
            ->pageNavigator($config->getUrl(), $account, $level, $listLimit);

        return $this->render('user-account/user-account.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'user-account',
            'form' => $form->createView(),
            'message' => Html::prepareMessage($message, $ok),
            'userData' => $userData,
            'siteList' => $siteList,
            'pageNavigator' => $pageNavigator
        ));
    }

    private function userAccountForm(
        object $config,
        Request $request,
        object $userData,
        string &$message,
        bool &$ok
    ): object {
        $userAccountForm = new UserAccountForm();
        $form = $this->createForm(
            UserAccountFormType::class,
            $userAccountForm
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $validator = $this->get('validator');
            $errors = $validator->validate($userAccountForm);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $message .= $error->getMessage() . "\r\n";
                }
            } else {
                $site = new Site();
                $site->setUser($userData);
                $site->setActive(false);
                $site->setVisible(true);
                $site->setName($userAccountForm->getName());
                $site->setUrl($userAccountForm->getUrl());
                $site->setIpAdded($config->getRemoteAddress());
                $site->setDateAdded($config->getDateTimeNow());
                $site->setIpUpdated('');
                $site->setDateUpdated(new \DateTime('1970-01-01 00:00:00'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($site);
                try {
                    $em->flush();
                    $message .= 'Strona www została dodana i oczekuje '
                        . 'na akceptację.' . "\r\n";
                    $ok = true;
                    unset($userAccountForm);
                    unset($form);
                    $userAccountForm = new UserAccountForm();
                    $form = $this->createForm(
                        UserAccountFormType::class,
                        $userAccountForm
                    );
                } catch (\Exception $e) {
                    $message .= 'Dodanie strony www nie powiodło się.'
                        . "\r\n";
                }
            }
        }

        return $form;
    }
}
