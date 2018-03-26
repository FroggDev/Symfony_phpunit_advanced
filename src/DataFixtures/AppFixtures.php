<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        # create a new fake object with fr
        $fake = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {

            # create new contact
            $user = new Contact();

            # add fake informations
            $user->setFirstname($fake->firstName())
                ->setLastname($fake->lastName())
                ->setEmail($fake->freeEmail())
                ->setPhone($fake->phoneNumber());

            # set for ctreate in db
            $manager->persist($user);
        }

        # add to db
        $manager->flush();
    }
}