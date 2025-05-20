<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'role_id')]
    private ?int $role_id = null;

    #[ORM\Column(type: "string", length: 50, nullable: false)]
    private string $libelle;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: "roles")]
    private Collection $utilisateurs;

    public function __construct() {
        $this->utilisateurs = new ArrayCollection();
    }

    public function getRoleId(): ?int
    {
        return $this->role_id;
    }

    public function getLibelle(): string {
        return $this->libelle;
    }
    
    public function setLibelle(string $libelle): self {
        $this->libelle = $libelle;
        return $this;
    }

    public function getUtilisateurs(): Collection {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
            $utilisateur->addRole($this);
        }
        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self {
        if ($this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->removeElement($utilisateur);
            $utilisateur->removeRole($this); }
    return $this;
    }
}
