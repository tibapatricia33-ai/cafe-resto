<?php

include "../../php/connexion.php";


/* =========================================
   RÉCUPÉRER L'ID DU PAIEMENT
   ========================================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {

    die("Paiement introuvable.");

}

$id = (int) $_GET['id'];


/* =========================================
   SUPPRIMER LE PAIEMENT
   ========================================= */

$sql = "DELETE FROM transaction_paiement
        WHERE id_transaction = ?";

$stmt = $connexion->prepare($sql);

$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    header("Location: paiements.php");

    exit();

} else {

    echo "Erreur lors de la suppression : "
         . $stmt->error;

}


$stmt->close();

$connexion->close();

?>