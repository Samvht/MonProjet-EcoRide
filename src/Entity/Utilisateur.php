<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException; // Ajout de l'import pour InvalidArgumentException
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: "utilisateur")]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50, unique: true, nullable: false)]
    private string $pseudo;

    #[ORM\Column(type: "string", length: 50, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private string $password;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private string $telephone;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private string $dateNaissance;

    #[ORM\Column(type: "blob", nullable: true)]
    private string $photo;

    #[ORM\OneToMany(targetEntity: Voiture::class, mappedBy: "utilisateur")] 
    private Collection $voitures;

    #[ORM\ManyToMany(targetEntity: Covoiturage::class, inversedBy: "utilisateurs")] 
    #[ORM\JoinTable(name: "utilisateur_covoiturage")] 
    private Collection $covoiturages;

    #[ORM\ManyToMany(targetEntity: Avis::class, inversedBy: "utilisateurs")] 
    #[ORM\JoinTable(name: "utilisateur_avis")] 
    private Collection $avis;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: "utilisateurs")] 
    #[ORM\JoinTable(name: "utilisateur_role")] 
    private Collection $roles;

    public function __construct() 
    { 
        $this->voitures = new ArrayCollection(); 
        $this->covoiturages = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPseudo(): string {
         return $this->pseudo; 
    }

    public function setPseudo(string $pseudo): self { 
        $this->pseudo = $pseudo; 
        return $this; 
    }

    public function getEmail(): string {
        return $this->email; 
    }

    public function setEmail(string $email): self { 
        $this->email = $email; 
        return $this; 
    }

    public function getPassword(): string {
        return $this->password; 
    }

    public function setPassword(string $password): self { 
        $this->password = $password; 
        return $this; 
    }

    public function getTelephone(): string { 
        return $this->telephone; 
    }

    public function setTelephone(string $telephone): self { 
        $this->telephone = $telephone; 
        return $this; 
    }

    public function getDateNaissance(): string { 
        return $this->dateNaissance; 
    }

    public function setDateNaissance(string $dateNaissance): self { 
        $this->dateNaissance = $dateNaissance; 
        return $this; 
    }

    public function getPhoto() {
        return $this->photo; 
    }

    public function setPhoto($photo): self {
        $this->photo = $photo; 
        return $this; 
    }

    public function getVoitures(): Collection { 
        return $this->voitures; 
    }

    public function addVoiture(Voiture $voiture): self { 
        if (!$this->voitures->contains($voiture)) { 
            $this->voitures[] = $voiture; 
            $voiture->setUtilisateur($this); } 
    return $this; 
}

    public function removeVoiture(Voiture $voiture): self { 
        if ($this->voitures->contains($voiture)) { 
            $this->voitures->removeElement($voiture); 
            if ($voiture->getUtilisateur() === $this) {
                $voiture->setUtilisateur(null); 
        } 
    } 
        return $this; 
}

    public function getCovoiturages(): Collection { 
        return $this->covoiturages; 
}

    public function addCovoiturage(Covoiturage $covoiturage): self { 
        if (!$this->covoiturages->contains($covoiturage)) { 
            $this->covoiturages[] = $covoiturage; 
            $covoiturage->addUtilisateur($this); } 
        return $this; 
}

public function removeCovoiturage(Covoiturage $covoiturage): self { 
    if ($this->covoiturages->contains($covoiturage)) { 
        $this->covoiturages->removeElement($covoiturage); 
        $covoiturage->removeUtilisateur($this);
} 
    return $this; 
}

public function getAvis(): Collection { 
    return $this->avis; 
}

public function addAvis(Avis $avis): self { 
    if (!$this->avis->contains($avis)) { 
        $this->avis[] = $avis; 
        $avis->addUtilisateur($this); } 
    return $this; 
}

public function removeAvis(Avis $avis): self { 
if ($this->avis->contains($avis)) { 
    $this->avis->removeElement($avis); 
    $avis->removeUtilisateur($this);
} 
return $this; 
}

public function getRoles(): Collection { 
    return $this->roles; 
}

public function addRole(Role $role): self { 
    if (!$this->roles->contains($role)) { 
        $this->roles[] = $role; 
        $role->addUtilisateur($this); } 
    return $this; 
}

public function removeRole(Role $role): self { 
if ($this->roles->contains($role)) { 
    $this->roles->removeElement($role); 
    $role->removeUtilisateur($this);
} 
return $this; 
}
}
