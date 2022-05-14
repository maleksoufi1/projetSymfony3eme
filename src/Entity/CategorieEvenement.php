<?php

namespace App\Entity;

use App\Repository\CategorieEvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=CategorieEvenementRepository::class)
 
 */
class CategorieEvenement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank (message="Veuillez remplir ce champs")
     * @Assert\Length(min=5  )
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank (message="Veuillez remplir ce champs")
     
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="categorieEvenement",orphanRemoval=true)
     * @Assert\NotBlank (message="Veuillez remplir ce champs")
     */
    private $evenements;

    /**
     * @ORM\Column(type="string", length=255)
     
     
     */
    private $image;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }
    public function __toString()
    {
        return(string)$this->getLibelle();
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->setCategorieEvenement($this);
        }

        return $this;
    }

    public function removeEvenement(evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getCategorieEvenement() === $this) {
                $evenement->setCategorieEvenement(null);
            }
        }

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage( $image)   
    {
        $this->image = $image;

        return $this;
    }
}
