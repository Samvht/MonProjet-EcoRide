<?php
require 'vendor/autoload.php';


try {
    $client = new MongoDB\Client("mongodb+srv://Jose:EcoRide13@cluster0.mxmxn.mongodb.net/Ecoride_db?retryWrites=true&w=majority");
    $databaseNames = iterator_to_array($client->listDatabaseNames()); // Convertit l'itérateur en tableau

    echo "Connexion réussie ! Bases disponibles : " . implode(", ", $databaseNames) . PHP_EOL;
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . PHP_EOL;
}