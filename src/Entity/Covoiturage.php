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
    #[ORM\Column]
    private ?int $id = null;

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
    private string $statut;

    #[ORM\Column(type: "integer", nullable: false)] 
    private string $nbre_place;

    #[ORM\Column(type: "float", nullable: false)] 
    private string $prix_personne;

    #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: "covoiturages")] 
    #[ORM\JoinColumn(nullable: false)] 
    private ?Voiture $voiture = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: "covoiturages")] 
    private Collection $utilisateurs;

    public function __construct() { 
        $this->utilisateurs = new ArrayCollection(); }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate_depart(): string { 
        return $this->date_depart; 
    } 
        
    public function setDate_depart(string $date_depart): self { 
        $this->date_depart = $date_depart; 
        return $this; 
    }

    public function getHeure_depart(): string { 
        return $this->heure_depart; 
    } 
        
    public function setHeure_depart(string $heure_depart): self { 
        $this->heure_depart = $heure_depart; 
        return $this; 
    }

    public function getLieu_depart(): string { 
        return $this->lieu_depart; 
    } 
        
    public function setLieu_depart(string $lieu_depart): self { 
        $this->lieu_depart = $lieu_depart; 
        return $this; 
    }

    public function getDate_arrivee(): string { 
        return $this->date_arrivee; 
    } 
        
    public function setDate_arrivee(string $date_arrivee): self { 
        $this->date_arrivee = $date_arrivee; 
        return $this; 
    }

    public function getHeure_arrivee(): string { 
        return $this->heure_arrivee; 
    } 
        
    public function setHeure_arrivee(string $heure_arrivee): self { 
        $this->heure_arrivee = $heure_arrivee; 
        return $this; 
    }

    public function getLieu_arrivee(): string { 
        return $this->lieu_arrivee; 
    } 
        
    public function setLieu_arrivee(string $lieu_arrivee): self { 
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

    public function getNbre_place(): int { 
        return $this->nbre_place; 
    } 
        
    public function setNbre_place(int $nbre_place): self { 
        $this->nbre_place = $nbre_place; 
        return $this; 
    }

    public function getPrix_personne(): float { 
        return $this->prix_personne; 
    } 
        
    public function setPrix_personne(float $prix_personne): self { 
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
