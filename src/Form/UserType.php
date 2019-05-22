<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
              'constraints' => [
                new Assert\Email([
            'message' => 'Votre email "{{ value }}" est pas invalide.',
            'checkMX' => true,
            ])]

          ])
            ->add('roles', CollectionType::class, [
                'allow_add' => true
            ])
            //ajout d'un password à verifier
          ->add('password', RepeatedType::class, [
              'type' => PasswordType::class,
              'invalid_message' => 'Le mot de passe doit être le même.',
              'options' => ['attr' => ['class' => 'password-field']],
              'required' => true,
              'first_options'  => ['label' => 'Password'],
              'second_options' => ['label' => 'Repeat Password'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
