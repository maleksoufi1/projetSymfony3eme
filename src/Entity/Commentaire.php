<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentaireRepository::class)
 */
class Commentaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Contenu is required")
     */
    private $contenu;

    /**
     * @Assert\NotBlank(message="Type is required")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Forum::class, inversedBy="commentaires")
     *
     */
    private $forum;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commentaires")
     * 
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vus;

    /**
     * @ORM\Column(type="date")
     */
    private $temps;

    /**
     * @ORM\OneToOne(targetEntity=CommentaireUser::class, mappedBy="commentaire", cascade={"persist", "remove"})
     */
    private $commentaireUser;

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

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

    public function getForum(): ?Forum
    {
        return $this->forum;
    }

    public function setForum(?Forum $forum): self
    {
        $this->forum = $forum;

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

    public function getVus(): ?int
    {
        return $this->vus;
    }

    public function setVus(?int $vus): self
    {
        $this->vus = $vus;

        return $this;
    }

    public function getTemps(): ?\DateTimeInterface
    {
        return $this->temps;
    }

    public function setTemps(\DateTimeInterface $temps): self
    {
        $this->temps = $temps;

        return $this;
    }

    public function getCommentaireUser(): ?CommentaireUser
    {
        return $this->commentaireUser;
    }

    public function setCommentaireUser(CommentaireUser $commentaireUser): self
    {
        // set the owning side of the relation if necessary
        if ($commentaireUser->getCommentaire() !== $this) {
            $commentaireUser->setCommentaire($this);
        }

        $this->commentaireUser = $commentaireUser;

        return $this;
    }
   

}
