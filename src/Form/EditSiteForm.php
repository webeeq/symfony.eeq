<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class EditSiteForm
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=180,
     *     maxMessage="Nazwa strony www może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $name = null;

    protected ?string $url = null;
    protected ?bool $visible = null;
    protected ?bool $delete = null;

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
