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
ÉTAT DU MINI PANIER
=====================================================
*/

$panierOuvert = (
    isset($_GET['panier']) &&
    $_GET['panier'] === 'ouvert'
);


/*
=====================================================
RÉCUPÉRATION DES PRODUITS
=====================================================
*/

$sql = "
    SELECT
        id_produit,
        nom,
        prix,
        quantite_stock,
        seuil_alerte
    FROM produit
    ORDER BY nom ASC
";

$resultat = mysqli_query(
    $connexion,
    $sql
);

$produits = [];

if ($resultat) {

    while ($produit = mysqli_fetch_assoc($resultat)) {

        $produits[] = $produit;

    }

}


/*
=====================================================
AJOUTER AU PANIER
=====================================================
*/

if (isset($_GET['ajouter'])) {

    $idProduit = (int)$_GET['ajouter'];

    foreach ($produits as $produit) {

        if (
            (int)$produit['id_produit'] ===
            $idProduit
        ) {

            $stockDisponible =
                (int)$produit['quantite_stock'];

            $quantiteActuelle = 0;

            if (
                isset(
                    $_SESSION['panier'][$idProduit]
                )
            ) {

                $quantiteActuelle =
                    (int)$_SESSION['panier']
                    [$idProduit]['quantite'];

            }


            /*
            Ne pas dépasser le stock
            */

            if (
                $quantiteActuelle <
                $stockDisponible
            ) {

                if (
                    isset(
                        $_SESSION['panier'][$idProduit]
                    )
                ) {

                    $_SESSION['panier']
                    [$idProduit]['quantite']++;

                } else {

                    $_SESSION['panier'][$idProduit] = [

                        'id_produit' =>
                            $produit['id_produit'],

                        'nom' =>
                            $produit['nom'],

                        'prix' =>
                            (float)$produit['prix'],

                        'quantite' => 1

                    ];

                }

            }

            break;
        }

    }


    /*
    Garder le mini-panier ouvert
    */

    header(
        "Location: commande_client.php?panier=ouvert"
    );

    exit;
}


/*
=====================================================
DIMINUER LA QUANTITÉ
=====================================================
*/

if (isset($_GET['diminuer'])) {

    $idProduit =
        (int)$_GET['diminuer'];

    if (
        isset(
            $_SESSION['panier'][$idProduit]
        )
    ) {

        $_SESSION['panier']
        [$idProduit]['quantite']--;


        if (
            $_SESSION['panier']
            [$idProduit]['quantite'] <= 0
        ) {

            unset(
                $_SESSION['panier']
                [$idProduit]
            );

        }

    }


    header(
        "Location: commande_client.php?panier=ouvert"
    );

    exit;
}


/*
=====================================================
SUPPRIMER DU PANIER
=====================================================
*/

if (isset($_GET['supprimer'])) {

    $idProduit =
        (int)$_GET['supprimer'];

    if (
        isset(
            $_SESSION['panier'][$idProduit]
        )
    ) {

        unset(
            $_SESSION['panier'][$idProduit]
        );

    }


    header(
        "Location: commande_client.php?panier=ouvert"
    );

    exit;
}


/*
=====================================================
CALCUL DU PANIER
=====================================================
*/

$nombrePanier = 0;

$totalPanier = 0;


foreach (
    $_SESSION['panier']
    as $article
) {

    if (
        !isset($article['quantite']) ||
        !isset($article['prix'])
    ) {

        continue;

    }


    $quantite =
        (int)$article['quantite'];

    $prix =
        (float)$article['prix'];


    $nombrePanier += $quantite;

    $totalPanier +=
        $prix * $quantite;

}


/*
=====================================================
RECHERCHE
=====================================================
*/

$recherche = "";

