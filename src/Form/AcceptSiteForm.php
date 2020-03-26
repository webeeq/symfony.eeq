<?php

declare(strict_types=1);

// src/Form/AcceptSiteForm.php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class AcceptSiteForm
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

    protected ?bool $active = null;
    protected ?bool $visible = null;
    protected ?bool $delete = null;

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

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setDelete(bool $delete): void
    {
        $this->delete = $delete;
    }

    public function getDelete(): ?bool
    {
        return $this->delete;
    }
}
