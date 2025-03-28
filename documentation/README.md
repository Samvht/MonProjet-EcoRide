# Application web de covoiturage EcoRide
***

##Description
***
Cette application de covoiturage a été crée dans le cadre d'un projet de formation. Elle permet aux utilisateurs de se connecter, rechercher des trajets, créer des covoiturage si vous êtes chauffeur ou bien d'y participer si vous êtes passager. 


##Fonctionnalités
***
- Connexion / Inscription
- Recherche de covoiturage
- Création d'un covoiturage
- Participation à un covoiturage

##Prérequis pour le déploiement
***
- PHP 8.4
- Composer (gestionnaire de dépendances PHP)
- WAMP avec Apache et MySQL
- Symfony 7.1
- Symfony CLI (gestionnaire des applications Symfony)

##Installation
***
1. Cloner le projet:
```bash
git clone https://github.com/Samvht/MonProjet-EcoRide
```

2.Installer les dépendances
```bash
composer install
```

3.configurer la base de données
Celle-ci a été créé manuellement, voir les fichier SQL dans le dépôt git pour créer et insérer les données dans MySQL.

4.Configurer la variable d'environnement
Dans le fichier .env, remplir la ligne DATABASE_URL="DATABASE_URL="mysql://username:password@127.0.0.1:3306/database_name"
Avec username = le nom de l'utilisateur de la base de donnée, password = le mot de passe de l'utilisateur, 127.0.0.1 = l'adresse IP de la base de données,  3306 = le port utiliser par la base de donnée, database_name = le nom de la base de données.

5.Démarrez le serveur local
```bash
cd /chemin/vers/le/projet
```
```bash
.\symfony server:start
```
Entrer l'URL donné par le serveur dans le navigateur, rajouter /accueil pour accéder à la page d'accueil du site

Pour arrêter le serveur :
```bash
.\symfony server:stop
```
##Capture d'écran
![Capture d'écran de la page accueil](PageAccueil.jpg)





