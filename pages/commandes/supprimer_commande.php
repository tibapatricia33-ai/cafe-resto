<?php

include "../../php/connexion.php";

if(isset($_GET['id'])){

    $id = $_GET['id'];

    $sql = "DELETE FROM commande WHERE id_commande = '$id'";

    if($connexion->query($sql) === TRUE){

        header("Location: commandes.php");
        exit();

    }else{

        echo "Erreur : " . $connexion->error;

    }

}else{

    echo "Aucune commande sélectionnée.";

}

$connexion->close();

?>