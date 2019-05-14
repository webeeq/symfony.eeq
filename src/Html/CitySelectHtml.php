<?php declare(strict_types=1);

// src/Html/CitySelectHtml.php
namespace App\Html;

class CitySelectHtml
{
    public function prepareCitySelect(
        array $cityList,
        int $selectedCity
    ): string {
        $citySelect =
            '<select id="edit_user_form_city" name="edit_user_form[city]">';
        $citySelect .= '<option value="0">&nbsp;</option>';
        foreach ($cityList as $city) {
            $citySelect .= '<option value="' . $city->getId() . '"'
                . (($city->getId() == $selectedCity) ? ' selected="selected"'
                : '') . '>' . $city->getName() . '</option>';
        }
        $citySelect .= '</select>';

        return $citySelect;
    }
}
