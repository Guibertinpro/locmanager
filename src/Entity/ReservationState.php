<?php

namespace App\Entity;

use App\Repository\ReservationStateRepository;
use Doctrine\ORM\Mapping as ORM;

use function PHPUnit\Framework\isNull;

#[ORM\Entity(repositoryClass: ReservationStateRepository::class)]
class ReservationState
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
<<<<<<< HEAD
    private ?string $name = '';
=======
    private $name = '';
>>>>>>> 25ead8818f7e2304628c7e61b24dfea2ebafbfb4

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /* public function __toString()
    {
        return $this->getName();
    } */
}
