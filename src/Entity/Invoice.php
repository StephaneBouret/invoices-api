<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\InvoiceRepository;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\InvoiceIncrementationController;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Put(),
        new Delete(),
        new Post(
            name: 'invoice_increment',
            uriTemplate: '/invoices/{id}/increment',
            controller: InvoiceIncrementationController::class,
        )
    ],
    normalizationContext: ['groups' => ['invoices_read']],
    denormalizationContext: ['disable_type_enforcement' => true],
    paginationEnabled: false,
    paginationItemsPerPage: 20,
    order: ['sentAt' => 'DESC']
)]
#[ApiFilter(OrderFilter::class, properties: ['amount', 'sentAt'])]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invoices_read', 'customers_read', 'invoices_subresource'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le montant de la facture est obligatoire")]
    #[Assert\Type(type: 'numeric', message: "Le montant de la facture doit être un numérique !")]
    #[Groups(['invoices_read', 'customers_read', 'invoices_subresource'])]
    private ?float $amount = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date d'envoi doit être renseignée")]
    #[Assert\Type(type: \DateTimeInterface::class, message: "La date doit être au format YYYY-MM-DD")]
    #[Groups(['invoices_read', 'customers_read', 'invoices_subresource'])]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut de la facture doit être obligatoire")]
    #[Assert\Choice(choices: ['SENT', 'PAID', 'CANCELLED'], message: "Le statut doit être SENT, PAID ou CANCELLED")]
    #[Groups(['invoices_read', 'customers_read', 'invoices_subresource'])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Le client de la facture doit être renseigné")]
    #[Groups(['invoices_read'])]
    private ?Customer $customer = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Il faut absolument un chrono pour la facture")]
    #[Assert\Type(type: 'integer', message: "Le chrono doit être un nombre")]
    #[Groups(['invoices_read', 'customers_read', 'invoices_subresource'])]
    private ?int $chrono = null;

    #[Groups(['invoices_read', 'invoices_subresource'])]
    public function getUser(): User
    {
        return $this->customer->getUser();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono(int $chrono): static
    {
        $this->chrono = $chrono;

        return $this;
    }
}
