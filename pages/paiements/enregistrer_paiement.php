<?php

include "../../php/connexion.php";

/* =========================================
   RÉCUPÉRER LES DONNÉES DU FORMULAIRE
   ========================================= */

$id_commande = $_POST['id_commande'];
$montant = $_POST['montant'];
$type = $_POST['type'];
$statut = $_POST['statut'];
$reference_mobile_money = $_POST['reference_mobile_money'];


/* =========================================
   VÉRIFICATION
   ========================================= */

if (
    empty($id_commande) ||
    empty($montant) ||
    empty($type) ||
    empty($statut)
) {

    die("Veuillez remplir tous les champs obligatoires.");

}


/* =========================================
   ENREGISTRER LE PAIEMENT
   ========================================= */

$sql = "INSERT INTO transaction_paiement
        (id_commande, montant, type, statut, reference_mobile_money)
        VALUES (?, ?, ?, ?, ?)";


$stmt = $connexion->prepare($sql);


$stmt->bind_param(
    "idsss",
    $id_commande,
    $montant,
    $type,
    $statut,
    $reference_mobile_money
);


if ($stmt->execute()) {

    /* =====================================
       PAIEMENT ENREGISTRÉ
       ===================================== */

    header("Location: paiements.php");

    exit();

} else {

    echo "Erreur lors de l'enregistrement du paiement : "
         . $stmt->error;

}


$stmt->close();

$connexion->close();

?>