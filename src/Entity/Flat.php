<?php

namespace App\Entity;

use App\Repository\FlatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FlatRepository::class)
 *  * @ORM\Table(
 *      name="flat",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"house_id","number"})
 *      }
 * )
 */
class Flat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=House::class, inversedBy="flats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $house;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHouse(): ?House
    {
        return $this->house;
    }

    public function setHouse(?House $house): self
    {
        $this->house = $house;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }
}
