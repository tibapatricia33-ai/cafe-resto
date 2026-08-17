<?php

include "../../php/connexion.php";

/* =========================================
   VÉRIFIER LES DONNÉES REÇUES
   ========================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Le formulaire n'a pas été envoyé avec POST.");
}

if (
    !isset($_POST['description']) ||
    !isset($_POST['nom']) ||
    !isset($_POST['quantite']) ||
    !isset($_POST['unite']) ||
    !isset($_POST['montant']) ||
    !isset($_POST['date'])
) {
    die("Une ou plusieurs données du formulaire manquent.");
}


/* =========================================
   RÉCUPÉRER LES DONNÉES
   ========================================= */

$description = trim($_POST['description']);
$nom         = trim($_POST['nom']);
$quantite    = (float) $_POST['quantite'];
$unite       = trim($_POST['unite']);
$montant     = (float) $_POST['montant'];
$date        = $_POST['date'];


/* =========================================
   AFFICHER CE QUI A ÉTÉ REÇU
   ========================================= */

echo "<h3>Données reçues :</h3>";

echo "Description : " . htmlspecialchars($description) . "<br>";
echo "Matière : " . htmlspecialchars($nom) . "<br>";
echo "Quantité : " . $quantite . "<br>";
echo "Unité : " . htmlspecialchars($unite) . "<br>";
echo "Montant : " . $montant . "<br>";
echo "Date : " . htmlspecialchars($date) . "<br><br>";


/* =========================================
   VÉRIFICATION
   ========================================= */

if (
    empty($description) ||
    empty($nom) ||
    $quantite <= 0 ||
    empty($unite) ||
    $montant <= 0 ||
    empty($date)
) {
    die("ERREUR : une donnée est vide ou incorrecte.");
}


/* =========================================
   RÉCUPÉRER L'UTILISATEUR
   ========================================= */

$sqlUtilisateur = "
    SELECT id_utilisateur
    FROM utilisateur
    ORDER BY id_utilisateur ASC
    LIMIT 1
";

$resultUtilisateur = $connexion->query($sqlUtilisateur);

if (!$resultUtilisateur) {
    die(
        "Erreur recherche utilisateur : "
        . $connexion->error
    );
}

if ($resultUtilisateur->num_rows == 0) {
    die("Aucun utilisateur n'existe dans la table utilisateur.");
}

$utilisateur = $resultUtilisateur->fetch_assoc();

$id_utilisateur = $utilisateur['id_utilisateur'];

echo "ID utilisateur : " . $id_utilisateur . "<br><br>";

$resultUtilisateur = $connexion->query($sqlUtilisateur);

if (!$resultUtilisateur) {
    die(
        "Erreur recherche utilisateur : "
        . $connexion->error
    );
}

if ($resultUtilisateur->num_rows == 0) {
    die("L'utilisateur Administrateur n'existe pas.");
}

$utilisateur = $resultUtilisateur->fetch_assoc();

$id_utilisateur = $utilisateur['id_utilisateur'];

echo "ID utilisateur : " . $id_utilisateur . "<br><br>";


/* =========================================
   COMMENCER LA TRANSACTION
   ========================================= */

$connexion->begin_transaction();


try {

    /* =====================================
       ENREGISTRER LA DÉPENSE
       ===================================== */

    $sqlDepense = "
        INSERT INTO depense
        (description, montant, date, id_utilisateur)
        VALUES (?, ?, ?, ?)
    ";

    $stmtDepense = $connexion->prepare($sqlDepense);

    if (!$stmtDepense) {
        throw new Exception(
            "Erreur préparation dépense : "
            . $connexion->error
        );
    }

    $stmtDepense->bind_param(
        "sdsi",
        $description,
        $montant,
        $date,
        $id_utilisateur
    );

    if (!$stmtDepense->execute()) {
        throw new Exception(
            "Erreur insertion dépense : "
            . $stmtDepense->error
        );
    }

    $id_depense = $connexion->insert_id;

    echo "Dépense enregistrée. ID = "
         . $id_depense
         . "<br>";


    /* =====================================
       ENREGISTRER LE STOCK
       ===================================== */

    $sqlStock = "
        INSERT INTO stock_matiere_premiere
        (
            nom,
            quantite,
            unite,
            prix_achat,
            date_achat,
            id_depense
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmtStock = $connexion->prepare($sqlStock);

    if (!$stmtStock) {
        throw new Exception(
            "Erreur préparation stock : "
            . $connexion->error
        );
    }

    $stmtStock->bind_param(
        "sdsdsi",
        $nom,
        $quantite,
        $unite,
        $montant,
        $date,
        $id_depense
    );

    if (!$stmtStock->execute()) {
        throw new Exception(
            "Erreur insertion stock : "
            . $stmtStock->error
        );
    }

    echo "Matière première ajoutée au stock.<br>";


    /* =====================================
       VALIDER
       ===================================== */

    $connexion->commit();

    echo "<br>";
    echo "<h3 style='color:green;'>";

    echo "✅ Tout a été enregistré correctement !";

    echo "</h3>";

    echo "<a href='/../depenses/depenses.php'>Voir les dépenses</a>";


} catch (Exception $e) {

    $connexion->rollback();

    echo "<h3 style='color:red;'>";

    echo "❌ ERREUR : ";

    echo htmlspecialchars($e->getMessage());

    echo "</h3>";
}

?>