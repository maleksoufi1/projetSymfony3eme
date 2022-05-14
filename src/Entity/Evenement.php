<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
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
     
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank (message="Veuillez remplir ce champs")
     
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Billet::class, mappedBy="evenement",orphanRemoval=true)
     * @Assert\NotBlank (message="Veuillez remplir ce champs")
     
     */
    private $billets;

    /**
     * @ORM\ManyToOne(targetEntity=CategorieEvenement::class, inversedBy="evenements")
     * @Assert\NotBlank (message="Veuillez remplir ce champs")
     */
    private $categorieEvenement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank (message="Veuillez remplir ce champs")
     * @Assert\Range( min = "now")
     */
    private $horraire;

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="evenement_id", cascade={"persist", "remove"})
     */
    private $avis;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    

    public function __construct()
    {
        $this->billets = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function __toString()
    {
        return(string)$this->getTitre();
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

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
     * @return Collection|billet[]
     */
    public function getBillets(): Collection
    {
        return $this->billets;
    }

    public function addBillet(billet $billet): self
    {
        if (!$this->billets->contains($billet)) {
            $this->billets[] = $billet;
            $billet->setEvenement($this);
        }

        return $this;
    }

    public function removeBillet(billet $billet): self
    {
        if ($this->billets->removeElement($billet)) {
            // set the owning side to null (unless already changed)
            if ($billet->getEvenement() === $this) {
                $billet->setEvenement(null);
            }
        }

        return $this;
    }

    public function getCategorieEvenement(): ?CategorieEvenement
    {
        return $this->categorieEvenement;
    }

    public function setCategorieEvenement(?CategorieEvenement $categorieEvenement): self
    {
        $this->categorieEvenement = $categorieEvenement;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage(string $image)
    {
        $this->image = $image;

        return $this;
    }

    public function getHorraire(): ?\DateTimeInterface
    {
        return $this->horraire;
    }

    public function setHorraire(?\DateTimeInterface $horraire): self
    {
        $this->horraire = $horraire;

        return $this;
    }

    /**
     * @return Collection|Avis[]
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis[] = $avi;
            $avi->setEvenementId($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getEvenementId() === $this) {
                $avi->setEvenementId(null);
            }
        }

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    
}
