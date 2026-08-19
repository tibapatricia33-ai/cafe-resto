 <?php

$serveur = "127.0.0.1";
$utilisateur = "root";
$mot_de_passe = "";
$base = "restaurant";

$connexion = new mysqli($serveur, $utilisateur, $mot_de_passe, $base, 3307);


if ($connexion->connect_error) {
    die("Erreur de connexion : " . $connexion->connect_error);
}
?>