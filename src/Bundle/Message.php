<?php declare(strict_types=1);

// src/Bundle/Message.php
namespace App\Bundle;

class Message
{
    protected $message;
    protected $ok;

    public function __construct()
    {
        $this->message = '';
        $this->ok = false;
    }

    public function addMessage(string $message): void
    {
        $this->message .= $message . "\r\n";
    }

    protected function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): ?array
    {
        $length = strlen($this->message);

        if ($length >= 2) {
            return explode("\r\n", substr($this->message, 0, ($length - 2)));
        }

        return null;
    }

    public function getStrMessage(): string
    {
        return $this->message;
    }

    public function isMessage(): bool
    {
        return ($this->message != '') ? true : false;
    }

    public function setOk(bool $ok): void
    {
        $this->ok = $ok;
    }

    public function getOk(): bool
    {
        return $this->ok;
    }
}
