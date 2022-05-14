<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\CategorieProgrammeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieProgrammeRepository::class)
 */
class CategorieProgramme
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
    private $libelle;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @Assert\NotBlank
     * @ORM\OneToMany(targetEntity=Programme::class, mappedBy="categorieProgramme")
     */
    private $programmes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $jaime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $jaimepas;
    public function __toString()
    {
        return(string)$this->getLibelle();
    }
    public function __construct()
    {
        $this->programmes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
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
     * @return Collection|programme[]
     */
    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function addProgramme(programme $programme): self
    {
        if (!$this->programmes->contains($programme)) {
            $this->programmes[] = $programme;
            $programme->setCategorieProgramme($this);
        }

        return $this;
    }

    public function removeProgramme(programme $programme): self
    {
        if ($this->programmes->removeElement($programme)) {
            // set the owning side to null (unless already changed)
            if ($programme->getCategorieProgramme() === $this) {
                $programme->setCategorieProgramme(null);
            }
        }

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

    public function setJaime(?int $jaime): self
    {
        $this->jaime = $jaime;

        return $this;
    }

    public function getJaimepas(): ?int
    {
        return $this->jaimepas;
    }

    public function setJaimepas(?int $jaimepas): self
    {
        $this->jaimepas = $jaimepas;

        return $this;
    }
}
