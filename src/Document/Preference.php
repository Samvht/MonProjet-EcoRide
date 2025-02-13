<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(collection:"preferences")]
class Preference
{
    #[ODM\Id(strategy: "AUTO")]
    private ?string $id = null;

    #[ODM\Field(type: "boolean")]
    #[Assert\NotNull]
    private bool $fumeur;

    #[ODM\Field(type: "boolean")]
    #[Assert\NotNull]
    private bool $animal;

    #[ODM\Field(type: "string")]
    #[Assert\Length(max: 500)]
    private ?string $PreferenceSupplementaire = null;

    #[ODM\Field(type: "string")]
    private ?string $utilisateur_id = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getfumeur(): bool
    {
        return $this->fumeur;
    }

    public function setFumeur(bool $fumeur): self
    {
        $this->fumeur = $fumeur;
        return $this;
    }

    public function getanimal(): bool
    {
        return $this->animal;
    }

    public function setAnimal(bool $animal): self
    {
        $this->animal = $animal;
        return $this;
    }

    public function getPreferenceSupplementaire(): ?string
    {
        return $this->PreferenceSupplementaire;
    }

    public function setPreferenceSupplementaire(string $preferenceSupplementaire): self
    {
        $this->PreferenceSupplementaire = $preferenceSupplementaire;
        return $this;
    }

    public function getUtilisateurId(): ?string
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateurId(?string $utilisateur_id): self
    {
        $this->utilisateur_id = $utilisateur_id;
        return $this;
    }
 }