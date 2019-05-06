<?php declare(strict_types=1);

// src/Form/Type/ContactFormFormType.php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    EmailType,
    ResetType,
    SubmitType,
    TextareaType,
    TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('email', EmailType::class, array('label' => 'E-mail:'))
            ->add('subject', TextType::class, array('label' => 'Temat:'))
            ->add('message', TextareaType::class, array(
                'label' => 'Wiadomość:',
                'attr' => array('style' => 'height: 220px;')
            ))
            ->add('save', SubmitType::class, array('label' => 'Wyślij'))
            ->add('reset', ResetType::class, array('label' => 'Wyczyść'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class'      => 'App\Form\ContactFormForm',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'contact_form_form_item'
        ));
    }
}
