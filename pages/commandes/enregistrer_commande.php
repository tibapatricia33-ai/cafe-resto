<?php

include "../../php/connexion.php";


$type = $_POST['type'];
$statut = $_POST['statut'];
$date = $_POST['date'];
$nom = $_POST['nom'];
$montant = $_POST['montant'];
$quantite = $_POST['quantite'];


$sql = "INSERT INTO commande
(type, statut, date, nom, montant, quantite)
VALUES
('$type', '$statut', '$date', '$nom', '$montant', '$quantite')";


if($connexion->query($sql) === TRUE){

    header("Location: commandes.php");
    exit();

}else{

    echo "Erreur : " . $connexion->error;

}


$connexion->close();

?>