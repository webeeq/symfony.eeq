<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, TextType};
use Symfony\Component\Form\FormBuilderInterface;

class RegisterUserFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('name', TextType::class, array('label' => 'Imię:'))
            ->add('surname', TextType::class, array('label' => 'Nazwisko:'))
            ->add('accept', CheckboxType::class, array('label' => 'Akceptuję'))
        ;
    }

    public function getParent(): string
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix(): string
    {
        return 'app_user_registration';
    }
}
