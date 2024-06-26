<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[Vich\Uploadable]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endAt = null;

    #[ORM\ManyToOne(targetEntity: Apartment::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private Apartment $apartment;

    #[ORM\Column]
    private ?int $nbOfAdults = null;

    #[ORM\Column]
    private ?int $nbOfChildren = null;

    #[ORM\Column(length: 255)]
    private ?string $price = null;

    #[ORM\ManyToOne(targetEntity: ReservationState::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ReservationState $state;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'reservations', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private Client $client;

    #[ORM\Column(length: 50)]
    private ?string $arrhes = null;

    #[ORM\Column(length: 50)]
    private ?string $leftToPay = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateLeftToPay = null;

    #[ORM\OneToOne(mappedBy: 'reservation')]
    private ?ContractFile $contractFile = null;

    #[ORM\Column]
    private ?bool $cautionValidated = false;

    #[ORM\Column]
    private ?bool $arrhesValidated = false;

    #[ORM\Column]
    private ?bool $soldeValidated = false;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'contrats', fileNameProperty: 'pdfName', size: 'pdfSize')]
    private ?File $pdfFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $pdfName = null;

    #[ORM\Column(nullable: true)]
    private ?int $pdfSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getApartment(): ?Apartment
    {
        return $this->apartment;
    }

    public function setApartment(Apartment $apartment): self
    {
        $this->apartment = $apartment;

        return $this;
    }

    public function getNbOfAdults(): ?int
    {
        return $this->nbOfAdults;
    }

    public function setNbOfAdults(int $nbOfAdults): self
    {
        $this->nbOfAdults = $nbOfAdults;

        return $this;
    }

    public function getNbOfChildren(): ?int
    {
        return $this->nbOfChildren;
    }

    public function setNbOfChildren(int $nbOfChildren): self
    {
        $this->nbOfChildren = $nbOfChildren;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getState(): ?ReservationState
    {
        return $this->state;
    }

    public function setState(ReservationState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getArrhes(): ?string
    {
        return $this->arrhes;
    }

    public function setArrhes(string $arrhes): self
    {
        $this->arrhes = $arrhes;

        return $this;
    }

    public function getLeftToPay(): ?string
    {
        return $this->leftToPay;
    }

    public function setLeftToPay(string $leftToPay): self
    {
        $this->leftToPay = $leftToPay;

        return $this;
    }

    public function getDateLeftToPay(): ?\DateTimeInterface
    {
        return $this->dateLeftToPay;
    }

    public function setDateLeftToPay(?\DateTimeInterface $dateLeftToPay): self
    {
        $this->dateLeftToPay = $dateLeftToPay;

        return $this;
    }

    public function getContractFile(): ?ContractFile
    {
        return $this->contractFile;
    }

    public function setContractFile(?ContractFile $contractFile): self
    {
        // unset the owning side of the relation if necessary
        if ($contractFile === null && $this->contractFile !== null) {
            $this->contractFile->setReservation(null);
        }

        // set the owning side of the relation if necessary
        if ($contractFile !== null && $contractFile->getReservation() !== $this) {
            $contractFile->setReservation($this);
        }

        $this->contractFile = $contractFile;

        return $this;
    }

    public function isCautionValidated(): ?bool
    {
        return $this->cautionValidated;
    }

    public function setCautionValidated(bool $cautionValidated): self
    {
        $this->cautionValidated = $cautionValidated;

        return $this;
    }

    public function isArrhesValidated(): ?bool
    {
        return $this->arrhesValidated;
    }

    public function setArrhesValidated(bool $arrhesValidated): self
    {
        $this->arrhesValidated = $arrhesValidated;

        return $this;
    }

    public function isSoldeValidated(): ?bool
    {
        return $this->soldeValidated;
    }

    public function setSoldeValidated(bool $soldeValidated): self
    {
        $this->soldeValidated = $soldeValidated;

        return $this;
    }

    public function setPdfFile(?File $pdfFile = null): void
    {
        $this->pdfFile = $pdfFile;

        if (null !== $pdfFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPdfFile(): ?File
    {
        return $this->pdfFile;
    }

    public function setPdfName(?string $pdfName): void
    {
        $this->pdfName = $pdfName;
    }

    public function getPdfName(): ?string
    {
        return $this->pdfName;
    }

    public function setPdfSize(?int $pdfSize): void
    {
        $this->pdfSize = $pdfSize;
    }

    public function getPdfSize(): ?int
    {
        return $this->pdfSize;
    }

    public function setUploadedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

}