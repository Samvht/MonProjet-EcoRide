<?php 

namespace App\Tests\Entity;

use App\Entity\Utilisateur;
use App\Entity\Covoiturage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UtilisateurTest extends KernelTestCase
{
    public function testUserCovoiturageRelation()
    {
        $utilisateur = new Utilisateur();
        $covoiturage = new Covoiturage();

        $utilisateur->addCovoiturage($covoiturage);

        $this->assertTrue($utilisateur->getCovoiturages()->contains($covoiturage));
        $this->assertTrue($covoiturage->getUtilisateurs()->contains($utilisateur));
    }
}