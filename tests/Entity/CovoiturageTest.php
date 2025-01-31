<?php 

namespace App\Tests\Entity;

use App\Entity\Utilisateur;
use App\Entity\Covoiturage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CovoiturageTest extends KernelTestCase
{
    public function testAddCovoiturage()
    {
        $utilisateur = new Utilisateur();
        $covoiturage = new Covoiturage();

        // Ajout du covoiturage à l'utilisateur
        $utilisateur->addCovoiturage($covoiturage);

        // Assertions pour vérifier les relations
        $this->assertTrue($utilisateur->getCovoiturages()->contains($covoiturage));
        $this->assertTrue($covoiturage->getUtilisateurs()->contains($utilisateur));
    }
}