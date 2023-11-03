<?php

// Remplacez ces valeurs par le titre, l'auteur et votre clé d'API
$titre = "Le Seigneur des Anneaux";
$auteur = "J.R.R. Tolkien";
$isbn = "9788845292613";
$cle_api = "AIzaSyDTLD3fEVUDuZulL7p47gOMRn-Bfu3Fvis";

// Construisez l'URL de l'API
$url = "https://www.googleapis.com/books/v1/volumes?q=intitle:" . urlencode($titre) . "+inauthor:" . urlencode($auteur) . "&key=" . $cle_api;

// Initialisez une session cURL
$ch = curl_init();

// Configurez les options de la session cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Effectuez la requête
$response = curl_exec($ch);

// Vérifiez si la requête a réussi
if ($response) {
    $data = json_decode($response, true);
    var_dump($data);

    if (isset($data['items'][0]['volumeInfo']['imageLinks']['thumbnail'])) {
        $couverture_url = $data['items'][0]['volumeInfo']['imageLinks']['thumbnail'];
        echo "URL de la couverture du livre : " . $couverture_url;
    } else {
        echo "Aucun résultat trouvé pour ce livre.";
    }
} else {
    echo "Erreur lors de la requête à l'API Google Books : " . curl_error($ch);
}

// Fermez la session cURL
curl_close($ch);

?>
