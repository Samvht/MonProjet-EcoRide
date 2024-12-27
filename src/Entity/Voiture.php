<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\Common\Collections\ArrayCollection; 
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
#[ORM\Table(name: "voiture")]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private string $modele;

    #[ORM\Column(type: "string", length: 50, nullable: false)]
    private string $immatriculation;

    #[ORM\Column(type: "string", length: 50, nullable: false)]
    private string $energie;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private string $couleur;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private string $date_premiere_immatriculation;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "voitures")] 
    #[ORM\JoinColumn(nullable: false)] 
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Marque::class, inversedBy: "voiture")] 
    #[ORM\JoinColumn(name:"marque_id",referencedColumnName:"marque_id", nullable: false)] 
    private ?Marque $marque = null;

    #[ORM\OneToMany(targetEntity: Covoiturage::class, mappedBy: "voiture")] 
    private Collection $covoiturages;

    public function __construct() { 
        $this->covoiturages = new ArrayCollection(); 
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getModele(): string {
        return $this->modele; 
   }

   public function setModele(string $modele): self { 
       $this->modele = $modele; 
       return $this; 
   }

   public function getImmatriculation(): string {
       return $this->immatriculation; 
   }

   public function setImmatriculation(string $immatriculation): self { 
       $this->immatriculation = $immatriculation; 
       return $this; 
   }

   public function getEnergie(): string {
       return $this->energie; 
   }

   public function setEnergie(string $energie): self{ 
       $this->energie = $energie; 
       return $this; 
   }

   public function getCouleur(): string {
    return $this->couleur; 
}

public function setCouleur(string $couleur): self{ 
    $this->couleur = $couleur; 
    return $this; 
}

public function getDate_premiere_immatriculation(): string {
    return $this->date_premiere_immatriculation; 
}

public function setDate_premiere_immatriculation(string $date_premiere_immatriculation): self { 
    $this->date_premiere_immatriculation = $date_premiere_immatriculation; 
    return $this; 
}

public function getUtilisateur(): ?Utilisateur { 
    return $this->utilisateur; 
}

public function setUtilisateur(?Utilisateur $utilisateur): self { 
    $this->utilisateur = $utilisateur; 
    return $this; 
}

public function getMarque(): ?Marque { 
    return $this->marque; 
} 
public function setMarque(?Marque $marque): self { 
    $this->marque = $marque; 
    return $this; 
}

public function getCovoiturages(): Collection { 
    return $this->covoiturages; 
} 

public function addCovoiturage(Covoiturage $covoiturage): self {
    if (!$this->covoiturages->contains($covoiturage)) { 
        $this->covoiturages[] = $covoiturage; 
        $covoiturage->setVoiture($this); } 
        return $this; 
} 

public function removeCovoiturage(Covoiturage $covoiturage): self {
    if ($this->covoiturages->contains($covoiturage)) { 
        $this->covoiturages->removeElement($covoiturage); 
        if ($covoiturage->getVoiture() === $this) { 
            $covoiturage->setVoiture(null); 
        } 
    } 
    return $this; 
}

}
