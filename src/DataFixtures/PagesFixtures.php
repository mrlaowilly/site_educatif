<?php

namespace App\DataFixtures;

use App\Entity\Page;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PagesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
//         $product = new Product();
//         $manager->persist($product);


        $pages1 = new Page();
        $pages1->setTitle( 'Les poissons')
            ->setContent('Du texte sur les écailles')
            ->setUser($this->getReference(UserFixtures::USER_USER1))
            ->setBlog($this->getReference(BlogFixtures::BLOG_BLOG1));
        $manager->persist($pages1);

        $pages2 = new Page();
        $pages2->setTitle( 'Les serpents')
            ->setContent('Du texte à propos des serpents')
            ->setUser($this->getReference(UserFixtures::USER_USER2))
            ->setBlog($this->getReference(BlogFixtures::BLOG_BLOG2));
        $manager->persist($pages2);

        $pages3 = new Page();
        $pages3->setTitle( 'les dinosaures')
            ->setContent('Du texte à propos des lezards')
            ->setUser($this->getReference(UserFixtures::USER_USER3))
            ->setBlog($this->getReference(BlogFixtures::BLOG_BLOG3));
        $manager->persist($pages3);



        $manager->flush();

    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            BlogFixtures::class,
        ];
    }
}