<?php

namespace App\State;

use App\Entity\Customer;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ApiPlatform\Validator\Exception\ValidationException as ExceptionValidationException;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class CustomerUserProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $innerProcessor, private Security $security, private ValidatorInterface $validator) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof Customer) {
            $user = $this->security->getUser();

            if (!$user) {
                throw new AccessDeniedException('Un utilisateur connecté est requis pour cette opération.');
            }

            $data->setUser($user);

            $violations = $this->validator->validate($data);
            if (count($violations) > 0) {
                throw new ExceptionValidationException($violations);
            }
        }
        $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
