<?php declare(strict_types=1);

// src/Form/AddUserForm.php
namespace App\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints as Assert;

class AddUserForm
{
    protected $em;
    protected $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=50,
     *     maxMessage="Imię może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=100,
     *     maxMessage="Nazwisko może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $surname;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=3,
     *     max=20,
     *     minMessage="Login musi zawierać minimalnie {{ limit }} znaki.",
     *     maxMessage="Login może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $login;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=8,
     *     max=30,
     *     minMessage="Hasło musi zawierać minimalnie {{ limit }} znaków.",
     *     maxMessage="Hasło może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=8,
     *     max=30,
     *     minMessage="Powtórzone hasło musi zawierać minimalnie {{ limit }} znaków.",
     *     maxMessage="Powtórzone hasło może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $repeatPassword;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=100,
     *     maxMessage="E-mail może zawierać maksymalnie {{ limit }} znaków."
     * )
     * @Assert\Email(
     *     message="E-mail musi mieć format zapisu: nazwisko@domena.pl"
     * )
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=100,
     *     maxMessage="Powtórzony e-mail może zawierać maksymalnie {{ limit }} znaków."
     * )
     * @Assert\Email(
     *     message="Powtórzony e-mail musi mieć format zapisu: nazwisko@domena.pl"
     * )
     */
    protected $repeatEmail;

    /**
     * @Assert\NotBlank()
     * @Assert\IsTrue(message="Musisz zaakceptować regulamin serwisu.")
     */
    protected $accept;

    public function __construct(EntityManager $em, string $user)
    {
        $this->em = $em;
        $this->user = $user;
    }

    /**
     * @Assert\IsTrue(message="Konto o podanym loginie już istnieje.")
     */
    public function isUserLogin(): bool
    {
        $userLogin = $this->em->getRepository('App:User')
            ->isUserLogin($this->user);

        return !$userLogin;
    }

    /**
     * @Assert\IsTrue(message="Login może składać się tylko z liter i cyfr.")
     */
    public function isLoginValid(): int
    {
        return preg_match('/^([0-9A-Za-z]*)$/', $this->login);
    }

    /**
     * @Assert\IsTrue(message="Hasło może składać się tylko z liter i cyfr.")
     */
    public function isPasswordValid(): int
    {
        return preg_match('/^([0-9A-Za-z]*)$/', $this->password);
    }

    /**
     * @Assert\IsFalse(message="Hasło i powtórzone hasło nie są zgodne.")
     */
    public function isPasswordEqual(): bool
    {
        return $this->password !== $this->repeatPassword;
    }

    /**
     * @Assert\IsFalse(message="E-mail i powtórzony e-mail nie są zgodne.")
     */
    public function isEmailEqual(): bool
    {
        return $this->email !== $this->repeatEmail;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setRepeatPassword(string $repeatPassword): void
    {
        $this->repeatPassword = $repeatPassword;
    }

    public function getRepeatPassword(): ?string
    {
        return $this->repeatPassword;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setRepeatEmail(string $repeatEmail): void
    {
        $this->repeatEmail = $repeatEmail;
    }

    public function getRepeatEmail(): ?string
    {
        return $this->repeatEmail;
    }

    public function setAccept(bool $accept): void
    {
        $this->accept = $accept;
    }

    public function getAccept(): ?bool
    {
        return $this->accept;
    }
}
