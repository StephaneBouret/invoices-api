<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckEmailController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $em)
    {}

    public function __invoke($email)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        // if (!$user) {
        //     throw $this->createNotFoundException("L'utilisateur n'existe pas !");
        // }
        return $user;
    }
}