<?php declare(strict_types=1);

// src/Form/Type/LoginUserFormType.php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType,
    PasswordType,
    SubmitType,
    TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginUserFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('login', TextType::class, array('attr' => array(
                'maxlength' => 20
            )))
            ->add('password', PasswordType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 30
                )
            ))
            ->add('forget', CheckboxType::class, array('required' => false))
            ->add('remember', CheckboxType::class, array('required' => false))
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class'      => 'App\Form\LoginUserForm',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'login_user_form_item'
        ));
    }
}
