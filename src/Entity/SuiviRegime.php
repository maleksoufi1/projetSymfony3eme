<?php

namespace App\Entity;

use App\Repository\SuiviRegimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SuiviRegimeRepository::class)
 */
class SuiviRegime
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $remarque;

    /**
     * @ORM\ManyToOne(targetEntity=Regime::class, inversedBy="suivisRegimes")
     */
    private $regime;

    
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="suiviRegime")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Calendar::class, mappedBy="suiviRegime",orphanRemoval=true)
     */
    private $calendars;

    /**
     * @ORM\Column(type="string", length=255 , nullable=true)
     */
    private $titre;

    public function __construct()
    {
        $this->calendars = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(?string $remarque): self
    {
        $this->remarque = $remarque;

        return $this;
    }

    public function getRegime(): ?Regime
    {
        return $this->regime;
    }

    public function setRegime(?Regime $regime): self
    {
        $this->regime = $regime;

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

    /**
     * @return Collection|Calendar[]
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): self
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars[] = $calendar;
            $calendar->setSuiviRegime($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): self
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getSuiviRegime() === $this) {
                $calendar->setSuiviRegime(null);
            }
        }

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function __toString()
    {
        return(string)$this->getTitre();
    }


}
