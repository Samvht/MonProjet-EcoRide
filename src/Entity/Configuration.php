<?php

namespace App\Entity;

use App\Repository\ConfigurationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
class Configuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'configuration_id')]
    private ?int $configuration_id = null;

    #[ORM\Column(type: "string", length: 50, unique: true)]
    private string $name;

    #[ORM\OneToMany(targetEntity: Utilisateur::class, mappedBy: "configuration")]
    private Collection $utilisateurs;

    public function __construct() {
        $this->utilisateurs = new ArrayCollection();
    }
    
    public function getConfigurationId(): int {
        return $this->configuration_id;
    }

    public function getName(): string {
        return $this->name;
    }
        
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getUtilisateurs(): Collection {
        return $this->utilisateurs;
    }
        
    public function addUtilisateur(Utilisateur $utilisateur): self {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
            $utilisateur->setConfiguration($this);
        }
            return $this;
        }
        
        public function removeUtilisateur(Utilisateur $utilisateur): self {
            if ($this->utilisateurs->contains($utilisateur)) {
                $this->utilisateurs->removeElement($utilisateur);
                $utilisateur->setConfiguration(null); }
        return $this;
    }
}
