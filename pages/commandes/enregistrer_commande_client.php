<?php

include "../../php/connexion.php";

/* =========================================
   RÉCUPÉRER LES DONNÉES DU FORMULAIRE
   ========================================= */

$nom = trim($_POST['nom']);
$telephone = trim($_POST['telephone']);
$adresse = trim($_POST['adresse']);
$type = $_POST['type'];

$panier = json_decode($_POST['panier'], true);


/* =========================================
   VÉRIFICATION
   ========================================= */

if (
    empty($nom) ||
    empty($telephone) ||
    empty($adresse) ||
    empty($type) ||
    empty($panier)
) {
    die("Informations incomplètes.");
}


/* =========================================
   TRANSACTION
   ========================================= */

$connexion->begin_transaction();

try {

    /* =====================================
       1. ENREGISTRER LE CLIENT
       ===================================== */

    $sql = "INSERT INTO client
            (nom, telephone, adresse)
            VALUES (?, ?, ?)";

    $stmt = $connexion->prepare($sql);

    $stmt->bind_param(
        "sss",
        $nom,
        $telephone,
        $adresse
    );

    $stmt->execute();

    $id_client = $connexion->insert_id;


    /* =====================================
       2. CALCULER LA QUANTITÉ TOTALE
       ===================================== */

    $quantiteTotale = 0;

    foreach ($panier as $produit) {

        $quantiteTotale += (int)$produit['quantite'];

    }


    /* =====================================
       3. CRÉER LA COMMANDE
       ===================================== */

    $statut = "En attente";

    $date = date("Y-m-d");

    $sql = "INSERT INTO commande
            (type, statut, date, quantite, id_client)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $connexion->prepare($sql);

    $stmt->bind_param(
        "sssii",
        $type,
        $statut,
        $date,
        $quantiteTotale,
        $id_client
    );

    $stmt->execute();

    $id_commande = $connexion->insert_id;


    /* =====================================
       4. ENREGISTRER LES PRODUITS COMMANDÉS
       ===================================== */

    $sql = "INSERT INTO commande_produit
            (id_commande, id_produit, quantite, prix)
            VALUES (?, ?, ?, ?)";

    $stmtProduit = $connexion->prepare($sql);


    foreach ($panier as $produit) {

        $id_produit = (int)$produit['id'];
        $quantite = (int)$produit['quantite'];
        $nom = $produit['nom'];
        $montant = $produit['montant'];
        $prix = (float)$produit['prix'];


        $stmtProduit->bind_param(
            "iiid",
            $id_commande,
            $id_produit,
            $nom,
            $quantite,
            $prix
        );

        $stmtProduit->execute();


        /* =================================
           5. DIMINUER LE STOCK
           ================================= */

        $sqlStock = "UPDATE produit
                     SET quantite_stock =
                         quantite_stock - ?
                     WHERE id_produit = ?
                     AND quantite_stock >= ?";

        $stmtStock = $connexion->prepare($sqlStock);

        $stmtStock->bind_param(
            "iii",
            $quantite,
            $id_produit,
            $quantite
        );

        $stmtStock->execute();


        if ($stmtStock->affected_rows === 0) {

            throw new Exception(
                "Stock insuffisant pour le produit : "
                . $produit['nom']
            );

        }

    }


    /* =====================================
       6. VALIDER LA TRANSACTION
       ===================================== */

    $connexion->commit();


} catch (Exception $e) {

    /* Annuler les modifications en cas d'erreur */

    $connexion->rollback();

    die(
        "Erreur lors de l'enregistrement de la commande : "
        . $e->getMessage()
    );
}


/* =========================================
   CALCULER LE TOTAL POUR L'AFFICHAGE
   ========================================= */

$total = 0;

foreach ($panier as $produit) {

    $total +=
        (float)$produit['prix']
        * (int)$produit['quantite'];

}

?>


<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Commande confirmée - Café Resto
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body class="bg-light">


<div class="container py-5">

    <div
        class="card shadow border-0 mx-auto"
        style="max-width: 650px;">

        <div class="card-body p-5">


            <!-- SUCCÈS -->

            <div class="text-center">

                <div
                    class="text-success"
                    style="font-size:70px;">

                    ✓

                </div>

                <h1 class="text-success">

                    Commande confirmée !

                </h1>

                <p class="lead">

                    Merci
                    <strong>
                        <?php echo htmlspecialchars($nom); ?>
                    </strong>

                </p>

                <p>

                    Votre commande a bien été enregistrée.

                </p>

            </div>


            <!-- NUMÉRO DE COMMANDE -->

            <div class="alert alert-success text-center">

                Numéro de commande :

                <strong>

                    #<?php echo $id_commande; ?>

                </strong>

            </div>


            <!-- RÉSUMÉ -->

            <h5 class="mt-4 mb-3">

                🛒 Votre commande

            </h5>


            <?php foreach ($panier as $produit) {

                $sousTotal =
                    (float)$produit['prix']
                    * (int)$produit['quantite'];

            ?>

                <div
                    class="d-flex justify-content-between
                           border-bottom py-2">

                    <span>

                        <?php
                        echo htmlspecialchars(
                            $produit['nom']
                        );
                        ?>

                        ×

                        <?php
                        echo $produit['quantite'];
                        ?>

                    </span>

                    <strong>

                        <?php
                        echo number_format(
                            $sousTotal,
                            0,
                            ',',
                            ' '
                        );
                        ?>

                        FCFA

                    </strong>

                </div>

            <?php } ?>


            <!-- TOTAL -->

            <div
                class="d-flex justify-content-between
                       mt-3 fs-5">

                <strong>
                    TOTAL
                </strong>

                <strong class="text-success">

                    <?php
                    echo number_format(
                        $total,
                        0,
                        ',',
                        ' '
                    );
                    ?>

                    FCFA

                </strong>

            </div>


            <!-- STATUT -->

            <div class="text-center mt-4">

                <span class="badge bg-warning text-dark fs-6">

                    En attente

                </span>

            </div>


            <!-- RETOUR -->

            <div class="text-center mt-4">

                <a
                    href="client_commande.php"
                    class="btn btn-success">

                    Retourner au menu

                </a>

            </div>


        </div>

    </div>

</div>


</body>

</html>