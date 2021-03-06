<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Page;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $options['user'];
        /* on recupere user qui a ete passer a lappel du formulaire
         * */
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class, [
                'attr'=>[
                  'class'=> 'ckeditor'
                ]
            ])
            ->add('preview', TextType::class)
            ->add('photo', FileType::class);

        if (in_array('ROLE_ADMIN', $user->getRoles())){ //test du role

            $builder
                ->add('blog', EntityType::class, [
                    'class' => Blog::class, // recupere l'entité blog
                    'choice_label' => 'title', // recupere le champs visible de l'entité
                ])
                ->add('user', EntityType::class, [
                    'class' => User::class, // recupere l'entité user
                    'choice_label' => 'email', // recupere le champs visible de l'entité
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
            'label_format' => 'page.%name%', //permet la traduction des champs des formulaires
            'user' => null, // pour ajouter l'option
        ]);
    }
}
