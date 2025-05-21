<?php

namespace App\Entity;

use App\Entity\Covoiturage;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AvisRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'avis_id')]
    private ?int $avis_id = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private string $pseudo;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $note;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private string $commentaire;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private string $statut;

    #[ORM\ManyToOne(targetEntity: Covoiturage::class, inversedBy: "avis")]
    #[ORM\JoinColumn(name: "covoiturage_id", referencedColumnName: "covoiturage_id",  nullable: false)]
    private ?Covoiturage $covoiturage = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "avis")]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "utilisateur_id",  nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function getAVisId(): ?int
    {
        return $this->avis_id;
    }

    public function getPseudo(): string {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getNote(): int {
        return $this->note;
    }

    public function setNote(int $note): self {
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

    public function getCovoiturage(): ?Covoiturage {
        return $this->covoiturage;
    }

    public function setCovoiturage(?Covoiturage $covoiturage): self {
        $this->covoiturage = $covoiturage;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur{
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self {
        $this->utilisateur = $utilisateur;
        return $this;
    }
}
