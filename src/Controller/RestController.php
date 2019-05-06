<?php declare(strict_types=1);

// src/Controller/RestController.php
namespace App\Controller;

use App\Bundle\{Config, Response};
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class RestController extends Controller
{
    /**
     * @Route("/rest/add-site")
     */
    public function addSiteAction(Request $request): object
    {
        $config = new Config();
        $em = $this->getDoctrine()->getManager();

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode($request->getContent());

        $message = '';
        $ok = false;

        $restUserPassword = $em->getRepository('App:User')
            ->isRestUserPassword($user, $password);
        if ($restUserPassword) {
            if (strlen($data->name) < 1) {
                $message .= 'Nazwa strony www musi zostać podana.' . "\r\n";
            } elseif (strlen($data->name) > 100) {
                $message .= 'Nazwa strony www może zawierać maksymalnie '
                    . '100 znaków.' . "\r\n";
            }
            $http = substr($data->url, 0, 7) != 'http://';
            $https = substr($data->url, 0, 8) != 'https://';
            if ($http && $https) {
                $message .= 'Url musi rozpoczynać się od znaków: http://'
                    . "\r\n";
            }
            if (strlen($data->url) > 100) {
                $message .= 'Url może zawierać maksymalnie 100 znaków.'
                    . "\r\n";
            }
            if ($message == '') {
                $site = new Site();
                $site->setUser($restUserPassword);
                $site->setActive(false);
                $site->setVisible(true);
                $site->setName($data->name);
                $site->setUrl($data->url);
                $site->setIpAdded($config->getRemoteAddress());
                $site->setDateAdded($config->getDateTimeNow());
                $site->setIpUpdated('');
                $site->setDateUpdated(new \DateTime('1970-01-01 00:00:00'));
                $em->persist($site);
                try {
                    $em->flush();
                    $message .= 'Strona www została dodana i oczekuje '
                        . 'na akceptację.' . "\r\n";
                    $ok = true;
                } catch (\Exception $e) {
                    $message .= 'Dodanie strony www nie powiodło się.'
                        . "\r\n";
                }
            }
        } else {
            $message .= 'Błędna autoryzacja przesyłanych danych.' . "\r\n";
        }

        $response = new Response();
        $response->message = $message;
        $response->success = $ok;

        return new JsonResponse($response);
    }

    /**
     * @Route("/rest/update-site")
     */
    public function updateSiteAction(Request $request): object
    {
        $config = new Config();
        $em = $this->getDoctrine()->getManager();

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode($request->getContent());

        $message = '';
        $ok = false;

        $restUserPassword = $em->getRepository('App:User')
            ->isRestUserPassword($user, $password);
        if ($restUserPassword) {
            $restUserSite = $em->getRepository('App:Site')
                ->isRestUserSite($restUserPassword->getId(), $data->id);
            if (!$restUserSite) {
                $message .= 'Baza nie zawiera podanej strony dla autoryzacji.'
                    . "\r\n";
            }
            if (strlen($data->name) < 1) {
                $message .= 'Nazwa strony www musi zostać podana.' . "\r\n";
            } elseif (strlen($data->name) > 100) {
                $message .= 'Nazwa strony www może zawierać maksymalnie '
                    . '100 znaków.' . "\r\n";
            }
            if ($message == '') {
                $siteData = $em->getRepository('App:Site')->setSiteData(
                    $data->id,
                    $data->visible,
                    $data->name,
                    $config->getRemoteAddress(),
                    $config->getDateTimeNow()
                );
                if ($siteData) {
                    $message .= 'Dane strony www zostały zapisane.'
                        . "\r\n";
                    $ok = true;
                } else {
                    $message .= 'Zapisanie danych strony www '
                        . 'nie powiodło się.' . "\r\n";
                }
            }
        } else {
            $message .= 'Błędna autoryzacja przesyłanych danych.' . "\r\n";
        }

        $response = new Response();
        $response->message = $message;
        $response->success = $ok;

        return new JsonResponse($response);
    }

    /**
     * @Route("/rest/delete-site")
     */
    public function deleteSiteAction(Request $request): object
    {
        $em = $this->getDoctrine()->getManager();

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode($request->getContent());

        $message = '';
        $ok = false;

        $restUserPassword = $em->getRepository('App:User')
            ->isRestUserPassword($user, $password);
        if ($restUserPassword) {
            $restUserSite = $em->getRepository('App:Site')
                ->isRestUserSite($restUserPassword->getId(), $data->id);
            if (!$restUserSite) {
                $message .= 'Baza nie zawiera podanej strony dla autoryzacji.'
                    . "\r\n";
            }
            if ($message == '') {
                $siteData = $em->getRepository('App:Site')
                    ->deleteSiteData($data->id);
                if ($siteData) {
                    $message .= 'Dane strony www zostały usunięte.' . "\r\n";
                    $ok = true;
                } else {
                    $message .= 'Usunięcie danych strony www nie powiodło się.'
                        . "\r\n";
                }
            }
        } else {
            $message .= 'Błędna autoryzacja przesyłanych danych.' . "\r\n";
        }

        $response = new Response();
        $response->message = $message;
        $response->success = $ok;

        return new JsonResponse($response);
    }
}
