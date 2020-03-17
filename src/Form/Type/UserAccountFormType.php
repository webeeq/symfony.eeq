<?php

declare(strict_types=1);

// src/Form/Type/UserAccountFormType.php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    SubmitType,
    TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAccountFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('name', TextType::class, array('label' => 'Nazwa:'))
            ->add('url', TextType::class, array('label' => 'Url:'))
            ->add('save', SubmitType::class, array('label' => 'Dodaj'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class'      => 'App\Form\UserAccountForm',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'user_account_form_item'
        ));
    }
}
