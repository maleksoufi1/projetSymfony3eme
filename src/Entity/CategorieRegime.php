<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\CategorieRegimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategorieRegimeRepository::class)
 */
class CategorieRegime
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("catRegime")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="libelle est obligatoire !")
     *  *  * @Assert\Length(
     *      min = 10,
     *      max = 30,
     *      minMessage = "Minimum {{ limit }} caractéres",
     *      maxMessage = "Maximum {{ limit }} caractéres"
     * )
     * @Groups("catRegime")
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="donner la déscription !")
     *  *  * @Assert\Length(
     *      min = 10,
     *      minMessage = "Minimum {{ limit }} caractéres"
     * )
     * @Groups("catRegime")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Regime::class, mappedBy="categorieRegime",orphanRemoval=true)
     * @Groups("catRegime")
     */
    private $regimes;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     */
    private $statcolor;

    public function __construct()
    {
        $this->regimes = new ArrayCollection();
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
     * @return Collection|regime[]
     */
    public function getRegimes(): Collection
    {
        return $this->regimes;
    }

    public function addRegime(regime $regime): self
    {
        if (!$this->regimes->contains($regime)) {
            $this->regimes[] = $regime;
            $regime->setCategorieRegime($this);
        }

        return $this;
    }

    public function removeRegime(regime $regime): self
    {
        if ($this->regimes->removeElement($regime)) {
            // set the owning side to null (unless already changed)
            if ($regime->getCategorieRegime() === $this) {
                $regime->setCategorieRegime(null);
            }
        }

        return $this;
    }

    public function getStatcolor(): ?string
    {
        return $this->statcolor;
    }

    public function setStatcolor(?string $statcolor): self
    {
        $this->statcolor = $statcolor;

        return $this;
    }
}
