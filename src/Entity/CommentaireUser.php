<?php

namespace App\Entity;

use App\Repository\CommentaireUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentaireUserRepository::class)
 */
class CommentaireUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="commentaireUser", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=Commentaire::class, inversedBy="commentaireUser", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $commentaire;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCommentaireUser(): ?int
    {
        return $this->id_commentaire_user;
    }

    public function setIdCommentaireUser(int $id_commentaire_user): self
    {
        $this->id_commentaire_user = $id_commentaire_user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCommentaire(): ?Commentaire
    {
        return $this->commentaire;
    }

    public function setCommentaire(Commentaire $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

  
}

