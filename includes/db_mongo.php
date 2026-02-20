<?php
require_once __DIR__ . '/../vendor/autoload.php';

function getMongoConnection()
{
    $user = "prof_correction";
    $pass = urlencode("ViteGourmand2026");
    $host = "cluster0.ziybmvg.mongodb.net";
    $dbname = "vite_gourmand";

    $uri = "mongodb+srv://$user:$pass@$host/$dbname?retryWrites=true&w=majority";

    try {
        $client = new MongoDB\Client($uri);
        return $client->$dbname;
    } catch (Exception $e) {
        die("Erreur de liaison MongoDB : " . $e->getMessage());
    }
}
