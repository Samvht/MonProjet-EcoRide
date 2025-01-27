<?php

namespace App\Entity;

use App\Repository\CovoiturageRepository;
use Doctrine\Common\Collections\ArrayCollection; 
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'covoiturage_id')]
    private ?int $covoiturage_id = null;

    #[ORM\Column(type: "string", length: 50, nullable: false)] 
    private string $date_depart;

    #[ORM\Column(type: "string", length: 50, nullable: false)] 
    private string $heure_depart;

    #[ORM\Column(type: "string", length: 50, nullable: false)] 
    private string $lieu_depart;

    #[ORM\Column(type: "string", length: 50, nullable: false)] 
    private string $date_arrivee;

    #[ORM\Column(type: "string", length: 50, nullable: false)] 
    private string $heure_arrivee;

    #[ORM\Column(type: "string", length: 50, nullable: false)] 
    private string $lieu_arrivee;

    #[ORM\Column(type: "string", length: 50, nullable: true)] 
    private ?string $statut;

    #[ORM\Column(type: "integer", nullable: false)] 
    private int $nbre_place;

    #[ORM\Column(type: "float", nullable: false)] 
    private float $prix_personne;

    #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: "covoiturages")] 
    #[ORM\JoinColumn(name: "utilisateur_id", nullable: false)] 
    private ?Voiture $voiture = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: "covoiturages")] 
    private Collection $utilisateurs;

    public function __construct() { 
        $this->utilisateurs = new ArrayCollection(); }

    public function getCovoiturageId(): ?int
    {
        return $this->covoiturage_id;
    }

    public function getDateDepart(): string { 
        return $this->date_depart; 
    } 
        
    public function setDateDepart(string $date_depart): self { 
        $this->date_depart = $date_depart; 
        return $this; 
    }

    public function getHeureDepart(): string { 
        return $this->heure_depart; 
    } 
        
    public function setHeureDepart(string $heure_depart): self { 
        $this->heure_depart = $heure_depart; 
        return $this; 
    }

    public function getLieuDepart(): string { 
        return $this->lieu_depart; 
    } 
        
    public function setLieuDepart(string $lieu_depart): self { 
        $this->lieu_depart = $lieu_depart; 
        return $this; 
    }

    public function getDateArrivee(): string { 
        return $this->date_arrivee; 
    } 
        
    public function setDateArrivee(string $date_arrivee): self { 
        $this->date_arrivee = $date_arrivee; 
        return $this; 
    }

    public function getHeureArrivee(): string { 
        return $this->heure_arrivee; 
    } 
        
    public function setHeureArrivee(string $heure_arrivee): self { 
        $this->heure_arrivee = $heure_arrivee; 
        return $this; 
    }

    public function getLieuArrivee(): string { 
        return $this->lieu_arrivee; 
    } 
        
    public function setLieuArrivee(string $lieu_arrivee): self { 
        $this->lieu_arrivee = $lieu_arrivee; 
        return $this; 
    }

    public function getStatut(): string { 
        return $this->statut; 
    } 
        
    public function setStatut(string $statut): self { 
        $this->statut = $statut; 
        return $this; 
    }

    public function getNbrePlace(): int { 
        return $this->nbre_place; 
    } 
        
    public function setNbrePlace(int $nbre_place): self { 
        $this->nbre_place = $nbre_place; 
        return $this; 
    }

    public function getPrixPersonne(): float { 
        return $this->prix_personne; 
    } 
        
    public function setPrixPersonne(float $prix_personne): self { 
        $this->prix_personne = $prix_personne; 
        return $this; 
    }

    public function getVoiture(): ?Voiture { 
        return $this->voiture; 
    } 

    public function setVoiture(?Voiture $voiture): self { 
        $this->voiture = $voiture; 
        return $this; 
    }

    public function getUtilisateurs(): Collection { 
        return $this->utilisateurs; 
    }

    public function addUtilisateur(Utilisateur $utilisateur): self { 
        if (!$this->utilisateurs->contains($utilisateur)) { 
            $this->utilisateurs[] = $utilisateur; 
            $utilisateur->addCovoiturage($this); 
        } 
        return $this; 
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self { 
        if ($this->utilisateurs->contains($utilisateur)) { 
            $this->utilisateurs->removeElement($utilisateur); 
            $utilisateur->removeCovoiturage($this); } 
        return $this; 
    }


}
