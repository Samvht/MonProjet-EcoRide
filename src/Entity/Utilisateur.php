<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid; 
use Symfony\Bridge\Doctrine\Types\UuidType;
use InvalidArgumentException; // Ajout de l'import pour InvalidArgumentException

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'il y a déjà un compte avec cet identifiant')]
#[ORM\Table(name: "utilisateur")]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)] 
    #[ORM\GeneratedValue(strategy: "CUSTOM")] 
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")] 
    private ?Uuid $id = null;

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
    #liaision table voiture

    #[ORM\ManyToMany(targetEntity: Covoiturage::class, inversedBy: "utilisateurs")] 
    #[ORM\JoinTable(name: "utilisateur_covoiturage")] 
    private Collection $covoiturages;
    #liaison table covoiturage

    #[ORM\ManyToMany(targetEntity: Avis::class, inversedBy: "utilisateurs")] 
    #[ORM\JoinTable(name: "utilisateur_avis")] 
    private Collection $avis;
    #liaison table avis

    #[ORM\ManyToOne(targetEntity: Configuration::class, inversedBy: "utilisateurs")] 
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id")] 
    private ?Configuration $configuration =null;
    #liaison table configuration qui correspond aussi au role de sécurité

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: "utilisateurs")] 
    #[ORM\JoinTable(name: "utilisateur_role")] 
    private Collection $userRoles;
    #liaison table role (chauffeur ou passager, soit 2 roles les 2)
    #renommage de $roles en userRoles pour éviter le conflit avec la methods getRoles() de la sécurité

    public function __construct() 
    { 
        $this->voitures = new ArrayCollection(); 
        $this->covoiturages = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
    }

    public function getId(): uuid
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

    public function getRoles(): array{
        $roles = []; 
        foreach ($this->configuration as $role) { 
            $roles[] = $this->configuration->getName();
        }
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }#methos sécurité symfony + s'assure que l'utilisateur à au moins 1 role (admin, employé..)


    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        #pour nettoyer les données sensibles
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

public function getUserRoles(): Collection { 
    return $this->userRoles; 
}

public function addRole(Role $role): self { 
    if (!$this->userRoles->contains($role)) { 
        $this->userRoles[] = $role; 
        $role->addUtilisateur($this); } 
    return $this; 
}

public function removeRole(Role $role): self { 
if ($this->userRoles->contains($role)) { 
    $this->userRoles->removeElement($role); 
    $role->removeUtilisateur($this);
} 
return $this; 
}

public function getConfiguration(): ?Configuration { 
    return $this->configuration; 
} 

public function setConfiguration(?Configuration $configuration): self { 
    $this->configuration = $configuration; 
    return $this; }
}
