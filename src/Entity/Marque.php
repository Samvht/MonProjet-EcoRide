<?php

namespace App\Entity;

use App\Repository\MarqueRepository;
use Doctrine\Common\Collections\ArrayCollection; 
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarqueRepository::class)]
class Marque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"marque_id")]
    private ?int $marque_id = null;

    #[ORM\Column(length: 50)]
    private ?string $libelle = null;
 
    #[ORM\OneToMany(targetEntity: Voiture::class, mappedBy: "marque")] 
    private Collection $voiture;

    public function __construct() { 
        $this->voiture = new ArrayCollection(); }

    public function getMarqueId(): ?int
    {
        return $this->marque_id;
    }
    public function getLibelle(): string { 
        return $this->libelle; 
    } 
    public function setLibelle(string $libelle): self { 
        $this->libelle = $libelle; 
        return $this; }

    public function getVoitures(): Collection { 
            return $this->voiture; 
        } 
    public function addVoiture(Voiture $voiture): self { 
        if (!$this->voiture->contains($voiture)) {
            $this->voiture[] = $voiture; 
            $voiture->setMarque($this); 
        } 
            return $this; 
    } 
    
    public function removeVoiture(Voiture $voiture): self {
         if ($this->voiture->contains($voiture)) {
            $this->voiture->removeElement($voiture); 
            if ($voiture->getMarque() === $this) { 
                $voiture->setMarque(null); }
            }
         return $this;
        }
}
