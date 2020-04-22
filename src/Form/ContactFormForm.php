<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class ContactFormForm
{
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
    protected ?string $email = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=180,
     *     maxMessage="Temat może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $subject = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=10000,
     *     maxMessage="Wiadomość może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected ?string $message = null;

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
