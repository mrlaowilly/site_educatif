<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PersonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $person1 = new Person();
        $person1->setFirstName( 'CHARLIE')
            ->setLastName('')
            ->setImage('')
            ->setDescription("Je m'appelle charlie et je m'appelle Lulu, on est sur M6, pour le hit machine !
            WESH ! WESH ! WESH ! WESH ! WESH ! WESH ! WESH ! WESH ! WESH ! WESH ! WESH ! WESH ! WESH ! ")
            ->setUser($this->getReference(UserFixtures::USER_USER1));
        $manager->persist($person1);

        $person2 = new Person();
        $person2->setFirstName( '')
            ->setLastName('')
            ->setImage('')
            ->setDescription("")
            ->setUser($this->getReference(UserFixtures::USER_USER2));
        $manager->persist($person2);

        $person3 = new Person();
        $person3->setFirstName( 'toto')
            ->setLastName('')
            ->setImage('')
            ->setDescription("")
            ->setUser($this->getReference(UserFixtures::USER_USER3));
        $manager->persist($person3);

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
        ];
    }
}
