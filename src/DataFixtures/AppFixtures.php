<?php

namespace App\DataFixtures;

use App\Entity\Events;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    /*
     * @var Generator
     */
    private Generator $faker;
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Generation of user fixtures
        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->setFirstname($this->faker->firstName());
            $user->setLastname($this->faker->lastName());
            $user->setEmail($this->faker->email());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'pass'));

            $manager->persist($user);
        }

        $manager->flush();

        // Generation of event fixtures
        for ($i = 0; $i < 3; $i++) {
            $event = new Events();
            $event->setTitle($this->faker->word());
            $event->setContent($this->faker->paragraph(2));
            $event->setLocation($this->faker->city());

            $start_date = new DateTime();
            $startAt = $this->faker->dateTimeBetween($start_date, $start_date->modify('+5 hours'));
            $event->setStartAt(DateTimeImmutable::createFromMutable($startAt));
            $event->setEndAt(DateTimeImmutable::createFromMutable($startAt));

            $users = $manager->getRepository(User::class)->findAll();
            $randomUser = $this->faker->randomElement($users);

            if ($randomUser) { // check if user exists
                $event->setUser($randomUser);
            }

            $manager->persist($event);
        }

        $manager->flush();
    }
}
