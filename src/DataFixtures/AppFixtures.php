<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Invoice;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(protected UserPasswordHasherInterface $passwordHasher)
    {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($u=0; $u < 10; $u++) { 
            $user = new User;
            $chrono = 1;
            $hash = $this->passwordHasher->hashPassword($user, "password");
            $user->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($hash);

            $manager->persist($user);

            for ($c = 0; $c < mt_rand(5, 20); $c++) {
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                        ->setLastName($faker->lastName)
                        ->setCompany($faker->company)
                        ->setEmail($faker->email)
                        ->setUser($user);

                $manager->persist($customer);

                for ($i=0; $i < mt_rand(3, 10); $i++) { 
                    $invoice = new Invoice;
                    $invoice->setAmount($faker->randomFloat(2, 250, 5000))
                            ->setSentAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')))
                            ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                            ->setCustomer($customer)
                            ->setChrono($chrono);

                    $chrono++;

                    $manager->persist($invoice);
                }
            }
        }

        $manager->flush();
    }
}
