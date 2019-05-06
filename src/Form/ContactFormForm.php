<?php declare(strict_types=1);

// src/Form/ContactFormForm.php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class ContactFormForm
{
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
     *     maxMessage="Temat może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $subject;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=10000,
     *     maxMessage="Wiadomość może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $message;

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
