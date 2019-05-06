<?php declare(strict_types=1);

// src/Bundle/Config.php
namespace App\Bundle;

class Config
{
    protected $url;
    protected $serverName;
    protected $serverDomain;
    protected $remoteAddress;
    protected $dateTimeNow;
    protected $adminEmail;
    protected $cookieLogin;

    public function __construct()
    {
        $this->url = 'http' . (($_SERVER['SERVER_PORT'] == 443) ? 's' : '')
            . '://' . $_SERVER['HTTP_HOST'];
        $this->serverName = $_SERVER['SERVER_NAME'];
        $this->serverDomain = str_replace('www.', '', $this->serverName);
        $this->remoteAddress = $_SERVER['REMOTE_ADDR'];
        $this->dateTimeNow = new \DateTime('now');
        $this->adminEmail = 'symfony20160510@gmail.com';
        try {
            $this->cookieLogin = $_COOKIE['login'];
        } catch (\Exception $e) {
            $this->cookieLogin = '';
        }
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setServerName(string $serverName): void
    {
        $this->serverName = $serverName;
    }

    public function getServerName(): string
    {
        return $this->serverName;
    }

    public function setServerDomain(string $serverDomain): void
    {
        $this->serverDomain = $serverDomain;
    }

    public function getServerDomain(): string
    {
        return $this->serverDomain;
    }

    public function setRemoteAddress(string $remoteAddress): void
    {
        $this->remoteAddress = $remoteAddress;
    }

    public function getRemoteAddress(): string
    {
        return $this->remoteAddress;
    }

    public function setDateTimeNow(object $dateTimeNow): void
    {
        $this->dateTimeNow = $dateTimeNow;
    }

    public function getDateTimeNow(): object
    {
        return $this->dateTimeNow;
    }

    public function setAdminEmail(string $adminEmail): void
    {
        $this->adminEmail = $adminEmail;
    }

    public function getAdminEmail(): string
    {
        return $this->adminEmail;
    }

    public function setCookieLogin(string $cookieLogin): void
    {
        $this->cookieLogin = $cookieLogin;
    }

    public function getCookieLogin(): string
    {
        return $this->cookieLogin;
    }
}
