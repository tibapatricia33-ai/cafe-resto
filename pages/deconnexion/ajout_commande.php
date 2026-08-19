<?php

session_start();

include "../../php/connexion.php";


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
    Si le produit existe déjà,
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
    Vérifier que le panier n'est pas vide
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
    */

    mysqli_begin_transaction($connexion);


    try {


        /*
        =================================================
        INSERTION DU CLIENT
        =================================================

        La table client contient :

        id_client
        nom
        telephone
        adresse

        L'email n'existe pas dans ta table actuelle.
        Il est donc récupéré du formulaire mais
        n'est pas enregistré.
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
        Récupérer l'identifiant du client
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

        On ne l'insère donc pas.

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

        type       = s
        statut     = s
        date       = s
        details    = s
        quantite   = i
        montant    = d
        id_client  = i

        Donc :

        ssssidi
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
        Exécuter l'insertion
        */

        if (!mysqli_stmt_execute($stmtCommande)) {

            throw new Exception(
                "Erreur enregistrement commande : "
                . mysqli_stmt_error($stmtCommande)
            );

        }


        /*
        Récupérer l'identifiant de la commande
        */

        $idCommande = mysqli_insert_id(
            $connexion
        );


        mysqli_stmt_close(
            $stmtCommande
        );


        /*
        =================================================
        INSERTION DES PRODUITS COMMANDÉS
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
        ENREGISTRER CHAQUE PRODUIT
        =================================================
        */

        foreach ($panier as $article) {

            $idProduit = (int)
                $article['id_produit'];

            $nomProduit =
                $article['nom'];

            $quantiteProduit = (int)
                $article['quantite'];

            $prixProduit = (float)
                $article['prix'];


            /*
            Types :

            i = entier
            s = texte
            d = décimal

            i i s i d

            = iisid
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
        REDIRECTION
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
        ANNULER EN CAS D'ERREUR
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
        Enregistrer une commande - Café Resto
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


        /*
        ================================================
        CONTENEUR PRINCIPAL
        ================================================
        */

        .panier-container {

            width: 94%;

            max-width: 1200px;

            margin: 30px auto;

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow:
                0 4px 15px
                rgba(0, 0, 0, 0.08);

        }


        /*
        ================================================
        TITRE
        ================================================
        */

        .titre-panier {

            text-align: center;

            color: #008f4c;

            margin-bottom: 25px;

            font-weight: bold;

        }


        /*
        ================================================
        DEUX COLONNES
        ================================================
        */

        .formulaire-deux-colonnes {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 25px;

            align-items: stretch;

        }


        /*
        ================================================
        BLOCS CLIENT / PRODUITS
        ================================================
        */

        .bloc-formulaire {

            background: #ffffff;

            border: 1px solid #e1e1e1;

            border-radius: 12px;

            padding: 22px;

            min-height: 520px;

            height: 100%;

            box-shadow:
                0 3px 10px
                rgba(0, 0, 0, 0.05);

        }


        /*
        ================================================
        TITRES DES BLOCS
        ================================================
        */

        .section-title {

            color: #008f4c;

            font-weight: bold;

            margin-top: 0;

            margin-bottom: 22px;

            padding-bottom: 10px;

            border-bottom: 2px solid #008f4c;

        }


        /*
        ================================================
        LABELS
        ================================================
        */

        label {

            font-weight: 600;

        }


        /*
        ================================================
        CHAMPS
        ================================================
        */

        .form-control,
        .form-select {

            border-radius: 7px;

            padding: 10px;

            border: 1px solid #ddd;

        }


        .form-control:focus,
        .form-select:focus {

            border-color: #008f4c;

            box-shadow:
                0 0 0 0.15rem
                rgba(0, 143, 76, 0.15);

        }


        /*
        ================================================
        TABLE
        ================================================
        */

        .table {

            margin-top: 10px;

            vertical-align: middle;

            font-size: 13px;

        }


        .table thead th {

            background: #008f4c;

            color: white;

            font-weight: bold;

            padding: 10px;

            white-space: nowrap;

        }


        .table tbody td {

            padding: 10px;

        }


        /*
        ================================================
        PRIX
        ================================================
        */

        .prix {

            font-weight: bold;

            color: #008f4c;

            white-space: nowrap;

        }


        /*
        ================================================
        TOTAL
        ================================================
        */

        .total-box {

            margin-top: 18px;

            padding: 15px;

            background: #f0fff7;

            border: 2px solid #008f4c;

            border-radius: 10px;

        }


        /*
        ================================================
        BOUTONS
        ================================================
        */

        .boutons-formulaire {

            display: flex;

            justify-content: flex-end;

            align-items: center;

            gap: 10px;

            margin-top: 22px;

        }


        /*
        ================================================
        BOUTON ENREGISTRER PETIT
        ================================================
        */

        .btn-success {

            background: #008f4c;

            border-color: #008f4c;

            padding: 7px 14px;

            border-radius: 6px;

            font-size: 13px;

            width: auto;

        }


        .btn-success:hover {

            background: #00753e;

            border-color: #00753e;

        }


        /*
        ================================================
        BOUTON RETOUR
        ================================================
        */

        .btn-secondary {

            padding: 7px 14px;

            border-radius: 6px;

            font-size: 13px;

        }


        /*
        ================================================
        MESSAGE PANIER VIDE
        ================================================
        */

        .panier-vide {

            text-align: center;

            padding: 40px;

        }


        /*
        ================================================
        MOBILE
        ================================================
        */

        @media (max-width: 800px) {

            .panier-container {

                width: 96%;

                padding: 15px;

            }


            .formulaire-deux-colonnes {

                grid-template-columns: 1fr;

            }


            .bloc-formulaire {

                min-height: auto;

            }


            .boutons-formulaire {

                flex-direction: row;

                justify-content: flex-end;

            }


            .boutons-formulaire a,
            .boutons-formulaire button {

                width: auto;

            }

        }


        /*
        ================================================
        PETITS ÉCRANS
        ================================================
        */

        @media (max-width: 500px) {

            .boutons-formulaire {

                flex-direction: column;

                align-items: stretch;

            }


            .boutons-formulaire a,
            .boutons-formulaire button {

                width: 100%;

            }

        }

    </style>

</head>


<body>


<div class="panier-container">


    <h2 class="titre-panier">

        🛒 Enregistrement de la commande

    </h2>


    <?php if (empty($panier)): ?>


        <!-- =========================================
             PANIER VIDE
             ========================================= -->

        <div class="alert alert-warning panier-vide">

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


        <!-- =========================================
             FORMULAIRE
             ========================================= -->

        <form
            method="POST"
            action=""
        >


            <div class="formulaire-deux-colonnes">


                <!-- =================================
                     COLONNE GAUCHE
                     INFORMATIONS CLIENT
                     ================================= -->

                <div class="bloc-formulaire">


                    <h5 class="section-title">

                        👤 Informations du client

                    </h5>


                    <!-- NOM -->

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


                    <!-- NUMÉRO -->

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


                    <!-- EMAIL -->

                    <div class="mb-3">

                        <label class="form-label">

                            Email du client

                        </label>


                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Entrez l'email du client"
                        >

                    </div>


                    <!-- TYPE -->

                    <div class="mb-3">

                        <label class="form-label">

                            Type de commande

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


                    <!-- STATUT -->

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


                    <!-- DATE -->

                    <div class="mb-3">

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


                </div>


                <!-- =================================
                     COLONNE DROITE
                     PRODUITS
                     ================================= -->

                <div class="bloc-formulaire">


                    <h5 class="section-title">

                        🛍️ Produits commandés

                    </h5>


                    <!-- TABLE PRODUITS -->

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead>

                                <tr>

                                    <th>
                                        Produit
                                    </th>


                                    <th>
                                        Prix
                                    </th>


                                    <th>
                                        Qté
                                    </th>


                                    <th>
                                        Total
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                            <?php foreach (
                                $panier
                                as $article
                            ): ?>


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


                    <!-- DÉTAILS -->

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


                    <!-- QUANTITÉ -->

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


                    <!-- MONTANT -->

                    <div class="total-box">

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


                </div>


            </div>


            <!-- =========================================
                 BOUTONS
                 ========================================= -->

            <div class="boutons-formulaire">


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

                    ✅ Enregistrer

                </button>


            </div>


        </form>


    <?php endif; ?>


</div>


</body>

</html>