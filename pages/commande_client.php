<?php

session_start();

include "../php/connexion.php";

/* =====================================================
   INITIALISATION DU PANIER
   ===================================================== */

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}


/* =====================================================
   RÉCUPÉRATION DES PRODUITS
   ===================================================== */

$sql = "SELECT 
            id_produit,
            nom,
            prix,
            quantite_stock,
            seuil_alerte
        FROM produit";

$resultat = mysqli_query($connexion, $sql);

$produits = [];

if ($resultat) {

    while ($produit = mysqli_fetch_assoc($resultat)) {

        $produits[] = $produit;

    }

}


/* =====================================================
   AJOUTER AU PANIER
   ===================================================== */

if (isset($_GET['ajouter'])) {

    $idProduit = intval($_GET['ajouter']);

    foreach ($produits as $produit) {

        if ((int)$produit['id_produit'] === $idProduit) {

            if (isset($_SESSION['panier'][$idProduit])) {

                $_SESSION['panier'][$idProduit]['quantite'] =
                    $_SESSION['panier'][$idProduit]['quantite'] + 1;

            } else {

                $_SESSION['panier'][$idProduit] = [
                    'id_produit' => $produit['id_produit'],
                    'nom' => $produit['nom'],
                    'prix' => $produit['prix'],
                    'quantite' => 1
                ];

            }

            break;
        }

    }

    header("Location: commande_client.php");
    exit;
}


/* =====================================================
   NOMBRE D'ARTICLES DU PANIER
   ===================================================== */

$nombrePanier = 0;

foreach ($_SESSION['panier'] as $article) {

    if (isset($article['quantite'])) {

        $nombrePanier += (int)$article['quantite'];

    }

}


/* =====================================================
   RECHERCHE
   ===================================================== */

$recherche = "";

if (isset($_GET['recherche'])) {

    $recherche = strtolower(
        trim($_GET['recherche'])
    );

}

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Café Resto</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            color: #333;
        }

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

        .container {
            width: 75%;
            margin: 30px auto;
        }

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

        .search button {
            margin-left: 5px;
            padding: 13px 18px;
            border: none;
            border-radius: 6px;
            background: #008f4c;
            color: white;
            cursor: pointer;
        }

        .products-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

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
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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
            margin-bottom: 10px;
        }

        .stock {
            color: #888;
            font-size: 12px;
            margin-bottom: 10px;
        }

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

        .mon-panier {
            position: fixed;
            right: 25px;
            bottom: 20px;
            background: #008f4c;
            color: white;
            padding: 12px 18px;
            border-radius: 25px;
            text-decoration: none;
        }

    </style>

</head>

<body>

<nav class="navbar">

    <div class="logo">
        🍽️ Café Resto
    </div>

    <div>
        👤 Client
    </div>

</nav>


<div class="container">

    <div class="welcome">

        <h1>
            Bienvenue chez Café Resto 🍽️
        </h1>

        <p>
            Choisissez vos plats et boissons préférés
            et passez votre commande facilement !
        </p>

    </div>


    <!-- RECHERCHE -->

    <form
        method="GET"
        action="commande_client.php"
        class="search"
    >

        <input
            type="text"
            name="recherche"
            placeholder="🔍 Rechercher un produit..."
            value="<?php echo htmlspecialchars($recherche); ?>"
        >

        <button type="submit">
            Rechercher
        </button>

    </form>


    <!-- TITRE -->

    <div class="products-title">

        <h2>
            Nos produits
        </h2>

        <span>

            <?php echo count($produits); ?>

            produits disponibles

        </span>

    </div>


    <!-- PRODUITS -->

    <div class="products">

        <?php foreach ($produits as $produit): ?>

            <?php

            if (
                $recherche != ""
                &&
                strpos(
                    strtolower($produit['nom']),
                    $recherche
                ) === false
            ) {

                continue;

            }

            ?>

            <div class="product">

                <div class="product-image">

                    <span class="emoji-image">
                        🍽️
                    </span>

                </div>


                <div class="product-info">

                    <h3>

                        <?php

                        echo htmlspecialchars(
                            $produit['nom']
                        );

                        ?>

                    </h3>


                    <div class="price">

                        <?php

                        echo number_format(
                            $produit['prix'],
                            0,
                            ',',
                            ' '
                        );

                        ?>

                        FCFA

                    </div>


                    <div class="stock">

                        Stock disponible :

                        <?php

                        echo $produit['quantite_stock'];

                        ?>

                    </div>


                    <a
                        href="commande_client.php?ajouter=<?php echo $produit['id_produit']; ?>"
                        class="btn-panier"
                    >

                        🛒 Ajouter au panier

                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>


<!-- PANIER -->

<a
    href="ajout_commande.php"
    class="mon-panier"
>

    🛒 Mon panier
    (<?php echo $nombrePanier; ?>)

</a>

</body>

</html>