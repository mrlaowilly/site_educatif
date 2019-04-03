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


    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $blog1 = new Blog();
        $blog1-> setTitle('blog1')
            ->setDescriptif('Le premier blog')
            ->setAuthor('Pierre');

        $manager->persist($blog1);

        $blog2 = new Blog();
        $blog2-> setTitle('blog2')
            ->setDescriptif('Le deuxieme article diver voiture ponpon')
            ->setAuthor('Paul');

        $manager->persist($blog2);

        $blog3 = new Blog();
        $blog3-> setTitle('blog3')
            ->setDescriptif('Le premier article diver canard')
            ->setAuthor('Jacque');

        $manager->persist($blog3);


        $manager->flush();

        $this->addReference(self::BLOG_BLOG1, $blog1);
        $this->addReference(self::BLOG_BLOG2, $blog2);
        $this->addReference(self::BLOG_BLOG3, $blog3);

    }
}
