<?php

namespace App\DataFixtures;

use App\Entity\User;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends BaseFixture
{
    private $passwordEncoder;
    
    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
         $this->passwordEncoder = $passwordEncoder;
    }
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_users', function($i) {
            $user = new User();
            $user->setEmail(sprintf('spacebar%d@example.com', $i));
            $user->setNom($this->faker->firstName);

            $user->setPassword($this->passwordEncoder->hashPassword(
                $user,
                'engage'
            ));
            return $user;
        });
        $this->createMany(3, 'admin_users', function($i) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@thespacebar.com', $i));
            $user->setNom($this->faker->firstName);

            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($this->passwordEncoder->hashPassword(
                $user,
                'engage'
            ));
            return $user;

        }); 
        $manager->flush();
    }
}
