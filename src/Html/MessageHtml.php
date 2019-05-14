<?php declare(strict_types=1);

// src/Html/MessageHtml.php
namespace App\Html;

class MessageHtml
{
    public function prepareMessage(string $message, bool $ok): string
    {
        return ($message != '') ? '<p class="' . (($ok) ? 'ok' : 'bad') . '">'
            . str_replace("\r\n", '<br />', $message) . '</p>' . "\r\n" : '';
    }
}
