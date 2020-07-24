<?php

declare(strict_types=1);

namespace App\Html;

class MessageHtml
{
    public function prepareMessage(string $message, bool $ok): string
    {
        return ($message !== '') ? '<p class="' . (($ok) ? 'ok' : 'bad') . '">'
            . str_replace("\n", '<br />', htmlspecialchars($message)) . '</p>'
            . "\n" : '';
    }
}
