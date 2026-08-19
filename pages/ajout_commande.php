<?php

session_start();

include "../php/connexion.php";


/*
=====================================================
INITIALISATION DU PANIER
=====================================================
*/

if (
    !isset($_SESSION['panier']) ||
    !is_array($_SESSION['panier'])
) {
    $_SESSION['panier'] = [];
}


/*
=====================================================
REGROUPEMENT DU PANIER
=====================================================
*/

$panier = [];

foreach ($_SESSION['panier'] as $article) {

    if (
        !isset($article['id_produit']) ||
        !isset($article['nom']) ||
        !isset($article['prix'])
    ) {
        continue;
    }


    $idProduit = (int)$article['id_produit'];

    $quantite = isset($article['quantite'])
        ? (int)$article['quantite']
        : 1;


    if ($quantite < 1) {
        $quantite = 1;
    }


    /*
    Si le produit existe déjà dans le panier,
    on additionne les quantités.
    */

    if (isset($panier[$idProduit])) {

        $panier[$idProduit]['quantite'] += $quantite;

    } else {

        $panier[$idProduit] = [

            'id_produit' => $idProduit,

            'nom' => $article['nom'],

            'prix' => (float)$article['prix'],

            'quantite' => $quantite

        ];
    }
}


/*
=====================================================
CALCUL DU TOTAL
=====================================================
*/

$total = 0;

$quantiteTotale = 0;

$detailsCommande = [];


foreach ($panier as $article) {

    $prix = (float)$article['prix'];

    $quantite = (int)$article['quantite'];

    $sousTotal = $prix * $quantite;


    $total += $sousTotal;

    $quantiteTotale += $quantite;


    if ($quantite > 1) {

        $detailsCommande[] =
            $article['nom'] . ' x' . $quantite;

    } else {

        $detailsCommande[] =
            $article['nom'];
    }
}


$details = implode(
    ' + ',
    $detailsCommande
);


