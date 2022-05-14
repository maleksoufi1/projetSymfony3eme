<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProgrammeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProgrammeRepository::class)
 */
class Programme
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titre;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;
    public function __toString()
    {
        return(string)$this->getTitre();
    }
    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $affiche;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $difficulte;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $duree;

    
    /**
     * @Assert\NotBlank
     * @ORM\OneToMany(targetEntity=SuiviProgramme::class, mappedBy="programme", cascade={"persist", "remove"})
     */
    private $suivisProgrammes;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity=CategorieProgramme::class, inversedBy="programmes")
     */
    private $categorieProgramme;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="programmes")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $jaime;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $jaimepas;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $abn;

    public function __construct()
    {
        $this->suivisProgrammes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAffiche(): ?string
    {
        return $this->affiche;
    }

    public function setAffiche(?string $affiche): self
    {
        $this->affiche = $affiche;

        return $this;
    }

    public function getDifficulte(): ?string
    {
        return $this->difficulte;
    }

    public function setDifficulte(?string $difficulte): self
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

   

    /**
     * @return Collection|suiviProgramme[]
     */
    public function getSuivisProgrammes(): Collection
    {
        return $this->suivisProgrammes;
    }

    public function addSuivisProgramme(suiviProgramme $suivisProgramme): self
    {
        if (!$this->suivisProgrammes->contains($suivisProgramme)) {
            $this->suivisProgrammes[] = $suivisProgramme;
            $suivisProgramme->setProgramme($this);
        }

        return $this;
    }

    public function removeSuivisProgramme(suiviProgramme $suivisProgramme): self
    {
        if ($this->suivisProgrammes->removeElement($suivisProgramme)) {
            // set the owning side to null (unless already changed)
            if ($suivisProgramme->getProgramme() === $this) {
                $suivisProgramme->setProgramme(null);
            }
        }

        return $this;
    }

    public function getCategorieProgramme(): ?CategorieProgramme
    {
        return $this->categorieProgramme;
    }

    public function setCategorieProgramme(?CategorieProgramme $categorieProgramme): self
    {
        $this->categorieProgramme = $categorieProgramme;

        return $this;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getJaime(): ?int
    {
        return $this->jaime;
    }

    public function setJaime(int $jaime): self
    {
        $this->jaime = $jaime;

        return $this;
    }

    public function getJaimepas(): ?int
    {
        return $this->jaimepas;
    }

    public function setJaimepas(int $jaimepas): self
    {
        $this->jaimepas = $jaimepas;

        return $this;
    }

    public function getAbn(): ?int
    {
        return $this->abn;
    }

    public function setAbn(?int $abn): self
    {
        $this->abn = $abn;

        return $this;
    }
}
