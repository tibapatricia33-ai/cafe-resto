<?php

include "../../php/connexion.php";


/* =========================================
   RÉCUPÉRER LES DONNÉES
   ========================================= */

$id_transaction = (int) $_POST['id_transaction'];
$id_commande = (int) $_POST['id_commande'];
$montant = (float) $_POST['montant'];
$type = $_POST['type'];
$statut = $_POST['statut'];
$reference_mobile_money =
    $_POST['reference_mobile_money'] ?? '';


/* =========================================
   MODIFIER LE PAIEMENT
   ========================================= */

$sql = "UPDATE transaction_paiement
        SET
            id_commande = ?,
            montant = ?,
            type = ?,
            statut = ?,
            reference_mobile_money = ?
        WHERE id_transaction = ?";


$stmt = $connexion->prepare($sql);

$stmt->bind_param(
    "idsssi",
    $id_commande,
    $montant,
    $type,
    $statut,
    $reference_mobile_money,
    $id_transaction
);


if ($stmt->execute()) {

    header("Location: paiements.php");

    exit();

} else {

    echo "Erreur lors de la modification : "
         . $stmt->error;

}


$stmt->close();

$connexion->close();

?>