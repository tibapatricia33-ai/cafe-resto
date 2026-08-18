<?php

include "../../php/connexion.php";

/* =========================================================
   RECHERCHE
   ========================================================= */

$recherche = trim($_GET['recherche'] ?? '');

if ($recherche !== '') {

    $sql = "
        SELECT
            id_produit,
            nom,
            prix,
            quantite_stock,
            seuil_alerte
        FROM produit
        WHERE nom LIKE ?
        ORDER BY nom ASC
    ";

    $stmt = $connexion->prepare($sql);

    $mot = "%" . $recherche . "%";

    $stmt->bind_param("s", $mot);

    $stmt->execute();

    $resultat = $stmt->get_result();

} else {

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

    $resultat = $connexion->query($sql);
}


/* =========================================================
   STATISTIQUES
   ========================================================= */

$sqlTotal = "
    SELECT COUNT(*) AS total
    FROM produit
";

$resultTotal = $connexion->query($sqlTotal);
$totalProduits = $resultTotal->fetch_assoc()['total'];


$sqlStockFaible = "
    SELECT COUNT(*) AS total
    FROM produit
    WHERE quantite_stock <= seuil_alerte
";

$resultStockFaible = $connexion->query($sqlStockFaible);
$stockFaible = $resultStockFaible->fetch_assoc()['total'];


$sqlDisponible = "
    SELECT COUNT(*) AS total
    FROM produit
    WHERE quantite_stock > seuil_alerte
";

$resultDisponible = $connexion->query($sqlDisponible);
$produitsDisponibles = $resultDisponible->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="fr">

<head>


<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Produits - Café Resto</title>


<!-- Bootstrap -->

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
    rel="stylesheet"  >


<!-- Bootstrap Icons -->

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<!-- CSS du projet -->

<link
    rel="stylesheet"
    href="../../css/style.css">


<style>

    .produit-card {
        border: none;
        border-radius: 12px;
        transition: 0.2s;
    }

    .produit-card:hover {
        transform: translateY(-2px);
    }

    .statistique {
        border-radius: 12px;
        border: none;
    }

    .prix {
        font-weight: bold;
        color: #198754;
        white-space: nowrap;
    }

    .stock-normal {
        font-weight: bold;
        color: #198754;
    }

    .stock-faible {
        font-weight: bold;
        color: #dc3545;
    }

    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .table thead th {
        background: #198754;
        color: white;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }

    .actions {
        white-space: nowrap;
    }

    .empty-message {
        padding: 30px;
        text-align: center;
        color: #777;
    }

</style>


</head>

<body>

<!-- =====================================================
     MENU LATERAL
     ===================================================== -->

<?php

include "../partials/sidebar.php";

