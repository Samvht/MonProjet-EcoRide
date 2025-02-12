<?php 

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;

#pour gérer les roles métier et gérer l'accès des pages et des liens en fonction
class RoleService
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getUserRolesMetier(): array
    {
        $utilisateur = $this->security->getUser();

        if (!$utilisateur) {
            return [];
        }

        return $utilisateur->getUserRoles()->map(function($role) {
            return $role->getRoleId();
        })->toArray();
    }
}