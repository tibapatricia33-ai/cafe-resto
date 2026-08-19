<?php

$serveur = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$base = "restaurant (1)";


$connexion = new mysqli($serveur, $utilisateur, $mot_de_passe, $base);


if ($connexion->connect_error) {
    die("Erreur de connexion : " . $connexion->connect_error);
}
?>