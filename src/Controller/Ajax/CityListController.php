<?php

declare(strict_types=1);

namespace App\Controller\Ajax;

use App\Html\CitySelectHtml;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};

class CityListController extends Controller
{
    public function cityListAction(Request $request): object
    {
        $html = new CitySelectHtml();

        $selectedProvince = (int) $request->get('inData');
        $selectedCity = (int) $request->get('inData2');

        $cityList = $this->getDoctrine()
            ->getRepository('App:City')
            ->getCityList($selectedProvince);

        $response = array(
            'code' => 100,
            'success' => true,
            'outData' => $html->prepareCitySelect($cityList, $selectedCity)
        );

        return new JsonResponse($response);
    }
}
