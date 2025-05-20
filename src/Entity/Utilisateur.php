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
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'il y a déjà un compte avec cet identifiant', groups: ['registration'])]
#[ORM\Table(name: "utilisateur")]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(name:"utilisateur_id", type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $utilisateur_id = null;

    #[ORM\Column(type: "string", length: 50, unique: true, nullable: false)]
    private string $pseudo;

    #[ORM\Column(type: "string", length: 50, nullable: false)]
    private string $email;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private string $password;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $telephone;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $date_naissance;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $photo;

    #[ORM\OneToMany(targetEntity: Voiture::class, mappedBy: "utilisateur")]
    private Collection $voitures;
    #liaision table voiture

    #[ORM\ManyToMany(targetEntity: Covoiturage::class, inversedBy: "utilisateurs")]
    #[ORM\JoinTable(name: "utilisateur_covoiturage")]
    #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'utilisateur_id')]
    #[ORM\InverseJoinColumn(name: 'covoiturage_id', referencedColumnName: 'covoiturage_id')]
    private Collection $covoiturages;
    #liaison table covoiturage

    #[ORM\ManyToMany(targetEntity: Avis::class, inversedBy: "utilisateurs")]
    #[ORM\JoinTable(name: "utilisateur_avis")]
    private Collection $avis;
    #liaison table avis

    #[ORM\ManyToOne(targetEntity: Configuration::class, inversedBy: "utilisateurs")]
    #[ORM\JoinColumn(name: "configuration_id", referencedColumnName: "configuration_id", nullable: false)]
    private ?Configuration $configuration =null;
    #liaison table configuration qui correspond aussi au role de sécurité

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: "utilisateurs")]
    #[ORM\JoinTable(name: "utilisateur_role")]
    #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'utilisateur_id')]
    #[ORM\InverseJoinColumn(name: 'role_id', referencedColumnName: 'role_id')]
    private Collection $userRoles;
    #liaison table role (chauffeur ou passager, soit 2 roles les 2)
    #renommage de $roles en userRoles pour éviter le conflit avec la methods getRoles() de la sécurité

    public function __construct()
    {
        $this->utilisateur_id = Uuid::v4();
        $this->voitures = new ArrayCollection();
        $this->covoiturages = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
    }

    public function getUtilisateurId(): ?Uuid
    {
        return $this->utilisateur_id;
    }

    #rajout setter Id pour visualiser covoiturage
    public function setUtilisateurId(uuid $utilisateur_id): self
    {
        $this->utilisateur_id = $utilisateur_id;
        return $this;
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

    public function getTelephone(): ?string {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self {
        $this->telephone = $telephone;
        return $this;
    }

    public function getDateNaissance(): ?string {
        return $this->date_naissance;
    }

    public function setDateNaissance(?string $date_naissance): self{
        $this->date_naissance = $date_naissance;
        return $this;
    }

    public function getPhoto(): ?string {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self {
        $this->photo = $photo;
        return $this;
    }

    public function getRoles(): array{
        $roles = [];
        if ($this->configuration) {
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
        return $this;
    }
}
