<?php

namespace App\Entity;

use App\Repository\ForumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ForumRepository::class)
 */
class Forum
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Titre is required")
     */
    private $titre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * *@Assert\NotBlank(message="Nbr de participant is required")
     */
    private $nbrParticipant;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="forum")
     */
    private $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
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

    public function getNbrParticipant(): ?int
    {
        return $this->nbrParticipant;
    }

    public function setNbrParticipant(?int $nbrParticipant): self
    {
        $this->nbrParticipant = $nbrParticipant;

        return $this;
    }

    /**
     *@return Collection|commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setForum($this);
        }

        return $this;
    }

    public function removeCommentaire(commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getForum() === $this) {
                $commentaire->setForum(null);
            }
        }

        return $this;
    }
}