if (
    isset($_GET['recherche'])
) {

    $recherche =
        trim($_GET['recherche']);

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
        Café Resto
    </title>


    <style>

        * {

            margin: 0;

            padding: 0;

            box-sizing: border-box;

        }


        body {

            font-family:
                Arial,
                sans-serif;

            background: #f7f7f7;

            color: #333;

        }


        /*
        =================================================
        NAVBAR
        =================================================
        */

        .navbar {

            height: 65px;

            background: #008f4c;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 8%;

            color: white;

        }


        .logo {

            font-size: 23px;

            font-weight: bold;

        }


        /*
        =================================================
        CONTENU
        =================================================
        */

        .container {

            width: 75%;

            margin: 30px auto;

        }


        /*
        =================================================
        BIENVENUE
        =================================================
        */

        .welcome {

            text-align: center;

            margin-bottom: 25px;

        }


        .welcome h1 {

            color: #26734d;

            font-size: 25px;

            margin-bottom: 8px;

        }


        .welcome p {

            color: #999;

            font-size: 13px;

        }


        /*
        =================================================
        RECHERCHE
        =================================================
        */

        .search {

            display: flex;

            justify-content: center;

            margin-bottom: 50px;

        }


        .search input {

            width: 350px;

            padding: 13px;

            border: 1px solid #ddd;

            border-radius: 6px;

            outline: none;

        }


        .search input:focus {

            border-color: #008f4c;

        }


        .search button {

            margin-left: 5px;

            padding: 13px 18px;

            border: none;

            border-radius: 6px;

            background: #008f4c;

            color: white;

            cursor: pointer;

        }


        .search button:hover {

            background: #00753e;

        }


        /*
        =================================================
        TITRE PRODUITS
        =================================================
        */

        .products-title {

            display: flex;

            justify-content:
                space-between;

            align-items: center;

            margin-bottom: 30px;

        }


        .products-title h2 {

            color: #26734d;

        }


        .products-title span {

            color: #888;

            font-size: 13px;

        }


        /*
        =================================================
        PRODUITS
        =================================================
        */

        .products {

            display: flex;

            gap: 20px;

            flex-wrap: wrap;

        }


        .product {

            width: 190px;

            background: white;

            border-radius: 8px;

            overflow: hidden;

            box-shadow:
                0 2px 8px
                rgba(0,0,0,0.08);

            transition:
                transform 0.2s;

        }


        .product:hover {

            transform:
                translateY(-3px);

        }


        .product-image {

            height: 130px;

            display: flex;

            justify-content: center;

            align-items: center;

            background: #fafafa;

        }


        .emoji-image {

            font-size: 50px;

        }


        .product-info {

            padding: 12px;

        }


        .product-info h3 {

            font-size: 14px;

            margin-bottom: 8px;

        }


        .price {

            font-weight: bold;

            color: #008f4c;

            margin-bottom: 10px;

        }


        .stock {

            color: #888;

            font-size: 12px;

            margin-bottom: 10px;

        }


        .stock-danger {

            color: #dc3545;

            font-weight: bold;

        }


        /*
        =================================================
        BOUTON AJOUTER
        =================================================
        */

        .btn-panier {

            display: block;

            width: 100%;

            padding: 9px;

            border: none;

            border-radius: 5px;

            background: #008f4c;

            color: white;

            text-align: center;

            text-decoration: none;

            font-size: 12px;

        }


        .btn-panier:hover {

            background: #00753e;

        }


        /*
        =================================================
        MINI PANIER
        =================================================
        */

        .panier-wrapper {

            position: fixed;

            right: 15px;

            bottom: 15px;

            z-index: 1000;

        }


        /*
        =================================================
        BOUTON PANIER
        =================================================
        */

        .mon-panier {

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 5px;

            background: #008f4c;

            color: white;

            padding: 9px 14px;

            border-radius: 20px;

            text-decoration: none;

            font-size: 12px;

            font-weight: bold;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.18);

        }


        .mon-panier:hover {

            background: #00753e;

        }


        /*
        =================================================
        PETIT TABLEAU
        =================================================
        */

        .panier-menu {

            position: absolute;

            right: 0;

            bottom: 48px;

            width: 280px;

            max-width:
                calc(100vw - 25px);

            background: white;

            border-radius: 8px;

            box-shadow:
                0 4px 18px
                rgba(0,0,0,0.18);

            padding: 8px;

            display: none;

        }


        /*
        =================================================
        OUVERTURE AU SURVOL
        =================================================
        */

        .panier-wrapper:hover
        .panier-menu {

            display: block;

        }


        /*
        =================================================
        RESTER OUVERT APRÈS + OU -
        =================================================
        */

        .panier-wrapper.panier-ouvert
        .panier-menu {

            display: block;

        }


        /*
        =================================================
        TITRE MINI PANIER
        =================================================
        */

        .panier-menu h3 {

            color: #008f4c;

            font-size: 13px;

            margin: 0 0 7px 0;

            padding-bottom: 6px;

            border-bottom:
                1px solid #eee;

        }


        /*
        =================================================
        TABLEAU MINI PANIER
        =================================================
        */

        .panier-table {

            width: 100%;

            border-collapse:
                collapse;

            font-size: 10px;

        }


        .panier-table th {

            background: #008f4c;

            color: white;

            padding: 5px 4px;

            font-size: 9px;

            text-align: center;

        }


        .panier-table td {

            padding: 5px 4px;

            border-bottom:
                1px solid #eee;

            vertical-align: middle;

        }


        /*
        =================================================
        NOM PRODUIT
        =================================================
        */

        .panier-produit {

            display: block;

            max-width: 90px;

            overflow: hidden;

            white-space: nowrap;

            text-overflow: ellipsis;

            font-weight: bold;

        }


        /*
        =================================================
        PRIX
        =================================================
        */

        .panier-prix {

            color: #008f4c;

            font-weight: bold;

            white-space: nowrap;

            font-size: 10px;

        }


        /*
        =================================================
        BOUTONS QUANTITÉ
        =================================================
        */

        .quantite-controls {

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 3px;

        }


        .quantite-btn {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 18px;

            height: 18px;

            background: #008f4c;

            color: white;

            text-decoration: none;

            border-radius: 3px;

            font-size: 12px;

            font-weight: bold;

        }


        .quantite-btn:hover {

            background: #00753e;

        }


        .quantite-number {

            min-width: 15px;

            text-align: center;

            font-size: 10px;

            font-weight: bold;

        }


        /*
        =================================================
        SUPPRIMER
        =================================================
        */

        .supprimer {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 18px;

            height: 18px;

            background: #dc3545;

            color: white;

            text-decoration: none;

            border-radius: 50%;

            font-size: 9px;

        }


        .supprimer:hover {

            background: #b02a37;

        }


        /*
        =================================================
        TOTAL
        =================================================
        */

        .panier-total {

            margin-top: 7px;

            padding-top: 7px;

            border-top:
                1px solid #008f4c;

            display: flex;

            justify-content:
                space-between;

            font-size: 11px;

            font-weight: bold;

        }


        .panier-total span:last-child {

            color: #008f4c;

        }


        /*
        =================================================
        BOUTON FORMULAIRE
        =================================================
        */

        .btn-commande {

            display: block;

            width: 100%;

            margin-top: 7px;

            padding: 6px;

            background: #008f4c;

            color: white;

            text-align: center;

            text-decoration: none;

            border-radius: 4px;

            font-size: 10px;

            font-weight: bold;

        }


        .btn-commande:hover {

            background: #00753e;

        }


        /*
        =================================================
        PANIER VIDE
        =================================================
        */

        .panier-vide {

            text-align: center;

            color: #888;

            padding: 10px;

            font-size: 10px;

        }


        /*
        =================================================
        STOCK ÉPUISÉ
        =================================================
        */

        .stock-epuise {

            display: block;

            background: #f8d7da;

            color: #842029;

            padding: 8px;

            text-align: center;

            border-radius: 5px;

            font-size: 12px;

        }


        /*
        =================================================
        RESPONSIVE
        =================================================
        */

        @media (max-width: 768px) {

            .container {

                width: 90%;

            }


            .products {

                justify-content: center;

            }


            .search input {

                width: 250px;

            }


            .panier-menu {

                width: 270px;

            }

        }


        @media (max-width: 500px) {

            .navbar {

                padding: 0 4%;

            }


            .logo {

                font-size: 18px;

            }


            .container {

                width: 94%;

            }


            .search {

                margin-bottom: 30px;

            }


            .search input {

                width: 200px;

            }


            .panier-wrapper {

                right: 10px;

                bottom: 10px;

            }


            .panier-menu {

                width: 260px;

                max-width:
                    calc(100vw - 20px);

            }

        }

    </style>

