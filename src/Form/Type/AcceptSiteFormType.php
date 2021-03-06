<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType,
    ChoiceType,
    ResetType,
    SubmitType,
    TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AcceptSiteFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('name', TextType::class, array('label' => 'Nazwa:'))
            ->add('url', TextType::class, array('label' => 'Url:'))
            ->add('active', ChoiceType::class, array(
                'label' => 'Aktywna:',
                'choices' => array(
                    ' Tak ' => 1,
                    ' Nie ' => 0
                ),
                'expanded' => true
            ))
            ->add('visible', ChoiceType::class, array(
                'label' => 'Widoczna:',
                'choices' => array(
                    ' Tak ' => 1,
                    ' Nie ' => 0
                ),
                'expanded' => true
            ))
            ->add('delete', CheckboxType::class, array(
                'label' => 'Usuń stronę',
                'required' => false
            ))
            ->add('save', SubmitType::class, array('label' => 'Zatwierdź'))
            ->add('reset', ResetType::class, array('label' => 'Wyczyść'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class'      => 'App\Form\AcceptSiteForm',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'accept_site_form_item'
        ));
    }
}
