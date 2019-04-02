<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    const USER_USER1 = 'user1';
    const USER_USER2 = 'user2';
    const USER_USER3 = 'user3';

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $user1 = new User();
        $user1->setEmail('user1@hotmail.fr')
            ->setPassword($this->passwordEncoder->encodePassword(
                $user1,
                'password1'
            ))
                ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@hotmail.fr')
            ->setPassword($this->passwordEncoder->encodePassword(
                $user2,
                'password2'
            ))
            ->setRoles(['ROLE_USER']);

        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('user3@hotmail.fr')
            ->setPassword($this->passwordEncoder->encodePassword(
                $user3,
                'password3'
            ))
            ->setRoles(['ROLE_USER']);

        $manager->persist($user3);

        $manager->flush();

        $this->addReference(self::USER_USER1, $user1);
        $this->addReference(self::USER_USER2, $user2);
        $this->addReference(self::USER_USER3, $user3);
    }
}
