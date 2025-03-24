<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb+srv://Jose:EcoRide13@cluster0.mxmxn.mongodb.net/Ecoride_db?retryWrites=true&w=majority");

try {
    $databases = $client->listDatabases();
    echo "Connexion réussie ! Voici les bases disponibles :\n";
    foreach ($databases as $database) {
        echo $database['name'] . "\n";
    }
} catch (Exception $e) {
    echo "Erreur de connexion : " . $e->getMessage() . "\n";
}