/*
=====================================================
ENREGISTREMENT DE LA COMMANDE
=====================================================
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    /*
    =================================================
    VÉRIFIER QUE LE PANIER N'EST PAS VIDE
    =================================================
    */

    if (empty($panier)) {

        die("Le panier est vide.");

    }


    /*
    =================================================
    RÉCUPÉRATION DES INFORMATIONS CLIENT
    =================================================
    */

    $nom = trim(
        $_POST['nom_client'] ?? ''
    );


    $numero = trim(
        $_POST['numero'] ?? ''
    );


    $email = trim(
        $_POST['email'] ?? ''
    );


    $type = trim(
        $_POST['type'] ?? ''
    );


    $statut = trim(
        $_POST['statut'] ?? ''
    );


    $date = $_POST['date']
        ?? date('Y-m-d');


    /*
    =================================================
    VÉRIFICATIONS
    =================================================
    */

    if ($nom === '') {

        die(
            "Le nom du client est obligatoire."
        );

    }


    if ($type === '') {

        die(
            "Le type de commande est obligatoire."
        );

    }


    if ($statut === '') {

        die(
            "Le statut de la commande est obligatoire."
        );

    }


    if ($date === '') {

        $date = date('Y-m-d');

    }


    /*
    =================================================
    TRANSACTION
    =================================================

    Toutes les opérations seront validées ensemble.
    Si une erreur survient, tout sera annulé.
    */

    mysqli_begin_transaction($connexion);


    try {


        /*
        =================================================
        INSERTION DU CLIENT
        =================================================

        Ta table client contient :

        id_client
        nom
        telephone
        adresse

        Il n'y a actuellement pas de colonne email.

        On met donc l'adresse à vide.
        */

        $adresse = '';


        $sqlClient = "
            INSERT INTO client
            (
                nom,
                telephone,
                adresse
            )
            VALUES
            (
                ?,
                ?,
                ?
            )
        ";


        $stmtClient = mysqli_prepare(
            $connexion,
            $sqlClient
        );


        if (!$stmtClient) {

            throw new Exception(
                "Erreur préparation client : "
                . mysqli_error($connexion)
            );

        }


        mysqli_stmt_bind_param(
            $stmtClient,
            "sss",
            $nom,
            $numero,
            $adresse
        );


        /*
        Exécuter l'insertion du client
        */

        if (!mysqli_stmt_execute($stmtClient)) {

            throw new Exception(
                "Erreur enregistrement client : "
                . mysqli_stmt_error($stmtClient)
            );

        }


        /*
        Récupérer l'ID du client
        */

        $idClient = mysqli_insert_id(
            $connexion
        );


        mysqli_stmt_close(
            $stmtClient
        );


        /*
        =================================================
        INSERTION DE LA COMMANDE
        =================================================

        id_commande est AUTO_INCREMENT.

        On ne l'insère donc PAS.

        Colonnes :

        type
        statut
        date
        nom
        quantite
        montant
        id_client
        */

        $sqlCommande = "
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
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ";


        $stmtCommande = mysqli_prepare(
            $connexion,
            $sqlCommande
        );


        if (!$stmtCommande) {

            throw new Exception(
                "Erreur préparation commande : "
                . mysqli_error($connexion)
            );

        }


        /*
        =================================================
        PARAMÈTRES
        =================================================

        s = chaîne
        i = entier
        d = nombre décimal

        type       = s
        statut     = s
        date       = s
        details    = s
        quantite   = i
        montant    = d
        id_client  = i

        Donc :

        s s s s i d i

        = "ssssidi"
        */

        mysqli_stmt_bind_param(
            $stmtCommande,
            "ssssidi",
            $type,
            $statut,
            $date,
            $details,
            $quantiteTotale,
            $total,
            $idClient
        );


        /*
        Exécuter l'insertion de la commande
        */

        if (!mysqli_stmt_execute($stmtCommande)) {

            throw new Exception(
                "Erreur enregistrement commande : "
                . mysqli_stmt_error($stmtCommande)
            );

        }


        /*
        Récupérer l'ID de la commande créée
        */

        $idCommande = mysqli_insert_id(
            $connexion
        );


        mysqli_stmt_close(
            $stmtCommande
        );


        /*
        =================================================
        INSERTION DES PRODUITS
        =================================================
        */

        $sqlProduit = "
            INSERT INTO commande_produit
            (
                id_commande,
                id_produit,
                nom,
                quantite,
                prix
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ";


        $stmtProduit = mysqli_prepare(
            $connexion,
            $sqlProduit
        );


        if (!$stmtProduit) {

            throw new Exception(
                "Erreur préparation produit : "
                . mysqli_error($connexion)
            );

        }


        /*
        =================================================
        ENREGISTRER CHAQUE PRODUIT DU PANIER
        =================================================
        */

        foreach ($panier as $article) {


            $idProduit = (int)
                $article['id_produit'];


            $nomProduit = $article['nom'];


            $quantiteProduit = (int)
                $article['quantite'];


            $prixProduit = (float)
                $article['prix'];


            /*
            Types :

            id_commande = i
            id_produit  = i
            nom         = s
            quantite    = i
            prix        = d

            Donc :

            "i i s i d"

            = "i i s i d" sans espaces
            = "iisid"
            */

            mysqli_stmt_bind_param(
                $stmtProduit,
                "iisid",
                $idCommande,
                $idProduit,
                $nomProduit,
                $quantiteProduit,
                $prixProduit
            );


            if (!mysqli_stmt_execute($stmtProduit)) {

                throw new Exception(
                    "Erreur enregistrement produit : "
                    . mysqli_stmt_error($stmtProduit)
                );

            }
        }


        mysqli_stmt_close(
            $stmtProduit
        );


        /*
        =================================================
        VALIDATION DE LA TRANSACTION
        =================================================
        */

        mysqli_commit(
            $connexion
        );


        /*
        =================================================
        VIDER LE PANIER
        =================================================
        */

        $_SESSION['panier'] = [];


        /*
        =================================================
        MESSAGE DE CONFIRMATION
        =================================================
        */

        echo "
            <script>
                alert(
                    'Commande enregistrée avec succès !'
                );

                window.location.href =
                    'commande_client.php';
            </script>
        ";

        exit;


    } catch (Exception $e) {


        /*
        =================================================
        ANNULER TOUTES LES OPÉRATIONS EN CAS D'ERREUR
        =================================================
        */

        mysqli_rollback(
            $connexion
        );


        die(
            "Erreur lors de l'enregistrement : "
            . $e->getMessage()
        );
    }
}

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Panier - Café Resto
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            padding: 0;

            font-family: Arial, sans-serif;

            background: #f5f5f5;

            color: #333;

        }


        .panier-container {

            width: 90%;

            max-width: 1100px;

            margin: 40px auto;

            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 4px 15px
                rgba(0, 0, 0, 0.08);

        }


        .titre-panier {

            text-align: center;

            color: #008f4c;

            margin-bottom: 30px;

            font-weight: bold;

        }


        .section-title {

            color: #008f4c;

            font-weight: bold;

            margin-top: 20px;

            margin-bottom: 20px;

        }


        label {

            font-weight: 600;

        }


        .form-control,
        .form-select {

            border-radius: 7px;

            padding: 11px;

            border: 1px solid #ddd;

        }


        .form-control:focus,
        .form-select:focus {

            border-color: #008f4c;

            box-shadow:
                0 0 0 0.15rem
                rgba(0, 143, 76, 0.15);

        }


        .table {

            margin-top: 20px;

            vertical-align: middle;

        }


        .table thead th {

            background: #008f4c;

            color: white;

            font-weight: bold;

            padding: 14px;

        }


        .table tbody td {

            padding: 14px;

        }


        .prix {

            font-weight: bold;

            color: #008f4c;

        }


        .total-box {

            margin-top: 25px;

            padding: 20px;

            background: #f0fff7;

            border: 2px solid #008f4c;

            border-radius: 10px;

        }


        .btn-success {

            background: #008f4c;

            border-color: #008f4c;

            padding: 11px 20px;

            border-radius: 7px;

        }


        .btn-success:hover {

            background: #00753e;

            border-color: #00753e;

        }


        .btn-secondary {

            padding: 11px 20px;

            border-radius: 7px;

        }

    </style>

