<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\Common\Collections\ArrayCollection; 
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50, nullable: false)] 
    private string $note;

    #[ORM\Column(type: "string", length: 255, nullable: true)] 
    private string $commentaire;

    #[ORM\Column(type: "string", length: 50, nullable: true)] 
    private string $statut;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: "avis")] 
    private Collection $utilisateurs;

    public function __construct() { 
        $this->utilisateurs = new ArrayCollection(); 
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): string { 
        return $this->note; 
    } 

    public function setNote(string $note): self { 
        $this->note = $note; 
        return $this; 
    }

    public function getCommentaire(): string { 
        return $this->commentaire; 
    } 

    public function setCommentaire(string $commentaire): self { 
        $this->commentaire = $commentaire; 
        return $this; 
    }

    public function getStatut(): string { 
        return $this->statut; 
    } 

    public function setStatut(string $statut): self { 
        $this->statut = $statut; 
        return $this; 
    }

    public function getUtilisateur(): Collection { 
        return $this->utilisateurs; 
    } 
        
    public function addUtilisateur(Utilisateur $utilisateur): self { 
        if (!$this->utilisateurs->contains($utilisateur)) { 
            $this->utilisateurs[] = $utilisateur; 
            $utilisateur->addAvis($this); 
        } 
            return $this; 
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self { 
        if ($this->utilisateurs->contains($utilisateur)) { 
            $this->utilisateurs->removeElement($utilisateur); 
            $utilisateur->removeAvis($this); } 
    return $this; 
}
}
