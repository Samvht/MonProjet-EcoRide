<?php

require 'vendor/autoload.php'; 

$client = new MongoDB\Client('mongodb+srv://Jose:EcoRide13@cluster0.mxmxn.mongodb.net/?retryWrites=true&w=majority');

try {
    // Change 'Ecoride_db' par le nom que tu penses avoir utilisé
    $database = $client->selectDatabase('Ecoride_db');
    $collection = $database->selectCollection('testCollection');

    // Insère un document dans la collection
    $result = $collection->insertOne([
        'test_field' => 'Hello, MongoDB!',
        'timestamp' => new DateTime()
    ]);

    echo "Document inséré avec l'ID : " . $result->getInsertedId() . PHP_EOL;

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . PHP_EOL;
}