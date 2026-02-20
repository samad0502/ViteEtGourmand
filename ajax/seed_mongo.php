<?php
require '../vendor/autoload.php';

$uri = "mongodb+srv://prof_correction:" . urlencode("ViteGourmand2026") . "@cluster0.ziybmvg.mongodb.net/vite_gourmand?retryWrites=true&w=majority";

try {
    $client = new MongoDB\Client($uri);
    $collection = $client->vite_gourmand->order_stats;

    // On vide la collection avant de la remplir (pour éviter les doublons à chaque test)
    $collection->drop();

    $testData = [
        ['menu' => 'Menu Éco', 'price' => 12.50, 'qty' => 12],
        ['menu' => 'Menu Gourmand', 'price' => 22.00, 'qty' => 10],
        ['menu' => 'Menu Signature', 'price' => 35.00, 'qty' => 6],
    ];

    $documents = [];
    foreach ($testData as $item) {
        for ($i = 0; $i < $item['qty']; $i++) {
            $documents[] = [
                'order_id'    => rand(1000, 9999),
                'menu_name'   => $item['menu'],
                'price'       => (float)$item['price'],
                'executed_at' => new MongoDB\BSON\UTCDateTime()
            ];
        }
    }

    // Insertion massive
    $insertManyResult = $collection->insertMany($documents);

    echo "<h2 style='color:green;'> Succès !</h2>";
    echo "{$insertManyResult->getInsertedCount()} ventes ont été ajoutées à ton cluster Atlas.";
    echo "<br><br><a href='admin_stats.php' style='padding:10px; background:#0d6efd; color:white; text-decoration:none; border-radius:5px;'>Voir les statistiques</a>";
} catch (Exception $e) {
    echo "<h2 style='color:red;'> Erreur lors du seeding</h2>";
    echo $e->getMessage();
}
