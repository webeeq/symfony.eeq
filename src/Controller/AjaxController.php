<?php declare(strict_types=1);

// src/Controller/AjaxController.php
namespace App\Controller;

use App\Bundle\Html;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};

class AjaxController extends Controller
{
    public function cityListAction(Request $request): object
    {
        $selectedProvince = (int) $request->get('inData');
        $selectedCity = (int) $request->get('inData2');

        $cityList = $this->getDoctrine()
            ->getRepository('App:City')
            ->getCityList($selectedProvince);

        $response = array(
            'code' => 100,
            'success' => true,
            'outData' => Html::prepareCitySelect($cityList, $selectedCity)
        );

        return new JsonResponse($response);
    }
}
