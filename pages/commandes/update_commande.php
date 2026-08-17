<?php

include "../../php/connexion.php";

$id = $_POST['id_commande'];
$type = $_POST['type'];
$statut = $_POST['statut'];
$date = $_POST['date'];
$quantite = $_POST['quantite'];

$sql = "UPDATE commande
SET
type='$type',
statut='$statut',
date='$date',
quantite='$quantite'
WHERE id_commande='$id'";

if($connexion->query($sql) === TRUE){

    header("Location: commandes.php");
    exit();

}else{

    echo "Erreur : " . $connexion->error;

}

$connexion->close();

?>