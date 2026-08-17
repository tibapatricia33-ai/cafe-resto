<?php

include "../../php/connexion.php";


/* =========================================================
   VÉRIFIER L'ID DU PRODUIT
   ========================================================= */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    die("Produit introuvable.");

}

$id = (int)$_GET['id'];


/* =========================================================
   RÉCUPÉRER LE PRODUIT
   ========================================================= */

$sql = "
    SELECT
        id_produit,
        nom,
        prix,
        quantite_stock,
        seuil_alerte
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


/* IMPORTANT : créer la variable $produit */

$produit = $resultat->fetch_assoc();


/* =========================================================
   MODIFICATION
   ========================================================= */

$message = "";
$succes = false;


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = trim($_POST['nom'] ?? "");
    $prix = trim($_POST['prix'] ?? "");
    $quantite_stock = trim($_POST['quantite_stock'] ?? "");
    $seuil_alerte = trim($_POST['seuil_alerte'] ?? "");


    /* Vérification */

    if (
        $nom === "" ||
        $prix === "" ||
        $quantite_stock === "" ||
        $seuil_alerte === ""
    ) {

        $message =
            "Veuillez remplir tous les champs.";

    } elseif (
        !is_numeric($prix) ||
        !is_numeric($quantite_stock) ||
        !is_numeric($seuil_alerte)
    ) {

        $message =
            "Veuillez entrer des valeurs numériques valides.";

    } elseif (
        $prix < 0 ||
        $quantite_stock < 0 ||
        $seuil_alerte < 0
    ) {

        $message =
            "Les valeurs ne peuvent pas être négatives.";

    } else {


        /* ==============================================
           MODIFIER LE PRODUIT
           ============================================== */

        $sqlUpdate = "
            UPDATE produit
            SET
                nom = ?,
                prix = ?,
                quantite_stock = ?,
                seuil_alerte = ?
            WHERE id_produit = ?
        ";

        $stmtUpdate =
            $connexion->prepare($sqlUpdate);


        $prix = (float)$prix;
        $quantite_stock = (int)$quantite_stock;
        $seuil_alerte = (int)$seuil_alerte;


        $stmtUpdate->bind_param(
            "sdiii",
            $nom,
            $prix,
            $quantite_stock,
            $seuil_alerte,
            $id
        );


        if ($stmtUpdate->execute()) {

            $succes = true;

            /* Mettre à jour les données affichées */

            $produit['nom'] = $nom;
            $produit['prix'] = $prix;
            $produit['quantite_stock'] =
                $quantite_stock;
            $produit['seuil_alerte'] =
                $seuil_alerte;

        } else {

            $message =
                "Erreur lors de la modification : "
                . $stmtUpdate->error;

        }

    }

}

?>

<!DOCTYPE html>

<html lang="fr">

<head>


<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>
    Modifier un produit - Café Resto
</title>


<!-- Bootstrap -->

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
    rel="stylesheet">


<!-- Bootstrap Icons -->

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<!-- CSS du projet -->

<link
    rel="stylesheet"
    href="../css/style.css">


<style>

    .form-card {

        max-width: 750px;

        margin: 30px auto;

        background: white;

        padding: 30px;

        border-radius: 15px;

        box-shadow:
            0 5px 20px
            rgba(0,0,0,0.08);

    }

    .titre {

        color: #198754;

        font-weight: bold;

        margin-bottom: 25px;

    }

    .form-label {

        font-weight: 600;

    }

    .btn-save {

        background: #198754;

        color: white;

        border: none;

    }

    .btn-save:hover {

        background: #146c43;

        color: white;

    }

</style>


</head>

<body>

<!-- =====================================================
     MENU LATERAL
     ===================================================== -->

<aside class="sidebar">


<div class="logo">

    ☕ <span>Café Resto</span>

</div>