</head>


<body>


<!-- ==================================================
     NAVBAR
================================================== -->

<nav class="navbar">

    <div class="logo">

        🍽️ Café Resto

    </div>


    <div>

        👤 Client

    </div>

</nav>


<!-- ==================================================
     CONTENU PRINCIPAL
================================================== -->

<div class="container">


    <!-- BIENVENUE -->

    <div class="welcome">

        <h1>

            Bienvenue chez Café Resto 🍽️

        </h1>


        <p>

            Choisissez vos plats et boissons préférés
            et passez votre commande facilement !

        </p>

    </div>


    <!-- =================================================
         RECHERCHE
    ================================================= -->

    <form
        method="GET"
        action="commande_client.php"
        class="search"
    >

        <input
            type="text"
            name="recherche"
            placeholder="🔍 Rechercher un produit..."
            value="<?php

                echo htmlspecialchars(
                    $recherche,
                    ENT_QUOTES,
                    'UTF-8'
                );

            ?>"
        >


        <button type="submit">

            Rechercher

        </button>

    </form>


    <!-- =================================================
         TITRE
    ================================================= -->

    <div class="products-title">

        <h2>

            Nos produits

        </h2>


        <span>

            <?php

            echo count($produits);

            ?>

            produits disponibles

        </span>

    </div>


    <!-- =================================================
         LISTE DES PRODUITS
    ================================================= -->

    <div class="products">


        <?php

        $produitTrouve = false;

        ?>


        <?php foreach (
            $produits
            as $produit
        ): ?>


            <?php

            /*
            Filtrage recherche
            */

            if (
                $recherche !== '' &&
                stripos(
                    $produit['nom'],
                    $recherche
                ) === false
            ) {

                continue;

            }


            $produitTrouve = true;


            $idProduit =
                (int)$produit['id_produit'];


            $stock =
                (int)$produit['quantite_stock'];


            $prix =
                (float)$produit['prix'];

            ?>


            <div class="product">


                <!-- IMAGE -->

                <div class="product-image">

                    <span class="emoji-image">

                        🍽️

                    </span>

                </div>


                <!-- INFORMATIONS -->

                <div class="product-info">


                    <h3>

                        <?php

                        echo htmlspecialchars(
                            $produit['nom'],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        ?>

                    </h3>


                    <!-- PRIX -->

                    <div class="price">

                        <?php

                        echo number_format(
                            $prix,
                            0,
                            ',',
                            ' '
                        );

                        ?>

                        FCFA

                    </div>


                    <!-- STOCK -->

                    <div
                        class="<?php

                            echo $stock <= 0
                                ? 'stock stock-danger'
                                : 'stock';

                        ?>"
                    >

                        Stock disponible :

                        <?php

                        echo $stock;

                        ?>

                    </div>


                    <?php if ($stock > 0): ?>


                        <!-- AJOUTER -->

                        <a
                            href="commande_client.php?ajouter=<?php
                                echo $idProduit;
                            ?>&panier=ouvert"
                            class="btn-panier"
                        >

                            🛒 Ajouter au panier

                        </a>


                    <?php else: ?>


                        <span class="stock-epuise">

                            Stock épuisé

                        </span>


                    <?php endif; ?>


                </div>

            </div>


        <?php endforeach; ?>


        <?php if (!$produitTrouve): ?>


            <div
                style="
                    width:100%;
                    text-align:center;
                    padding:40px;
                    color:#888;
                "
            >

                Aucun produit trouvé.

            </div>


        <?php endif; ?>


    </div>


</div>


<!-- ==================================================
     MINI PANIER
================================================== -->

<div
    class="panier-wrapper <?php

        echo $panierOuvert
            ? 'panier-ouvert'
            : '';

    ?>"
>


    <!-- =================================================
         TABLEAU
    ================================================= -->

    <div class="panier-menu">


        <h3>

            🛒 Votre panier

        </h3>


        <?php if (
            empty($_SESSION['panier'])
        ): ?>


            <div class="panier-vide">

                Votre panier est vide.

            </div>


        <?php else: ?>


            <table class="panier-table">


                <thead>

                    <tr>

                        <th>
                            Produit
                        </th>

                        <th>
                            Qté
                        </th>

                        <th>
                            Prix
                        </th>

                        <th>
                            ✕
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach (
                        $_SESSION['panier']
                        as $article
                    ): ?>


                        <?php

                        if (
                            !isset(
                                $article['id_produit'],
                                $article['nom'],
                                $article['prix'],
                                $article['quantite']
                            )
                        ) {

                            continue;

                        }


                        $idProduit =
                            (int)$article['id_produit'];


                        $nomProduit =
                            $article['nom'];


                        $prixProduit =
                            (float)$article['prix'];


                        $quantiteProduit =
                            (int)$article['quantite'];


                        $sousTotal =
                            $prixProduit *
                            $quantiteProduit;

                        ?>


                        <tr>


                            <!-- PRODUIT -->

                            <td>

                                <span
                                    class="panier-produit"
                                    title="<?php

                                        echo htmlspecialchars(
                                            $nomProduit,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );

                                    ?>"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $nomProduit,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </span>

                            </td>


                            <!-- QUANTITÉ -->

                            <td>

                                <div
                                    class="quantite-controls"
                                >


                                    <!-- MOINS -->

                                    <a
                                        href="commande_client.php?diminuer=<?php
                                            echo $idProduit;
                                        ?>&panier=ouvert"
                                        class="quantite-btn"
                                        title="Diminuer"
                                    >

                                        −

                                    </a>


                                    <span
                                        class="quantite-number"
                                    >

                                        <?php

                                        echo $quantiteProduit;

                                        ?>

                                    </span>


                                    <!-- PLUS -->

                                    <a
                                        href="commande_client.php?ajouter=<?php
                                            echo $idProduit;
                                        ?>&panier=ouvert"
                                        class="quantite-btn"
                                        title="Ajouter"
                                    >

                                        +

                                    </a>


                                </div>

                            </td>


                            <!-- PRIX -->

                            <td>

                                <span
                                    class="panier-prix"
                                >

                                    <?php

                                    echo number_format(
                                        $sousTotal,
                                        0,
                                        ',',
                                        ' '
                                    );

                                    ?>

                                    F

                                </span>

                            </td>


                            <!-- SUPPRIMER -->

                            <td>

                                <a
                                    href="commande_client.php?supprimer=<?php
                                        echo $idProduit;
                                    ?>&panier=ouvert"
                                    class="supprimer"
                                    title="Supprimer"
                                >

                                    ✕

                                </a>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>

            </table>


            <!-- =================================================
                 TOTAL
            ================================================= -->

            <div class="panier-total">

                <span>

                    Total :

                </span>


                <span>

                    <?php

                    echo number_format(
                        $totalPanier,
                        0,
                        ',',
                        ' '
                    );

                    ?>

                    FCFA

                </span>

            </div>


            <!-- =================================================
                 FORMULAIRE
            ================================================= -->

            <a
                href="ajout_commande.php"
                class="btn-commande"
            >

                Voir le formulaire

            </a>


        <?php endif; ?>


    </div>


    <!-- =================================================
         BOUTON DU PANIER
    ================================================= -->

    <a
        href="ajout_commande.php"
        class="mon-panier"
        title="Voir le formulaire de commande"
    >

        🛒 Panier

        (

        <?php

        echo $nombrePanier;

        ?>

        )

    </a>


</div>


</body>

</html>