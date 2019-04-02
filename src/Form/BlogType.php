<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class) // definition du type de champs pour le formulaire
            ->add('updatedAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('descriptif', TextareaType::class)
            ->add('author', TextType::class)
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)  //  ppaser les options aux formulaire
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
            'label_format' => 'blog.%name%',  //permet la traduction des champas des formulaire
        ]);
    }
}
