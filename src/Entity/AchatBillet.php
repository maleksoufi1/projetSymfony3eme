<?php

namespace App\Entity;

use App\Repository\AchatBilletRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AchatBilletRepository::class)
 */
class AchatBillet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="achatBillets")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Billet::class, inversedBy="achatBillets")
     */
    private $billet;

    /**
     * @ORM\Column(type="integer")
     * * @Assert\NotBlank (message="Veuillez remplir ce champs")
     */
    private $quantite;

    /**
     * @ORM\Column(type="date")
     */
    private $dateAchat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBillet(): ?Billet
    {
        return $this->billet;
    }

    public function setBillet(?Billet $billet): self
    {
        $this->billet = $billet;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeInterface $dateAchat): self
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }
    public function __toString()
    {
        return(string)$this->getId();
    }
}