?>

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


    <!-- =================================================
         TITRE
         ================================================= -->

    <div
        class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            Gestion des produits
        </h2>

        <a
            href="/produits/ajout_produit.php"
            class="btn btn-success">

            <i class="bi bi-plus-circle"></i>

            Ajouter un produit

        </a>

    </div>



    <!-- =================================================
         STATISTIQUES
         ================================================= -->

    <div class="row mb-4">


        <!-- TOTAL -->

        <div class="col-md-4 mb-3">

            <div
                class="card statistique text-white bg-primary shadow h-100">

                <div class="card-body">

                    <h5>
                        Total des produits
                    </h5>

                    <h2>
                        <?php echo $totalProduits; ?>
                    </h2>

                </div>

            </div>

        </div>



        <!-- DISPONIBLES -->

        <div class="col-md-4 mb-3">

            <div
                class="card statistique text-white bg-success shadow h-100">

                <div class="card-body">

                    <h5>
                        Produits disponibles
                    </h5>

                    <h2>
                        <?php echo $produitsDisponibles; ?>
                    </h2>

                </div>

            </div>

        </div>



        <!-- STOCK FAIBLE -->

        <div class="col-md-4 mb-3">

            <div
                class="card statistique text-dark bg-warning shadow h-100">

                <div class="card-body">

                    <h5>
                        Stocks faibles
                    </h5>

                    <h2>
                        <?php echo $stockFaible; ?>
                    </h2>

                </div>

            </div>

        </div>


    </div>



    <!-- =================================================
         RECHERCHE
         ================================================= -->

    <form
        method="GET"
        action="produits.php"
        class="search-form mb-4">

        <input
            type="text"
            name="recherche"
            class="form-control"
            placeholder="Rechercher un produit..."
            value="<?php echo htmlspecialchars($recherche); ?>">

        <button
            type="submit"
            class="btn btn-success">

            <i class="bi bi-search"></i>

            Rechercher

        </button>

        <?php if ($recherche !== ''): ?>

            <a
                href="produits.php"
                class="btn btn-secondary">

                <i class="bi bi-x-circle"></i>

                Réinitialiser

            </a>

        <?php endif; ?>

    </form>



    <!-- =================================================
         TABLEAU
         ================================================= -->

    <div class="table-container">

        <div class="table-responsive">

            <table
                class="table table-bordered table-hover mb-0">

                <thead>

                    <tr>

                        <th>
                            Nom du produit
                        </th>

                        <th>
                            Prix
                        </th>

                        <th>
                            Stock
                        </th>

                        <th>
                            Seuil d'alerte
                        </th>

                        <th>
                            État
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (
                    $resultat &&
                    $resultat->num_rows > 0
                ):

                    while (
                        $produit =
                        $resultat->fetch_assoc()
                    ):

                        $stock =
                            (int)$produit['quantite_stock'];

                        $seuil =
                            (int)$produit['seuil_alerte'];

                        $prix =
                            (float)$produit['prix'];

                ?>


                    <tr>


                        <!-- NOM -->

                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $produit['nom']
                                );

                                ?>

                            </strong>

                        </td>



                        <!-- PRIX -->

                        <td>

                            <span class="prix">

                                <?php

                                echo number_format(
                                    $prix,
                                    0,
                                    ',',
                                    ' '
                                );

                                ?>

                                FCFA

                            </span>

                        </td>



                        <!-- STOCK -->

                        <td>

                            <span
                                class="<?php

                                echo
                                    ($stock <= $seuil)
                                    ? 'stock-faible'
                                    : 'stock-normal';

                                ?>">

                                <?php

                                echo $stock;

                                ?>

                            </span>

                        </td>



                        <!-- SEUIL -->

                        <td>

                            <?php

                            echo $seuil;

                            ?>

                        </td>



                        <!-- ETAT -->

                        <td>

                            <?php

                            if ($stock <= 0) {

                                echo '
                                    <span class="badge bg-danger">
                                        ❌ Rupture de stock
                                    </span>
                                ';

                            } elseif ($stock <= $seuil) {

                                echo '
                                    <span class="badge bg-warning text-dark">
                                        ⚠️ Stock faible
                                    </span>
                                ';

                            } else {

                                echo '
                                    <span class="badge bg-success">
                                        ✓ Disponible
                                    </span>
                                ';

                            }

                            ?>

                        </td>



                        <!-- ACTIONS -->

                        <td class="actions">


                            <!-- MODIFIER -->

                            <a
                                href="modifier_produit.php?id=<?php

                                    echo $produit[
                                        'id_produit'
                                    ];

                                ?>"
                                class="btn btn-primary btn-sm">

                                <i
                                    class="bi bi-pencil">
                                </i>

                                Modifier

                            </a>



                            <!-- SUPPRIMER -->

                            <a
                                href="supprimer_produit.php?id=<?php

                                    echo $produit[
                                        'id_produit'
                                    ];

                                ?>"
                                class="btn btn-danger btn-sm"

                                onclick="return confirm(
                                    'Voulez-vous vraiment supprimer ce produit ?'
                                );">

                                <i
                                    class="bi bi-trash">
                                </i>

                                Supprimer

                            </a>


                        </td>


                    </tr>


                <?php

                    endwhile;

                else:

                ?>


                    <tr>

                        <td
                            colspan="6"
                            class="empty-message">

                            <i
                                class="bi bi-box-seam"
                                style="font-size: 40px;">
                            </i>

                            <br><br>

                            Aucun produit trouvé.

                        </td>

                    </tr>


                <?php

                endif;

                ?>


                </tbody>

            </table>

        </div>

    </div>


</div>


</main>

<script src="../js/script.js"></script>

</body>

</html>
