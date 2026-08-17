<?php

include "../.../php/connexion.php";


/* =========================================================
   VÉRIFIER L'ID
   ========================================================= */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    die("Produit introuvable.");

}

$id = (int)$_GET['id'];


/* =========================================================
   VÉRIFIER QUE LE PRODUIT EXISTE
   ========================================================= */

$sql = "
    SELECT id_produit, nom
    FROM produit
    WHERE id_produit = ?
";

$stmt = $connexion->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$resultat = $stmt->get_result();


if ($resultat->num_rows === 0) {

    die("Ce produit n'existe pas.");

}


/* =========================================================
   SUPPRIMER LE PRODUIT
   ========================================================= */

$sqlDelete = "
    DELETE FROM produit
    WHERE id_produit = ?
";

$stmtDelete = $connexion->prepare($sqlDelete);

$stmtDelete->bind_param("i", $id);


if ($stmtDelete->execute()) {

    /* Retour vers la liste des produits */

    header("Location: produits.php?message=supprime");

    exit;

} else {

    die(
        "Impossible de supprimer le produit : "
        . $stmtDelete->error
    );

}

?>
