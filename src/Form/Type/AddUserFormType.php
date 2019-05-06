<?php declare(strict_types=1);

// src/Form/Type/AddUserFormType.php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType,
    EmailType,
    PasswordType,
    SubmitType,
    TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddUserFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('name', TextType::class, array('attr' => array(
                'maxlength' => 50
            )))
            ->add('surname', TextType::class, array('attr' => array(
                'maxlength' => 100
            )))
            ->add('login', TextType::class, array('attr' => array(
                'maxlength' => 20
            )))
            ->add('password', PasswordType::class, array('attr' => array(
                'maxlength' => 30
            )))
            ->add('repeatPassword', PasswordType::class, array('attr' => array(
                'maxlength' => 30
            )))
            ->add('email', EmailType::class, array('attr' => array(
                'maxlength' => 100
            )))
            ->add('repeatEmail', EmailType::class, array('attr' => array(
                'maxlength' => 100
            )))
            ->add('accept', CheckboxType::class)
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class'      => 'App\Form\AddUserForm',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'add_user_form_item'
        ));
    }
}