<nav class="menu">

    <a href="dashboard.php">
        🏠 <span>Dashboard</span>
    </a>

    <a href="produits/produits.php" class="active">
        📦 <span>Produits</span>
    </a>

    <a href="commandes/commandes.php">
        📝 <span>Commandes</span>
    </a>

    <a href="employes/employes.php">
        👨‍🍳 <span>Employés</span>
    </a>

    <a href="clients/clients.php">
        👥 <span>Clients</span>
    </a>

    <a href="paiements/paiements.php">
        💳 <span>Paiements</span>
    </a>

    <a href="depenses/depenses.php">
        💰 <span>Dépenses</span>
    </a>

    <a href="factures.php">
        🧾 <span>Factures</span>
    </a>

    <a href="rapports.php">
        📊 <span>Rapports</span>
    </a>

</nav>


<a
    href="deconnexion.php"
    class="logout">

    🚪 <span>Déconnexion</span>

</a>


</aside>

<!-- =====================================================
     CONTENU PRINCIPAL
     ===================================================== -->

<main class="main-content">


<!-- TOPBAR -->

<div class="topbar">

    <span class="menu-icon">
        ☰
    </span>

    <span class="admin">
        👤 Administrateur ▾
    </span>

</div>



<div class="content">


    <div class="form-card">


        <h2 class="titre">

            <i class="bi bi-pencil-square"></i>

            Modifier le produit

        </h2>


        <!-- MESSAGE D'ERREUR -->

        <?php if ($message !== ""): ?>

            <div class="alert alert-danger">

                <?php

                echo htmlspecialchars($message);

                ?>

            </div>

        <?php endif; ?>


        <!-- MESSAGE DE SUCCÈS -->

        <?php if ($succes): ?>

            <div class="alert alert-success">

                <i class="bi bi-check-circle"></i>

                Produit modifié avec succès.

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="">


            <!-- NOM -->

            <div class="mb-3">

                <label class="form-label">

                    Nom du produit

                </label>

                <input
                    type="text"
                    name="nom"
                    class="form-control"
                    value="<?php

                        echo htmlspecialchars(
                            $produit['nom']
                        );

                    ?>"
                    >

            </div>



            <!-- PRIX -->

            <div class="mb-3">

                <label class="form-label">

                    Prix

                </label>

                <div class="input-group">

                    <input
                        type="number"
                        name="prix"
                        class="form-control"
                        step="0.01"
                        min="0"
                        value="<?php

                            echo htmlspecialchars(
                                $produit['prix']
                            );

                        ?>"
                        >

                    <span class="input-group-text">

                        FCFA

                    </span>

                </div>

            </div>



            <!-- STOCK -->

            <div class="mb-3">

                <label class="form-label">

                    Quantité en stock

                </label>

                <input
                    type="number"
                    name="quantite_stock"
                    class="form-control"
                    min="0"
                    value="<?php

                        echo htmlspecialchars(
                            $produit[
                                'quantite_stock'
                            ]
                        );

                    ?>"
                    >

            </div>



            <!-- SEUIL -->

            <div class="mb-4">

                <label class="form-label">

                    Seuil d'alerte

                </label>

                <input
                    type="number"
                    name="seuil_alerte"
                    class="form-control"
                    min="0"
                    value="<?php

                        echo htmlspecialchars(
                            $produit[
                                'seuil_alerte'
                            ]
                        );

                    ?>"
                    >

                <small class="text-muted">

                    Une alerte sera affichée lorsque
                    le stock sera inférieur ou égal
                    à ce nombre.

                </small>

            </div>



            <!-- BOUTONS -->

            <div
                class="d-flex gap-2">


                <button
                    type="submit"
                    class="btn btn-save">

                    <i class="bi bi-check-circle"></i>

                    Enregistrer les modifications

                </button>


                <a
                    href="produits.php"
                    class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i>

                    Retour

                </a>


            </div>


        </form>


    </div>


</div>


</main>

</body>

</html>
