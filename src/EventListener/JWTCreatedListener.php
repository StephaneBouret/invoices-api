<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function updateJwtData(JWTCreatedEvent $event)
    {
        // 1. Récupérer l'utilisateur (pour avoir son firstName et lastName)
        /** @var User */
        $user = $event->getUser();

        // 2. Enrichir les datas pour qu'elles contiennent ces données
        $data = $event->getData();
        $data['firstName'] = $user->getFirstName();
        $data['lastName'] = $user->getLastName();
        $data['id'] = $user->getId();

        $event->setData($data);
    }
}
