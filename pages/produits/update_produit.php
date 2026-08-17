<?php

include "../../php/connexion.php";

$id = $_POST['id_produit'];
$nom = $_POST['nom'];
$prix = $_POST['prix'];
$quantite_stock = $_POST['quantite_stock'];
$seuil_alerte = $_POST['seuil_alerte'];

$sql = "UPDATE produit
        SET nom='$nom',
            prix='$prix',
            quantite_stock='$quantite_stock',
            seuil_alerte='$seuil_alerte'
        WHERE id_produit='$id'";

if ($connexion->query($sql) === TRUE) {

    header("Location: produits.php");
    exit();

} else {

    echo "Erreur : " . $connexion->error;

}

$connexion->close();

?>