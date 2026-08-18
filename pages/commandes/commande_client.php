<?php

include "../../php/connexion.php";


// ============================================================
// CRÉATION AUTOMATIQUE DE LA COLONNE IMPRIMÉ
// ============================================================

$check = $connexion->query("
    SHOW COLUMNS FROM commande LIKE 'imprime'
");

if ($check && $check->num_rows == 0) {
    $connexion->query("
        ALTER TABLE commande
        ADD imprime TINYINT(1) NOT NULL DEFAULT 0
    ");
}


// ============================================================
// FONCTION SÉCURISÉE
// ============================================================

function e($texte)
{
    return htmlspecialchars($texte ?? '', ENT_QUOTES, 'UTF-8');
}


// ============================================================
// SUPPRESSION
// ============================================================

if (isset($_GET['supprimer'])) {

    $id = (int) $_GET['supprimer'];

    // Supprimer d'abord les produits liés
    $stmt = $connexion->prepare("
        DELETE FROM commande_produit
        WHERE id_commande = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();


    // Puis supprimer la commande
    $stmt = $connexion->prepare("
        DELETE FROM commande
        WHERE id_commande = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();


    header("Location: commandes.php");
    exit;
}


// ============================================================
// LIVRER UNE COMMANDE
// ============================================================

if (isset($_GET['livrer'])) {

    $id = (int) $_GET['livrer'];

    $stmt = $connexion->prepare("
        UPDATE commande
        SET statut = 'Livrée'
        WHERE id_commande = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();


    header("Location: commandes.php");
    exit;
}


// ============================================================
// MARQUER COMME IMPRIMÉE
// ============================================================

if (isset($_GET['imprime'])) {

    $id = (int) $_GET['imprime'];

    $stmt = $connexion->prepare("
        UPDATE commande
        SET imprime = 1
        WHERE id_commande = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();


    header("Location: commandes.php");
    exit;
}


// ============================================================
// AJOUT D'UNE COMMANDE
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && $_POST['action'] === 'ajouter') {


    $nomClient = trim($_POST['nom_client'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');

    $type = $_POST['type'] ?? 'Sur place';

    $produits = $_POST['produit'] ?? [];
    $quantites = $_POST['quantite'] ?? [];


    // --------------------------------------------------------
    // VÉRIFICATION
    // --------------------------------------------------------

    if ($nomClient === '') {
        die("Le nom du client est obligatoire.");
    }


    // --------------------------------------------------------
    // RECHERCHE DU CLIENT
    // --------------------------------------------------------

    $idClient = null;

    $stmt = $connexion->prepare("
        SELECT id_client
        FROM client
        WHERE telephone = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $telephone);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($client = $result->fetch_assoc()) {

        $idClient = $client['id_client'];

    } else {

        // Création du client
        $stmtClient = $connexion->prepare("
            INSERT INTO client
            (nom, telephone, adresse)
            VALUES (?, ?, ?)
        ");

        $stmtClient->bind_param(
            "sss",
            $nomClient,
            $telephone,
            $adresse
        );

        $stmtClient->execute();

        $idClient = $stmtClient->insert_id;

        $stmtClient->close();
    }

    $stmt->close();


    // --------------------------------------------------------
    // CALCUL DE LA COMMANDE
    // --------------------------------------------------------

    $total = 0;
    $quantiteTotale = 0;
    $details = [];

    $lignes = [];


    foreach ($produits as $index => $idProduit) {

        $idProduit = (int) $idProduit;
        $qte = (int) ($quantites[$index] ?? 0);

        if ($idProduit <= 0 || $qte <= 0) {
            continue;
        }


        $stmtProduit = $connexion->prepare("
            SELECT nom, prix, quantite_stock
            FROM produit
            WHERE id_produit = ?
        ");

        $stmtProduit->bind_param("i", $idProduit);
        $stmtProduit->execute();

        $resultProduit = $stmtProduit->get_result();

        $produit = $resultProduit->fetch_assoc();

        $stmtProduit->close();


        if (!$produit) {
            continue;
        }


        $sousTotal = $produit['prix'] * $qte;

        $total += $sousTotal;

        $quantiteTotale += $qte;


        $details[] =
            $produit['nom']
            . " x"
            . $qte;


        $lignes[] = [
            'id_produit' => $idProduit,
            'quantite' => $qte
        ];
    }


    if (count($lignes) === 0) {
        die("Veuillez sélectionner au moins un produit.");
    }


    $nomCommande = implode(" + ", $details);


    // --------------------------------------------------------
    // INSERTION COMMANDE
    // --------------------------------------------------------

    $statut = "En attente";
    $date = date("Y-m-d");


    $stmt = $connexion->prepare("
        INSERT INTO commande
        (
            type,
            statut,
            date,
            nom,
            quantite,
            montant,
            id_client
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");


    $stmt->bind_param(
        "ssssidi",
        $type,
        $statut,
        $date,
        $nomCommande,
        $quantiteTotale,
        $total,
        $idClient
    );


    $stmt->execute();

    $idCommande = $stmt->insert_id;

    $stmt->close();


    // --------------------------------------------------------
    // ENREGISTREMENT DES PRODUITS DE LA COMMANDE
    // --------------------------------------------------------

    foreach ($lignes as $ligne) {

        $stmt = $connexion->prepare("
            INSERT INTO commande_produit
            (
                id_commande,
                id_produit,
                quantite
            )
            VALUES (?, ?, ?)
        ");

        $stmt->bind_param(
            "iii",
            $idCommande,
            $ligne['id_produit'],
            $ligne['quantite']
        );

        $stmt->execute();
        $stmt->close();
    }


    header("Location: commandes.php?nouvelle=1");
    exit;
}


// ============================================================
// MODIFICATION
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && $_POST['action'] === 'modifier') {


    $idCommande = (int) $_POST['id_commande'];

    $nomClient = trim($_POST['nom_client']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);

    $type = $_POST['type'];

    $produits = $_POST['produit'] ?? [];
    $quantites = $_POST['quantite'] ?? [];


    // --------------------------------------------------------
    // CLIENT
    // --------------------------------------------------------

    $idClient = null;

    $stmt = $connexion->prepare("
        SELECT id_client
        FROM client
        WHERE telephone = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $telephone);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($client = $result->fetch_assoc()) {

        $idClient = $client['id_client'];

    } else {

        $stmtClient = $connexion->prepare("
            INSERT INTO client
            (nom, telephone, adresse)
            VALUES (?, ?, ?)
        ");

        $stmtClient->bind_param(
            "sss",
            $nomClient,
            $telephone,
            $adresse
        );

        $stmtClient->execute();

        $idClient = $stmtClient->insert_id;

        $stmtClient->close();
    }

    $stmt->close();


    // --------------------------------------------------------
    // CALCUL
    // --------------------------------------------------------

    $total = 0;
    $quantiteTotale = 0;
    $details = [];
    $lignes = [];


    foreach ($produits as $index => $idProduit) {

        $idProduit = (int) $idProduit;
        $qte = (int) ($quantites[$index] ?? 0);

        if ($idProduit <= 0 || $qte <= 0) {
            continue;
        }


        $stmtProduit = $connexion->prepare("
            SELECT nom, prix
            FROM produit
            WHERE id_produit = ?
        ");

        $stmtProduit->bind_param("i", $idProduit);
        $stmtProduit->execute();

        $resultProduit = $stmtProduit->get_result();

        $produit = $resultProduit->fetch_assoc();

        $stmtProduit->close();


        if (!$produit) {
            continue;
        }


        $total += $produit['prix'] * $qte;

        $quantiteTotale += $qte;

        $details[] =
            $produit['nom']
            . " x"
            . $qte;


        $lignes[] = [
            'id_produit' => $idProduit,
            'quantite' => $qte
        ];
    }


    $nomCommande = implode(" + ", $details);


    // --------------------------------------------------------
    // UPDATE COMMANDE
    // --------------------------------------------------------

    $stmt = $connexion->prepare("
        UPDATE commande
        SET
            type = ?,
            nom = ?,
            quantite = ?,
            montant = ?,
            id_client = ?
        WHERE id_commande = ?
    ");


    $stmt->bind_param(
        "ssidii",
        $type,
        $nomCommande,
        $quantiteTotale,
        $total,
        $idClient,
        $idCommande
    );


    $stmt->execute();

    $stmt->close();


    // --------------------------------------------------------
    // SUPPRESSION DES ANCIENS PRODUITS
    // --------------------------------------------------------

    $stmt = $connexion->prepare("
        DELETE FROM commande_produit
        WHERE id_commande = ?
    ");

    $stmt->bind_param("i", $idCommande);
    $stmt->execute();
    $stmt->close();


    // --------------------------------------------------------
    // NOUVEAUX PRODUITS
    // --------------------------------------------------------

    foreach ($lignes as $ligne) {

        $stmt = $connexion->prepare("
            INSERT INTO commande_produit
            (id_commande, id_produit, quantite)
            VALUES (?, ?, ?)
        ");

        $stmt->bind_param(
            "iii",
            $idCommande,
            $ligne['id_produit'],
            $ligne['quantite']
        );

        $stmt->execute();
        $stmt->close();
    }


    header("Location: commandes.php");
    exit;
}


// ============================================================
// RÉCUPÉRER LES PRODUITS
// ============================================================

$produitsDB = [];

$resultProduits = $connexion->query("
    SELECT
        id_produit,
        nom,
        prix,
        quantite_stock
    FROM produit
    ORDER BY nom ASC
");


if ($resultProduits) {

    while ($p = $resultProduits->fetch_assoc()) {

        $produitsDB[] = $p;
    }
}


// ============================================================
// FORMULAIRE DE MODIFICATION
// ============================================================

$commandeModification = null;

if (isset($_GET['modifier'])) {

    $id = (int) $_GET['modifier'];


    $stmt = $connexion->prepare("
        SELECT
            commande.*,
            client.nom AS nom_client,
            client.telephone,
            client.adresse
        FROM commande
        LEFT JOIN client
            ON commande.id_client = client.id_client
        WHERE commande.id_commande = ?
    ");


    $stmt->bind_param("i", $id);

    $stmt->execute();

    $result = $stmt->get_result();

    $commandeModification = $result->fetch_assoc();

    $stmt->close();
}


// ============================================================
// RECHERCHE
// ============================================================

$recherche = trim($_GET['recherche'] ?? '');


if ($recherche !== '') {

    $sql = "
        SELECT
            commande.*,
            client.nom AS nom_client,
            client.telephone,
            client.adresse
        FROM commande

        LEFT JOIN client
            ON commande.id_client = client.id_client

        WHERE
            client.nom LIKE ?
            OR client.telephone LIKE ?
            OR commande.nom LIKE ?
            OR commande.type LIKE ?
            OR commande.statut LIKE ?

        ORDER BY commande.id_commande DESC
    ";


    $stmt = $connexion->prepare($sql);

    $mot = "%" . $recherche . "%";

    $stmt->bind_param(
        "sssss",
        $mot,
        $mot,
        $mot,
        $mot,
        $mot
    );

    $stmt->execute();

    $commandes = $stmt->get_result();

} else {

    $commandes = $connexion->query("
        SELECT
            commande.*,
            client.nom AS nom_client,
            client.telephone,
            client.adresse
        FROM commande

        LEFT JOIN client
            ON commande.id_client = client.id_client

        ORDER BY commande.id_commande DESC
    ");
}


// ============================================================
// STATISTIQUES
// ============================================================

$totalCommandes = 0;
$attente = 0;
$livrees = 0;


$result = $connexion->query("
    SELECT COUNT(*) AS total
    FROM commande
");

if ($result) {
    $totalCommandes = $result->fetch_assoc()['total'];
}


$result = $connexion->query("
    SELECT COUNT(*) AS total
    FROM commande
    WHERE statut = 'En attente'
");

if ($result) {
    $attente = $result->fetch_assoc()['total'];
}


$result = $connexion->query("
    SELECT COUNT(*) AS total
    FROM commande
    WHERE statut = 'Livrée'
");

if ($result) {
    $livrees = $result->fetch_assoc()['total'];
}


// ============================================================
// DATE
// ============================================================

$jours = [
    'Sunday' => 'Dimanche',
    'Monday' => 'Lundi',
    'Tuesday' => 'Mardi',
    'Wednesday' => 'Mercredi',
    'Thursday' => 'Jeudi',
    'Friday' => 'Vendredi',
    'Saturday' => 'Samedi'
];

$mois = [
    'January' => 'janvier',
    'February' => 'février',
    'March' => 'mars',
    'April' => 'avril',
    'May' => 'mai',
    'June' => 'juin',
    'July' => 'juillet',
    'August' => 'août',
    'September' => 'septembre',
    'October' => 'octobre',
    'November' => 'novembre',
    'December' => 'décembre'
];


$jour = $jours[date('l')];
$moisActuel = $mois[date('F')];

$dateComplete =
    $jour
    . " "
    . date('d')
    . " "
    . $moisActuel
    . " "
    . date('Y');

?>

<!DOCTYPE html>

<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Commandes - Café Resto</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
rel="stylesheet">


<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<link rel="stylesheet" href="../../css/style.css">


<style>

/* =========================================================
   PAGE
   ========================================================= */

.content {
    padding: 25px;
}


/* =========================================================
   NOTIFICATION
   ========================================================= */

.notification {
    position: relative;
    font-size: 22px;
    color: #198754;
    text-decoration: none;
    margin-right: 25px;
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -10px;
    background: red;
    color: white;
    font-size: 10px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}


/* =========================================================
   STATISTIQUES
   ========================================================= */

.stat-card {
    border: none;
    border-radius: 15px;
}


/* =========================================================
   TABLE
   ========================================================= */

.table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,.08);
    overflow: hidden;
}


.table th {
    background: #198754;
    color: white;
    white-space: nowrap;
}


.table td {
    vertical-align: middle;
}


/* =========================================================
   CLIENT
   ========================================================= */

.client-name {
    font-weight: bold;
    font-size: 15px;
}


.client-phone {
    color: #666;
    font-size: 13px;
}


/* =========================================================
   COMMANDE
   ========================================================= */

.details-commande {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
    min-width: 220px;
}


.montant {
    font-weight: bold;
    color: #198754;
    white-space: nowrap;
}


/* =========================================================
   ICONES ACTIONS
   ========================================================= */

.action-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 2px;
    text-decoration: none;
}


.action-edit {
    background: #0d6efd;
    color: white;
}


.action-delete {
    background: #dc3545;
    color: white;
}


.action-print {
    background: #6c757d;
    color: white;
}


.action-deliver {
    background: #198754;
    color: white;
}


.action-icon:hover {
    opacity: .8;
    color: white;
}


/* =========================================================
   FORMULAIRE
   ========================================================= */

.order-form {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 3px 15px rgba(0,0,0,.08);
}


.product-line {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 10px;
}


.total-box {
    background: #198754;
    color: white;
    padding: 15px;
    border-radius: 10px;
    font-size: 20px;
    font-weight: bold;
}


</style>

</head>


<body>


<!-- =======================================================
     MENU VERT
     ======================================================= -->

     <?php
     
     include "../../partials/sidebar.php";

     ?>



<!-- =======================================================
     CONTENU
     ======================================================= -->

<main class="main-content">


<!-- TOPBAR -->

<div class="topbar">


<span class="menu-icon">
    ☰
</span>


<div class="d-flex align-items-center">


<a href="commandes.php"
   class="notification"
   title="Notifications">

    <i class="bi bi-bell-fill"></i>

    <?php if (isset($_GET['nouvelle'])) { ?>

        <span class="notification-badge">
            1
        </span>

    <?php } ?>

</a>


<span class="admin">
    👤 Administrateur
</span>


</div>


</div>


<!-- CONTENU -->

<div class="content">


<!-- =======================================================
     TITRE
     ======================================================= -->

<div class="d-flex justify-content-between align-items-center mb-4">


<div>

<h2>
    Gestion des commandes
</h2>

<p class="text-muted mb-0">
    <?= e($dateComplete) ?>
</p>

</div>


<a href="commandes.php?ajouter=1"
   class="btn btn-success">

    <i class="bi bi-plus-circle"></i>

    Ajouter une commande

</a>


</div>


<!-- =======================================================
     STATISTIQUES
     ======================================================= -->

<div class="row mb-4">


<div class="col-md-4">

<div class="card stat-card bg-primary text-white shadow">

<div class="card-body">

<h6>
    TOTAL COMMANDES
</h6>

<h2>
    <?= $totalCommandes ?>
</h2>

</div>

</div>

</div>


<div class="col-md-4">

<div class="card stat-card bg-warning text-dark shadow">

<div class="card-body">

<h6>
    EN ATTENTE
</h6>

<h2>
    <?= $attente ?>
</h2>

</div>

</div>

</div>


<div class="col-md-4">

<div class="card stat-card bg-success text-white shadow">

<div class="card-body">

<h6>
    LIVRÉES
</h6>

<h2>
    <?= $livrees ?>
</h2>

</div>

</div>

</div>


</div>


<!-- =======================================================
     FORMULAIRE AJOUT / MODIFICATION
     ======================================================= -->

<?php if (isset($_GET['ajouter']) || $commandeModification) { ?>


<div class="order-form">


<div class="d-flex justify-content-between align-items-center mb-4">

<h4>

<?php

if ($commandeModification) {
    echo "Modifier la commande";
} else {
    echo "Nouvelle commande";
}

?>

</h4>


<a href="commandes.php"
   class="btn btn-secondary">

    <i class="bi bi-x"></i>

</a>


</div>


<form method="POST">


<?php if ($commandeModification) { ?>

<input
type="hidden"
name="action"
value="modifier">


<input
type="hidden"
name="id_commande"
value="<?= $commandeModification['id_commande'] ?>">

<?php } else { ?>

<input
type="hidden"
name="action"
value="ajouter">

<?php } ?>


<!-- CLIENT -->

<div class="row">


<div class="col-md-4 mb-3">

<label class="form-label">
    Nom du client
</label>

<input
type="text"
name="nom_client"
class="form-control"
required
value="<?= e($commandeModification['nom_client'] ?? '') ?>">

</div>


<div class="col-md-4 mb-3">

<label class="form-label">
    Téléphone
</label>

<input
type="text"
name="telephone"
class="form-control"
required
value="<?= e($commandeModification['telephone'] ?? '') ?>">

</div>


<div class="col-md-4 mb-3">

<label class="form-label">
    Adresse
</label>

<input
type="text"
name="adresse"
class="form-control"
value="<?= e($commandeModification['adresse'] ?? '') ?>">

</div>


</div>


<!-- TYPE -->

<div class="mb-3">

<label class="form-label">
    Type de commande
</label>


<select
name="type"
class="form-select"
required>


<option value="Sur place"
<?php
if (($commandeModification['type'] ?? '') === 'Sur place')
    echo 'selected';
?>>
    Sur place
</option>


<option value="À emporter"
<?php
if (($commandeModification['type'] ?? '') === 'À emporter')
    echo 'selected';
?>>
    À emporter
</option>


<option value="Livraison"
<?php
if (($commandeModification['type'] ?? '') === 'Livraison')
    echo 'selected';
?>>
    Livraison
</option>


</select>

</div>


<!-- PRODUITS -->

<h5 class="mt-4 mb-3">
    🍽️ Produits commandés
</h5>


<div id="produits-container">


<div class="product-line row">


<div class="col-md-7">

<label>
    Produit
</label>


<select
name="produit[]"
class="form-select produit-select"
onchange="calculerTotal()"
required>


<option value="">
    Choisir un produit
</option>


<?php foreach ($produitsDB as $p) { ?>

<option
value="<?= $p['id_produit'] ?>"
data-prix="<?= $p['prix'] ?>">

<?= e($p['nom']) ?>
-
<?= number_format($p['prix'], 0, ',', ' ') ?>
FCFA

</option>

<?php } ?>


</select>

</div>


<div class="col-md-3">

<label>
    Quantité
</label>


<input
type="number"
name="quantite[]"
class="form-control quantite"
min="1"
value="1"
oninput="calculerTotal()"
required>

</div>


<div class="col-md-2 d-flex align-items-end">

<button
type="button"
class="btn btn-danger w-100"
onclick="supprimerLigne(this)">

<i class="bi bi-trash"></i>

</button>

</div>


</div>


</div>


<button
type="button"
class="btn btn-outline-success mb-4"
onclick="ajouterProduit()">

<i class="bi bi-plus"></i>

Ajouter un produit

</button>


<!-- TOTAL -->

<div class="total-box mb-4">

Total :

<span id="total">
0
</span>

FCFA

</div>


<button
type="submit"
class="btn btn-success">

<i class="bi bi-check-circle"></i>


<?php

if ($commandeModification) {
    echo "Enregistrer les modifications";
} else {
    echo "Enregistrer la commande";
}

?>

</button>


</form>


</div>


<?php } ?>


<!-- =======================================================
     RECHERCHE
     ======================================================= -->

<form
method="GET"
action="commandes.php"
class="d-flex gap-2 mb-4">


<input
type="text"
name="recherche"
class="form-control"
placeholder="Rechercher un client, téléphone, commande..."
value="<?= e($recherche) ?>">


<button
type="submit"
class="btn btn-success">

<i class="bi bi-search"></i>

</button>


<?php if ($recherche !== '') { ?>

<a href="commandes.php"
   class="btn btn-secondary">

    <i class="bi bi-x"></i>

</a>

<?php } ?>


</form>


<!-- =======================================================
     TABLEAU
     ======================================================= -->

<div class="table-container">


<div class="table-responsive">


<table class="table table-hover mb-0">


<thead>

<tr>

<th>Client</th>

<th>Commande</th>

<th>Quantité</th>

<th>Montant</th>

<th>Type</th>

<th>Date</th>

<th>Statut</th>

<th>Actions</th>

</tr>

</thead>


<tbody>


<?php

if ($commandes && $commandes->num_rows > 0) {

while ($commande = $commandes->fetch_assoc()) {

?>


<tr>


<!-- CLIENT -->

<td>

<div class="client-name">

<?= e($commande['nom_client'] ?? 'Client') ?>

</div>


<div class="client-phone">

<i class="bi bi-telephone"></i>

<?= e($commande['telephone'] ?? '') ?>

</div>


</td>


<!-- COMMANDE -->

<td>

<div class="details-commande">

<?= e($commande['nom']) ?>

</div>

</td>


<!-- QUANTITÉ -->

<td>

<strong>

<?= e($commande['quantite']) ?>

</strong>

</td>


<!-- MONTANT -->

<td>

<span class="montant">

<?= number_format(
    (float)$commande['montant'],
    0,
    ',',
    ' '
) ?>

FCFA

</span>

</td>


<!-- TYPE -->

<td>

<?= e($commande['type']) ?>

</td>


<!-- DATE -->

<td>

<?= e($commande['date']) ?>

</td>


<!-- STATUT -->

<td>


<?php if ($commande['statut'] === 'Livrée') { ?>

<span class="badge bg-success">

<i class="bi bi-circle-fill"></i>

Livrée

</span>


<?php } else { ?>

<span class="badge bg-warning text-dark">

<i class="bi bi-clock"></i>

En attente

</span>

<?php } ?>


</td>


<!-- ACTIONS -->

<td>

<div class="d-flex flex-wrap">


<!-- MODIFIER -->

<a
href="commandes.php?modifier=<?= $commande['id_commande'] ?>"
class="action-icon action-edit"
title="Modifier">

<i class="bi bi-pencil-fill"></i>

</a>


<!-- SUPPRIMER -->

<a
href="commandes.php?supprimer=<?= $commande['id_commande'] ?>"
class="action-icon action-delete"
title="Supprimer"
onclick="return confirm('Voulez-vous vraiment supprimer cette commande ?');">

<i class="bi bi-trash-fill"></i>

</a>


<!-- IMPRIMER -->

<a
href="commandes.php?imprime=<?= $commande['id_commande'] ?>"
class="action-icon action-print"
title="Imprimer">

<i class="bi bi-printer-fill"></i>

</a>


<!-- LIVRER -->

<?php if ($commande['statut'] !== 'Livrée') { ?>


<a
href="commandes.php?livrer=<?= $commande['id_commande'] ?>"
class="action-icon action-deliver"
title="Livrer"
onclick="return confirm('Confirmer la livraison de cette commande ?');">

<i class="bi bi-check-lg"></i>

</a>


<?php } else { ?>


<span
class="action-icon action-deliver"
title="Commande livrée">

<i class="bi bi-check-circle-fill"></i>

</span>


<?php } ?>


<!-- INDICATEUR IMPRIMÉ -->

<?php if (!empty($commande['imprime'])) { ?>

<span
class="badge bg-secondary m-1"
title="Facture imprimée">

<i class="bi bi-printer-fill"></i>

</span>

<?php } ?>


</div>

</td>


</tr>


<?php

}

}

?>


</tbody>


</table>


</div>


</div>


</div>


</main>


<script>


// ============================================================
// CALCUL DU TOTAL
// ============================================================

function calculerTotal() {

    let total = 0;


    const lignes =
        document.querySelectorAll('.product-line');


    lignes.forEach(function(ligne) {


        const select =
            ligne.querySelector('.produit-select');


        const quantite =
            ligne.querySelector('.quantite');


        if (!select || !quantite) {
            return;
        }


        const option =
            select.options[select.selectedIndex];


        const prix =
            parseFloat(
                option?.dataset?.prix || 0
            );


        const qte =
            parseInt(
                quantite.value || 0
            );


        total += prix * qte;

    });


    document.getElementById('total').innerText =
        new Intl.NumberFormat('fr-FR').format(total);

}


// ============================================================
// AJOUTER UNE LIGNE
// ============================================================

function ajouterProduit() {


    const container =
        document.getElementById('produits-container');


    const premiereLigne =
        container.querySelector('.product-line');


    const nouvelleLigne =
        premiereLigne.cloneNode(true);


    nouvelleLigne
        .querySelector('.produit-select')
        .value = '';


    nouvelleLigne
        .querySelector('.quantite')
        .value = 1;


    container.appendChild(nouvelleLigne);


    calculerTotal();

}


// ============================================================
// SUPPRIMER UNE LIGNE
// ============================================================

function supprimerLigne(bouton) {


    const lignes =
        document.querySelectorAll('.product-line');


    if (lignes.length <= 1) {

        bouton
            .closest('.product-line')
            .querySelector('.produit-select')
            .value = '';

        bouton
            .closest('.product-line')
            .querySelector('.quantite')
            .value = 1;

    } else {

        bouton
            .closest('.product-line')
            .remove();

    }


    calculerTotal();

}


document.addEventListener(
    'DOMContentLoaded',
    calculerTotal
);

</script>


</body>

</html>

