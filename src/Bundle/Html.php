<?php declare(strict_types=1);

// src/Bundle/Html.php
namespace App\Bundle;

class Html
{
    public static function prepareCitySelect(
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

    public static function prepareMessage(string $message, bool $ok): string
    {
        return ($message != '') ? '<p class="' . (($ok) ? 'ok' : 'bad') . '">'
            . str_replace("\r\n", '<br />', $message) . '</p>' . "\r\n" : '';
    }

    public static function preparePageNavigator(
        string $url,
        int $level,
        int $listLimit,
        int $count,
        int $levelLimit
    ): string {
        $pageNavigator = '';

        if ($count > $listLimit) {
            $minLevel = 1;
            $maxLevel = number_format($count / $listLimit, 0, '.', '');
            $number = number_format($count / $listLimit, 2, '.', '');
            $maxLevel = ($number > $maxLevel) ? $maxLevel + 1 : $maxLevel;
            $number = $level - $levelLimit;
            $fromLevel = ($number < $minLevel) ? $minLevel : $number;
            $number = $level + $levelLimit;
            $toLevel = ($number > $maxLevel) ? $maxLevel : $number;
            $previousLevel = $level - 1;
            $nextLevel = $level + 1;
            if ($maxLevel > $levelLimit) {
                $pageNavigator .= ($level > $minLevel) ? '<a href="' . $url
                    . $minLevel . '">...</a>' : '';
            }
            $pageNavigator .= ($level > $minLevel) ? '<a href="' . $url
                . $previousLevel . '">&nbsp;&laquo&nbsp;</a>' : '';
            for ($i = $fromLevel; $i <= $toLevel; $i++) {
                $pageNavigator .= ($i != $level) ? '<a href="' . $url
                    . $i . '">&nbsp;' . $i . '&nbsp;</a>' : '[' . $i . ']';
            }
            $pageNavigator .= ($level < $maxLevel) ? '<a href="' . $url
                . $nextLevel . '">&nbsp;&raquo;&nbsp;</a>' : '';
            if ($maxLevel > $levelLimit) {
                $pageNavigator .= ($level < $maxLevel) ? '<a href="' . $url
                    . $maxLevel . '">...</a>' : '';
            }
        }

        return $pageNavigator;
    }
}
