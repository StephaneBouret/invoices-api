<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CustomerRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['customers_read']],
    denormalizationContext: ['groups' => ['customers_write']],
    operations: [
        new Get(normalizationContext: ['groups' => ['customers_read']]),
        new Post(),
        new Put(),
        new Delete(),
        new GetCollection(),
    ],
    paginationItemsPerPage: 10,
)]
#[ApiResource(
    uriTemplate: '/customers/{id}/invoices',
    operations: [new GetCollection()],
    normalizationContext: [
        'groups' => ['customers_read'],
    ],
)]
#[ApiFilter(SearchFilter::class, properties: ['firstName' => 'partial', 'lastName' => 'partial', 'email' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['lastName', 'firstName'], arguments: ['orderParameterName' => 'order'])]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["customers_read", "customers_write", "invoices_read", "invoices_write"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom du customer est obligatoire')]
    #[Assert\Length(
        min: 3,
        minMessage: 'Le prénom doit faire entre 3 et 255 caractères',
        max: 255,
        maxMessage: 'Le prénom doit faire entre 3 et 255 caractères'
    )]
    #[Groups(["customers_read", "customers_write", "invoices_read", "invoices_write"])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de famille du customer est obligatoire')]
    #[Assert\Length(
        min: 3,
        minMessage: 'Le nom de famille doit faire entre 3 et 255 caractères',
        max: 255,
        maxMessage: 'Le nom de famille doit faire entre 3 et 255 caractères'
    )]
    #[Groups(["customers_read", "customers_write", "invoices_read", "invoices_write"])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'adresse email du customer est obligatoire')]
    #[Assert\Email(message: 'Le format de l\'adresse email doit être valide')]
    #[Groups(["customers_read", "customers_write", "invoices_read", "invoices_write"])]
    private ?string $email = null;

    /**
     * @var Collection<int, Invoice>
     */
    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'customer')]
    #[Groups(["customers_read"])]
    private Collection $invoices;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    // #[Assert\NotBlank(message: 'L\'utilisateur est obligatoire')]
    #[Groups(["customers_read", "customers_write"])]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["customers_read", "customers_write", "invoices_read", "invoices_write"])]
    private ?string $company = null;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    #[Groups(['customers_read'])]
    public function getTotalAmount(): float
    {
        return array_reduce($this->invoices->toArray(), fn($total, $invoice) => $total + $invoice->getAmount(), 0.0);
    }

    #[Groups(['customers_read'])]
    public function getUnpaidAmount(): float
    {
        return array_reduce($this->invoices->toArray(), fn($total, $invoice) => $total + (($invoice->getStatus() === 'PAID' || $invoice->getStatus() === 'CANCELLED') ? 0 : $invoice->getAmount()), 0.0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

        return $this;
    }
}
