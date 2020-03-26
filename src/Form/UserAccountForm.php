<?php

declare(strict_types=1);

// src/Form/UserAccountForm.php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class UserAccountForm
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=180,
     *     maxMessage="Nazwa strony www może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $name = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=180,
     *     maxMessage="Url strony www może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $url = null;

    /**
     * @Assert\IsFalse(message="Url musi rozpoczynać się od znaków: http://")
     */
    public function isUrlValid(): bool
    {
        $urlValid = substr($this->url, 0, 7) != 'http://'
            && substr($this->url, 0, 8) != 'https://';

        return $urlValid;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
}
