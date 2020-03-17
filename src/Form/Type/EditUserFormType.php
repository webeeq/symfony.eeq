<?php

declare(strict_types=1);

// src/Form/Type/EditUserFormType.php
namespace App\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    ChoiceType,
    EmailType,
    PasswordType,
    ResetType,
    SubmitType,
    TextareaType,
    TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserFormType extends AbstractType
{
    protected static $em;
    protected static $province;

    public static function init(
        EntityManagerInterface $em,
        int $province
    ): void {
        self::$em = $em;
        self::$province = $province;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $provinceArray = array();
        $cityArray = array();

        $provinceList = self::$em->getRepository('App:Province')
            ->getProvinceList();
        $cityList = self::$em->getRepository('App:City')
            ->getCityList(self::$province);

        $provinceArray[' '] = 0;
        foreach ($provinceList as $province) {
            $provinceArray[$province->getName()] = $province->getId();
        }
        $cityArray[' '] = 0;
        foreach ($cityList as $city) {
            $cityArray[$city->getName()] = $city->getId();
        }

        $builder
            ->add('name', TextType::class, array('label' => 'Imię:'))
            ->add('surname', TextType::class, array('label' => 'Nazwisko:'))
            ->add('street', TextType::class, array(
                'label' => 'Ulica:',
                'required' => false
            ))
            ->add('postcode', TextType::class, array(
                'label' => 'Kod pocztowy:',
                'required' => false
            ))
            ->add('province', ChoiceType::class, array(
                'label' => 'Województwo:',
                'choices' => $provinceArray
            ))
            ->add('city', ChoiceType::class, array(
                'label' => 'Miasto:',
                'choices' => $cityArray
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Telefon:',
                'required' => false
            ))
            ->add('email', EmailType::class, array(
                'label' => 'E-mail:',
                'attr' => array('readonly' => true)
            ))
            ->add('url', TextType::class, array(
                'label' => 'Strona www:',
                'required' => false
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Opis:',
                'required' => false,
                'attr' => array('style' => 'height: 220px;')
            ))
            ->add('newEmail', EmailType::class, array(
                'label' => 'Nowy e-mail:',
                'required' => false
            ))
            ->add('repeatEmail', EmailType::class, array(
                'label' => 'Powtórz e-mail:',
                'required' => false
            ))
            ->add('login', TextType::class, array(
                'label' => 'Login:',
                'attr' => array('readonly' => true)
            ))
            ->add('password', PasswordType::class, array(
                'label' => 'Stare hasło:',
                'required' => false
            ))
            ->add('newPassword', PasswordType::class, array(
                'label' => 'Nowe hasło:',
                'required' => false
            ))
            ->add('repeatPassword', PasswordType::class, array(
                'label' => 'Powtórz hasło:',
                'required' => false
            ))
            ->add('save', SubmitType::class, array('label' => 'Zatwierdź'))
            ->add('reset', ResetType::class, array('label' => 'Wyczyść'))
            ->add('save2', SubmitType::class, array('label' => 'Zatwierdź'))
            ->add('reset2', ResetType::class, array('label' => 'Wyczyść'))
            ->add('save3', SubmitType::class, array('label' => 'Zatwierdź'))
            ->add('reset3', ResetType::class, array('label' => 'Wyczyść'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class'      => 'App\Form\EditUserForm',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'edit_user_form_item'
        ));
    }
}
