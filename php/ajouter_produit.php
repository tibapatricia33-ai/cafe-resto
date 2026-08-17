<?php

include "../php/connexion.php";


$nom = $_POST['nom'];
$prix = $_POST['prix'];
$quantite_stock = $_POST['quantite_stock'];
$seuil_alert = $_POST['seuil_alert'];


$sql = "INSERT INTO produit (nom, prix, quantite_stock, seuil_alerte)
VALUES ('$nom', '$prix', '$quantite_stock', '$seuil_alert')";


if ($connexion->query($sql) === TRUE) {

    echo "Produit ajouté avec succès";

} else {

    echo "Erreur : " . $connexion->error;

}


$connexion->close();
header("Location: produits.php");
exit();

?>