</head>


<body>


<div class="panier-container">


    <h2 class="titre-panier">

        🛒 Votre panier

    </h2>


    <?php if (empty($panier)): ?>


        <div class="alert alert-warning text-center">

            <h4>

                🛒 Votre panier est vide

            </h4>


            <p>

                Aucun produit n'a été sélectionné.

            </p>


            <a
                href="commande_client.php"
                class="btn btn-primary"
            >

                ← Retour aux produits

            </a>

        </div>


    <?php else: ?>


    <form
        method="POST"
        action=""
    >


        <h5 class="section-title">

            👤 Informations du client

        </h5>


        <div class="mb-3">

            <label class="form-label">

                Nom du client

            </label>


            <input
                type="text"
                name="nom_client"
                class="form-control"
                placeholder="Entrez le nom du client"
                required
            >

        </div>


        <div class="mb-3">

            <label class="form-label">

                Numéro du client

            </label>


            <input
                type="text"
                name="numero"
                class="form-control"
                placeholder="Entrez le numéro du client"
            >

        </div>


        <div class="mb-3">

            <label class="form-label">

                Email du client
                <small class="text-muted">
                    (non enregistré dans la base actuelle)
                </small>

            </label>


            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Entrez l'email du client"
            >

        </div>


        <div class="mb-3">

            <label class="form-label">

                Type

            </label>


            <select
                name="type"
                class="form-select"
                required
            >

                <option value="">

                    Choisir le type

                </option>


                <option value="Sur place">

                    Sur place

                </option>


                <option value="À emporter">

                    À emporter

                </option>


                <option value="Livraison">

                    Livraison

                </option>

            </select>

        </div>


        <div class="mb-3">

            <label class="form-label">

                Statut

            </label>


            <select
                name="statut"
                class="form-select"
                required
            >

                <option value="En attente">

                    En attente

                </option>


                <option value="Terminée">

                    Terminée

                </option>


                <option value="Annulée">

                    Annulée

                </option>

            </select>

        </div>


        <div class="mb-4">

            <label class="form-label">

                Date

            </label>


            <input
                type="date"
                name="date"
                class="form-control"
                value="<?php echo date('Y-m-d'); ?>"
                required
            >

        </div>


        <hr>


        <h5 class="section-title">

            🛍️ Produits sélectionnés

        </h5>


        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>
                            Produit
                        </th>


                        <th>
                            Prix unitaire
                        </th>


                        <th>
                            Quantité
                        </th>


                        <th>
                            Sous-total
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php foreach ($panier as $article): ?>


                    <?php

                    $prix =
                        (float)$article['prix'];


                    $quantite =
                        (int)$article['quantite'];


                    $sousTotal =
                        $prix * $quantite;

                    ?>


                    <tr>


                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $article['nom'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );


                                if ($quantite > 1) {

                                    echo ' x' . $quantite;

                                }

                                ?>

                            </strong>

                        </td>


                        <td>

                            <?php

                            echo number_format(
                                $prix,
                                0,
                                ',',
                                ' '
                            );

                            ?>

                            FCFA

                        </td>


                        <td>

                            <?php

                            echo $quantite;

                            ?>

                        </td>


                        <td class="prix">

                            <?php

                            echo number_format(
                                $sousTotal,
                                0,
                                ',',
                                ' '
                            );

                            ?>

                            FCFA

                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>

            </table>

        </div>


        <div class="mb-3">

            <label class="form-label">

                Détails de la commande

            </label>


            <input
                type="text"
                name="details"
                class="form-control"
                value="<?php

                    echo htmlspecialchars(
                        $details,
                        ENT_QUOTES,
                        'UTF-8'
                    );

                ?>"
                readonly
            >

        </div>


        <div class="mb-3">

            <label class="form-label">

                Quantité totale

            </label>


            <input
                type="number"
                name="quantite"
                class="form-control"
                value="<?php
                    echo $quantiteTotale;
                ?>"
                readonly
            >

        </div>


        <div class="total-box mb-4">

            <label class="form-label">

                💰 Montant total

            </label>


            <div class="input-group">

                <input
                    type="number"
                    name="montant"
                    class="form-control"
                    value="<?php
                        echo $total;
                    ?>"
                    readonly
                >


                <span class="input-group-text">

                    FCFA

                </span>

            </div>

        </div>


        <div class="d-flex gap-2">


            <a
                href="commande_client.php"
                class="btn btn-secondary"
            >

                ← Retour aux produits

            </a>


            <button
                type="submit"
                name="enregistrer"
                class="btn btn-success"
            >

                ✅ Enregistrer la commande

            </button>

        </div>


    </form>


    <?php endif; ?>


</div>


</body>

</html>