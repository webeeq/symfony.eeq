<?php declare(strict_types=1);

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
            ->add('name', TextType::class, array('attr' => array(
                'maxlength' => 50
            )))
            ->add('surname', TextType::class, array('attr' => array(
                'maxlength' => 100
            )))
            ->add('street', TextType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 30
                )
            ))
            ->add('postcode', TextType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 6
                )
            ))
            ->add('province', ChoiceType::class, array(
                'choices' => $provinceArray
            ))
            ->add('city', ChoiceType::class, array(
                'choices' => $cityArray
            ))
            ->add('phone', TextType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 12
                )
            ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    'readonly' => true,
                    'maxlength' => 100
                )
            ))
            ->add('url', TextType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 100
                )
            ))
            ->add('description', TextareaType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 65535,
                    'style' => 'height: 220px;'
                )
            ))
            ->add('newEmail', EmailType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 100
                )
            ))
            ->add('repeatEmail', EmailType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 100
                )
            ))
            ->add('login', TextType::class, array(
                'attr' => array(
                    'readonly' => true,
                    'maxlength' => 20
                )
            ))
            ->add('password', PasswordType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 30
                )
            ))
            ->add('newPassword', PasswordType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 30
                )
            ))
            ->add('repeatPassword', PasswordType::class, array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 30
                )
            ))
            ->add('save', SubmitType::class)
            ->add('reset', ResetType::class)
            ->add('save2', SubmitType::class)
            ->add('reset2', ResetType::class)
            ->add('save3', SubmitType::class)
            ->add('reset3', ResetType::class)
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
