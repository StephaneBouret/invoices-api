<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Repository\InvoiceRepository;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Invoice;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ApiPlatform\Validator\Exception\ValidationException as ExceptionValidationException;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class InvoiceChronoProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $innerProcessor, private Security $security, private ValidatorInterface $validator, private InvoiceRepository $invoiceRepository)
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof Invoice) {
            $user = $this->security->getUser();
            $nextChrono = $this->invoiceRepository->findNextChrono($user);
            $data->setChrono($nextChrono);

            if (empty($data->getSentAt())) {
                $data->setSentAt(new DateTimeImmutable());
            }

            $violations = $this->validator->validate($data);
            if (count($violations) > 0) {
                throw new ExceptionValidationException($violations);
            }
        }
        $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
