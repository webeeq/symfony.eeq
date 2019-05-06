<?php declare(strict_types=1);

// src/Form/LoginUserForm.php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class LoginUserForm
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=20,
     *     maxMessage="Login może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $login;

    /**
     * @Assert\Length(
     *     max=30,
     *     maxMessage="Hasło może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $password;

    protected $forget;
    protected $remember;

    /**
     * @Assert\IsFalse(message="Podaj login twojego konta.")
     */
    public function isLoginGiven(): bool
    {
        return $this->forget && $this->login == '';
    }

    /**
     * @Assert\IsFalse(message="Podaj login i hasło twojego konta.")
     */
    public function isLoginPasswordGiven(): bool
    {
        return !$this->forget && ($this->login == '' || $this->password == '');
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setForget(?bool $forget): void
    {
        $this->forget = $forget;
    }

    public function getForget(): ?bool
    {
        return $this->forget;
    }

    public function setRemember(?bool $remember): void
    {
        $this->remember = $remember;
    }

    public function getRemember(): ?bool
    {
        return $this->remember;
    }
}
