<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_password', RepeatedType::class, [
                'type'             => PasswordType::class,
                'mapped'           => false,
                'invalid_message'  => 'Les mots de passe sont différents',
                'required'         => true,
                'first_options'    => [
                    'label' => 'Nouveau mot de passe',
                    'attr'  => ['placeholder' => 'Nouveau mot de passe']
                ],
                'second_options'   => [
                    'label' => 'Confirmer le mot de passe',
                    'attr'  => ['placeholder' => 'Confirmer le mot de passe']
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour',
                'attr'  => ['class' => 'btn btn-info btn-block']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}