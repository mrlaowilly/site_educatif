<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class BlogFixtures extends Fixture
{

    const BLOG_BLOG1 = 'blog1';
    const BLOG_BLOG2 = 'blog2';
    const BLOG_BLOG3 = 'blog3';
    const BLOG_BLOG4 = 'blog4';
    const BLOG_BLOG5 = 'blog5';
    const BLOG_BLOG6 = 'blog6';


    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $blog1 = new Blog();
        $blog1-> setTitle('Divers')
            ->setDescriptif('Le premier article diver')
            ->setAuthor('Pierre')
            ->setImageLink('image/diver.jpg');

        $manager->persist($blog1);

        $blog2 = new Blog();
        $blog2-> setTitle('Super Cars')
            ->setDescriptif('Le deuxieme article diver voiture ponpon')
            ->setAuthor('Paul')
            ->setImageLink('image/voiture.jpg');

        $manager->persist($blog2);

        $blog3 = new Blog();
        $blog3-> setTitle('ANIMAUX')
            ->setDescriptif('Le premier article diver canard')
            ->setAuthor('Jacque')
            ->setImageLink('image/animaux.jpg');

        $manager->persist($blog3);

        $blog4 = new Blog();
        $blog4-> setTitle('Cuisine')
            ->setDescriptif('Le git premier article cuisine')
            ->setAuthor('Pierre')
            ->setImageLink('image/food.jpg');

        $manager->persist($blog4);

        $blog5 = new Blog();
        $blog5-> setTitle('Sports nautique')
            ->setDescriptif('Article Sports nautique')
            ->setAuthor('Paul')
            ->setImageLink('image/sport.jpg');

        $manager->persist($blog5);

        $blog6 = new Blog();
        $blog6-> setTitle('Jeux video')
            ->setDescriptif('Le premier article jeux video')
            ->setAuthor('Jacque')
            ->setImageLink('image/jeu.jpg');

        $manager->persist($blog6);



        $manager->flush();

        $this->addReference(self::BLOG_BLOG1, $blog1);
        $this->addReference(self::BLOG_BLOG2, $blog2);
        $this->addReference(self::BLOG_BLOG3, $blog3);
        $this->addReference(self::BLOG_BLOG4, $blog4);
        $this->addReference(self::BLOG_BLOG5, $blog5);
        $this->addReference(self::BLOG_BLOG6, $blog6);

    }
}