<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\SuiviProgrammeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SuiviProgrammeRepository::class)
 */
class SuiviProgramme
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="integer", nullable=true)
     */
    private $note;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $remarque;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity=Programme::class, inversedBy="suivisProgrammes")
     */
    private $programme;

    

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="suiviProgramme")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_de_seances;

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

    public function getProgramme(): ?Programme
    {
        return $this->programme;
    }

    public function setProgramme(?Programme $programme): self
    {
        $this->programme = $programme;

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

    public function getNombreDeSeances(): ?int
    {
        return $this->nombre_de_seances;
    }

    public function setNombreDeSeances(int $nombre_de_seances): self
    {
        $this->nombre_de_seances = $nombre_de_seances;

        return $this;
    }
}
