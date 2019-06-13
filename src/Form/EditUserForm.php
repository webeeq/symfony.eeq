<?php declare(strict_types=1);

// src/Form/EditUserForm.php
namespace App\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints as Assert;

class EditUserForm
{
    protected $em;
    protected $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=30,
     *     maxMessage="Imię może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=50,
     *     maxMessage="Nazwisko może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $surname;

    /**
     * @Assert\Length(
     *     max=60,
     *     maxMessage="Ulica może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $street;

    /**
     * @Assert\Length(
     *     max=6,
     *     maxMessage="Kod pocztowy może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $postcode;

    protected $province;
    protected $city;

    /**
     * @Assert\Length(
     *     max=20,
     *     maxMessage="Telefon może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $phone;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=180,
     *     maxMessage="E-mail może zawierać maksymalnie {{ limit }} znaków."
     * )
     * @Assert\Email(
     *     message="E-mail musi mieć format zapisu: nazwisko@domena.pl"
     * )
     */
    protected $email;

    /**
     * @Assert\Length(
     *     max=180,
     *     maxMessage="Url strony www może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $url;

    /**
     * @Assert\Length(
     *     max=10000,
     *     maxMessage="Opis może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $description;

    /**
     * @Assert\Length(
     *     max=180,
     *     maxMessage="Nowy e-mail może zawierać maksymalnie {{ limit }} znaków."
     * )
     * @Assert\Email(
     *     message="Nowy e-mail musi mieć format zapisu: nazwisko@domena.pl"
     * )
     */
    protected $newEmail;

    /**
     * @Assert\Length(
     *     max=180,
     *     maxMessage="Powtórzony e-mail może zawierać maksymalnie {{ limit }} znaków."
     * )
     * @Assert\Email(
     *     message="Powtórzony e-mail musi mieć format zapisu: nazwisko@domena.pl"
     * )
     */
    protected $repeatEmail;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="Login musi zawierać minimalnie {{ limit }} znaki.",
     *     maxMessage="Login może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $login;

    /**
     * @Assert\Length(
     *     min=2,
     *     max=72,
     *     minMessage="Stare hasło musi zawierać minimalnie {{ limit }} znaki.",
     *     maxMessage="Stare hasło może zawierać maksymalnie {{ limit }} znaki."
     * )
     */
    protected $password;

    /**
     * @Assert\Length(
     *     min=2,
     *     max=72,
     *     minMessage="Nowe hasło musi zawierać minimalnie {{ limit }} znaki.",
     *     maxMessage="Nowe hasło może zawierać maksymalnie {{ limit }} znaki."
     * )
     */
    protected $newPassword;

    /**
     * @Assert\Length(
     *     min=2,
     *     max=72,
     *     minMessage="Powtórzone hasło musi zawierać minimalnie {{ limit }} znaki.",
     *     maxMessage="Powtórzone hasło może zawierać maksymalnie {{ limit }} znaki."
     * )
     */
    protected $repeatPassword;

    public function __construct(EntityManager $em, int $user)
    {
        $this->em = $em;
        $this->user = $user;
    }

    /**
     * @Assert\IsTrue(message="Login może składać się tylko z liter i cyfr.")
     */
    public function isLoginValid(): int
    {
        return preg_match('/^([0-9A-Za-z]*)$/', $this->login);
    }

    /**
     * @Assert\IsTrue(
     *     message="Stare hasło może składać się tylko z liter i cyfr."
     * )
     */
    public function isPasswordValid(): int
    {
        return preg_match(
            '/^([!@#$%^&*()0-9A-Za-z]*)$/',
            $this->password ?? ''
        );
    }

    /**
     * @Assert\IsTrue(
     *     message="Nowe hasło może składać się tylko z liter i cyfr."
     * )
     */
    public function isNewPasswordValid(): int
    {
        return preg_match(
            '/^([!@#$%^&*()0-9A-Za-z]*)$/',
            $this->newPassword ?? ''
        );
    }

    /**
     * @Assert\IsTrue(
     *     message="Powtórzone hasło może składać się tylko z liter i cyfr."
     * )
     */
    public function isRepeatPasswordValid(): int
    {
        return preg_match(
            '/^([!@#$%^&*()0-9A-Za-z]*)$/',
            $this->repeatPassword ?? ''
        );
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
        $oldPasswordGiven = $this->password == ''
            && ($this->newPassword != '' || $this->repeatPassword != '')
            && $this->newPassword == $this->repeatPassword;

        return $oldPasswordGiven;
    }

    /**
     * @Assert\IsFalse(
     *     message="Nowe hasło lub powtórzone hasło nie zostało podane."
     * )
     */
    public function isNewOrRepeatPasswordGiven(): bool
    {
        $newOrRepeatPasswordGiven = $this->password != ''
            && ($this->newPassword == '' || $this->repeatPassword == '');

        return $newOrRepeatPasswordGiven;
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
        $passwordNotEqual = $this->password != ''
            && $this->password == $this->newPassword;

        return $passwordNotEqual;
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
     * @Assert\IsFalse(
     *     message="Strona www musi rozpoczynać się od znaków: http://"
     * )
     */
    public function isUrlValid(): bool
    {
        $urlValid = $this->url != ''
            && substr($this->url, 0, 7) != 'http://'
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
