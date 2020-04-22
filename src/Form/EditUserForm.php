<?php

declare(strict_types=1);

namespace App\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints as Assert;

class EditUserForm
{
    protected EntityManager $em;
    protected int $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=30,
     *     maxMessage="Imię może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $name = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=50,
     *     maxMessage="Nazwisko może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $surname = null;

    /**
     * @Assert\Length(
     *     max=60,
     *     maxMessage="Ulica może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $street = null;

    /**
     * @Assert\Length(
     *     max=6,
     *     maxMessage="Kod pocztowy może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $postcode = null;

    protected ?int $province = null;
    protected ?int $city = null;

    /**
     * @Assert\Length(
     *     max=20,
     *     maxMessage="Telefon może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $phone = null;

    protected ?string $email = null;

    /**
     * @Assert\Length(
     *     max=180,
     *     maxMessage="Url strony www może zawierać maksymalnie {{ limit }} znaków."
     * )
     * @Assert\Url(message="Url strony www nie jest poprawny.")
     */
    protected ?string $url = null;

    /**
     * @Assert\Length(
     *     max=10000,
     *     maxMessage="Opis może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $description = null;

    /**
     * @Assert\Length(
     *     max=180,
     *     maxMessage="Nowy e-mail może zawierać maksymalnie {{ limit }} znaków."
     * )
     * @Assert\Email(
     *     message="Nowy e-mail musi mieć format zapisu: nazwisko@domena.pl"
     * )
     */
    protected ?string $newEmail = null;

    /**
     * @Assert\Length(
     *     max=180,
     *     maxMessage="Powtórzony e-mail może zawierać maksymalnie {{ limit }} znaków."
     * )
     * @Assert\Email(
     *     message="Powtórzony e-mail musi mieć format zapisu: nazwisko@domena.pl"
     * )
     */
    protected ?string $repeatEmail = null;

    protected ?string $login = null;

    /**
     * @Assert\Length(
     *     min=2,
     *     max=72,
     *     minMessage="Stare hasło musi zawierać minimalnie {{ limit }} znaki.",
     *     maxMessage="Stare hasło może zawierać maksymalnie {{ limit }} znaki."
     * )
     */
    protected ?string $password = null;

    /**
     * @Assert\Length(
     *     min=2,
     *     max=72,
     *     minMessage="Nowe hasło musi zawierać minimalnie {{ limit }} znaki.",
     *     maxMessage="Nowe hasło może zawierać maksymalnie {{ limit }} znaki."
     * )
     */
    protected ?string $newPassword = null;

    /**
     * @Assert\Length(
     *     min=2,
     *     max=72,
     *     minMessage="Powtórzone hasło musi zawierać minimalnie {{ limit }} znaki.",
     *     maxMessage="Powtórzone hasło może zawierać maksymalnie {{ limit }} znaki."
     * )
     */
    protected ?string $repeatPassword = null;

    public function __construct(EntityManager $em, int $user)
    {
        $this->em = $em;
        $this->user = $user;
    }

    /**
     * @Assert\IsFalse(
     *     message="Stare hasło nie jest zgodne z dotychczas istniejącym."
     * )
     */
    public function isOldPasswordValid(): bool
    {
        if ($this->password != '') {
            $idPassword = $this->em->getRepository('App:User')
                ->getIdPassword($this->user);
            $passwordVerify = password_verify(
                $this->password,
                $idPassword->getPassword()
            );
        }

        return $this->password != '' && !$passwordVerify;
    }

    /**
     * @Assert\IsFalse(message="Stare hasło nie zostało podane.")
     */
    public function isOldPasswordGiven(): bool
    {
        return $this->password == ''
            && ($this->newPassword != '' || $this->repeatPassword != '')
            && $this->newPassword == $this->repeatPassword;
    }

    /**
     * @Assert\IsFalse(
     *     message="Nowe hasło lub powtórzone hasło nie zostało podane."
     * )
     */
    public function isNewAndRepeatPasswordGiven(): bool
    {
        return $this->password != ''
            && ($this->newPassword == '' || $this->repeatPassword == '');
    }

    /**
     * @Assert\IsFalse(message="Nowe hasło i powtórzone hasło nie są zgodne.")
     */
    public function isPasswordEqual(): bool
    {
        return $this->newPassword != $this->repeatPassword;
    }

    /**
     * @Assert\IsFalse(message="Nowe hasło i stare hasło nie mogą być zgodne.")
     */
    public function isPasswordNotEqual(): bool
    {
        return $this->password != '' && $this->password == $this->newPassword;
    }

    /**
     * @Assert\IsFalse(message="Nowy e-mail i powtórzony e-mail nie są zgodne.")
     */
    public function isEmailEqual(): bool
    {
        return $this->newEmail != $this->repeatEmail;
    }

    /**
     * @Assert\IsFalse(
     *     message="Nowy e-mail i stary e-mail nie mogą być zgodne."
     * )
     */
    public function isEmailNotEqual(): bool
    {
        return $this->email != '' && $this->email == $this->newEmail;
    }

    /**
     * @Assert\IsFalse(message="Nowy e-mail jest już użyty.")
     */
    public function isNotUserEmail(): bool
    {
        if ($this->newEmail != '') {
            $userEmail = $this->em->getRepository('App:User')
                ->isUserEmail($this->newEmail);
        }

        return $this->newEmail != '' && $userEmail;
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

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setPostcode(?string $postcode): void
    {
        $this->postcode = $postcode;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setProvince(int $province): void
    {
        $this->province = $province;
    }

    public function getProvince(): ?int
    {
        return $this->province;
    }

    public function setCity(int $city): void
    {
        $this->city = $city;
    }

    public function getCity(): ?int
    {
        return $this->city;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setNewEmail(?string $newEmail): void
    {
        $this->newEmail = $newEmail;
    }

    public function getNewEmail(): ?string
    {
        return $this->newEmail;
    }

    public function setRepeatEmail(?string $repeatEmail): void
    {
        $this->repeatEmail = $repeatEmail;
    }

    public function getRepeatEmail(): ?string
    {
        return $this->repeatEmail;
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

    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setRepeatPassword(?string $repeatPassword): void
    {
        $this->repeatPassword = $repeatPassword;
    }

    public function getRepeatPassword(): ?string
    {
        return $this->repeatPassword;
    }
}
