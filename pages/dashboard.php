<?php

include "../php/connexion.php";


/* =====================================================
   1. NOMBRE DE PRODUITS
   ===================================================== */

$sql = "SELECT COUNT(*) AS total FROM produit";
$result = $connexion->query($sql);
$data = $result->fetch_assoc();

$totalProduits = $data['total'];


/* =====================================================
   2. NOMBRE D'EMPLOYÉS
   ===================================================== */

$sql = "SELECT COUNT(*) AS total FROM employe";
$result = $connexion->query($sql);
$data = $result->fetch_assoc();

$totalEmployes = $data['total'];


/* =====================================================
   3. NOMBRE TOTAL DE COMMANDES
   ===================================================== */

$sql = "SELECT COUNT(*) AS total FROM commande";
$result = $connexion->query($sql);
$data = $result->fetch_assoc();

$totalCommandes = $data['total'];


/* =====================================================
   4. COMMANDES DU JOUR
   ===================================================== */

$sql = "SELECT COUNT(*) AS total
        FROM commande
        WHERE DATE(`date`) = CURDATE()";

$result = $connexion->query($sql);
$data = $result->fetch_assoc();

$commandesAujourdhui = $data['total'];


/* =====================================================
   5. PRODUITS EN STOCK
   ===================================================== */

$sql = "SELECT COUNT(*) AS total
        FROM produit
        WHERE quantite_stock > 0";

$result = $connexion->query($sql);
$data = $result->fetch_assoc();

$produitsEnStock = $data['total'];


/* =====================================================
   6. PRODUITS EN STOCK FAIBLE
   ===================================================== */

$sql = "SELECT COUNT(*) AS total
        FROM produit
        WHERE quantite_stock <= seuil_alerte";

$result = $connexion->query($sql);
$data = $result->fetch_assoc();

$produitsStockFaible = $data['total'];


/* =====================================================
   7. COMMANDES EN ATTENTE
   ===================================================== */

$sql = "SELECT COUNT(*) AS total
        FROM commande
        WHERE statut = 'En attente'";

$result = $connexion->query($sql);
$data = $result->fetch_assoc();

$commandesAttente = $data['total'];


/* =====================================================
   8. DERNIÈRES COMMANDES
   ===================================================== */

$sql = "SELECT *
        FROM commande
        ORDER BY id_commande DESC
        LIMIT 5";

$dernieresCommandes = $connexion->query($sql);

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tableau de bord - Café Resto</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- CSS -->

    <link rel="stylesheet" href="../css/style.css">

</head>


<body>


<!-- =====================================================
     MENU LATERAL
     ===================================================== -->

<?php

include "../../partials/sibebar.php"

?>

<!-- =====================================================
     CONTENU PRINCIPAL
     ===================================================== -->

<main class="main-content">


    <!-- BARRE DU HAUT -->

    <div class="topbar">


        <span class="menu-icon">

            ☰

        </span>


        <span class="admin">

            👤 Administrateur ▾

        </span>


    </div>



    <!-- CONTENU -->

    <div class="content">


        <h2>

            Bienvenue sur Café Resto

        </h2>


        <p>

            Gérez facilement votre restaurant.

        </p>



        <!-- =================================================
             CARTES STATISTIQUES
             ================================================= -->

        <div class="row mt-4">


            <!-- VENTES -->

            <div class="col-md-3 mb-3">

                <div class="card text-white bg-success shadow h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h5>Ventes</h5>

                                <h2>--</h2>

                                <p class="mb-0">
                                    Bientôt disponible
                                </p>

                            </div>

                            <i class="bi bi-cash-stack fs-1"></i>

                        </div>

                    </div>

                </div>

            </div>



            <!-- COMMANDES -->

            <div class="col-md-3 mb-3">

                <div class="card text-white bg-primary shadow h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h5>Commandes</h5>

                                <h2>
                                    <?php echo $totalCommandes; ?>
                                </h2>

                                <p class="mb-0">

                                    <?php echo $commandesAujourdhui; ?>

                                    aujourd'hui

                                </p>

                            </div>

                            <i class="bi bi-cart-fill fs-1"></i>

                        </div>

                    </div>

                </div>

            </div>



            <!-- PRODUITS -->

            <div class="col-md-3 mb-3">

                <div class="card text-white bg-warning shadow h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h5>Produits</h5>

                                <h2>
                                    <?php echo $totalProduits; ?>
                                </h2>

                                <p class="mb-0">

                                    <?php echo $produitsEnStock; ?>

                                    disponibles

                                </p>

                            </div>

                            <i class="bi bi-cup-hot-fill fs-1"></i>

                        </div>

                    </div>

                </div>

            </div>



            <!-- EMPLOYÉS -->

            <div class="col-md-3 mb-3">

                <div class="card text-white bg-danger shadow h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h5>Employés</h5>

                                <h2>
                                    <?php echo $totalEmployes; ?>
                                </h2>

                                <p class="mb-0">

                                    Employés enregistrés

                                </p>

                            </div>

                            <i class="bi bi-people-fill fs-1"></i>

                        </div>

                    </div>

                </div>

            </div>


        </div>



        <!-- =================================================
             INFORMATIONS RAPIDES
             ================================================= -->

        <div class="row mt-2">


            <!-- COMMANDES EN ATTENTE -->

            <div class="col-md-6 mb-3">

                <div class="card shadow-sm">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h5>

                                    <i class="bi bi-clock"></i>

                                    Commandes en attente

                                </h5>

                                <h2 class="text-warning">

                                    <?php echo $commandesAttente; ?>

                                </h2>

                            </div>

                            <a
                                href="commandes/commandes.php"
                                class="btn btn-outline-success">

                                Voir les commandes

                            </a>

                        </div>

                    </div>

                </div>

            </div>



            <!-- STOCK FAIBLE -->

            <div class="col-md-6 mb-3">

                <div class="card shadow-sm">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h5>

                                    <i class="bi bi-exclamation-triangle"></i>

                                    Produits en stock faible

                                </h5>

                                <h2 class="text-danger">

                                    <?php echo $produitsStockFaible; ?>

                                </h2>

                            </div>

                            <a
                                href="produits/produits.php"
                                class="btn btn-outline-success">

                                Voir les produits

                            </a>

                        </div>

                    </div>

                </div>

            </div>


        </div>



        <!-- =================================================
             DERNIÈRES COMMANDES
             ================================================= -->

        <div class="card shadow-sm mt-4">


            <div class="card-header bg-success text-white">

                <h5 class="mb-0">

                    Dernières commandes

                </h5>

            </div>


            <div class="card-body">


                <div class="table-responsive">

                    <table class="table table-hover">


                        <thead>

                            <tr>

                                <th>N°</th>

                                <th>Type</th>

                                <th>Date</th>

                                <th>Quantité</th>

                                <th>Statut</th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        if ($dernieresCommandes->num_rows > 0) {

                            while ($commande = $dernieresCommandes->fetch_assoc()) {

                        ?>


                            <tr>


                                <td>

                                    #<?php echo $commande['id_commande']; ?>

                                </td>


                                <td>

                                    <?php echo htmlspecialchars($commande['type']); ?>

                                </td>


                                <td>

                                    <?php echo htmlspecialchars($commande['date']); ?>

                                </td>


                                <td>

                                    <?php echo htmlspecialchars($commande['quantite']); ?>

                                </td>


                                <td>


                                    <?php

                                    if ($commande['statut'] == "En attente") {

                                        echo '<span class="badge bg-warning text-dark">
                                                En attente
                                              </span>';

                                    } elseif ($commande['statut'] == "Terminée") {

                                        echo '<span class="badge bg-success">
                                                Terminée
                                              </span>';

                                    } else {

                                        echo '<span class="badge bg-primary">'
                                            . htmlspecialchars($commande['statut'])
                                            . '</span>';

                                    }

                                    ?>


                                </td>


                            </tr>


                        <?php

                            }

                        } else {

                        ?>


                            <tr>

                                <td colspan="5" class="text-center">

                                    Aucune commande enregistrée.

                                </td>

                            </tr>


                        <?php

                        }

                        ?>


                        </tbody>


                    </table>

                </div>


            </div>

        </div>



        <!-- =================================================
             GRAPHIQUE
             ================================================= -->

        <div class="card shadow-sm mt-4">


            <div class="card-header bg-success text-white">

                <h5 class="mb-0">

                    Évolution des commandes

                </h5>

            </div>


            <div class="card-body">

                <canvas id="commandeChart"></canvas>

            </div>


        </div>


    </div>


</main>



<!-- Chart.js -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

const ctx = document.getElementById('commandeChart');


new Chart(ctx, {

    type: 'line',

    data: {

        labels: [
            'Lundi',
            'Mardi',
            'Mercredi',
            'Jeudi',
            'Vendredi',
            'Samedi'
        ],

        datasets: [{

            label: 'Commandes',

            data: [

                <?php

                /*
                 * Pour l'instant, on affiche 0.
                 * Quand on aura les données historiques,
                 * ce graphique sera relié directement à MySQL.
                 */

                echo "0, 0, 0, 0, 0, 0";

                ?>

            ],

            borderWidth: 2,

            tension: 0.3

        }]

    },

    options: {

        responsive: true,

        scales: {

            y: {

                beginAtZero: true,

                ticks: {

                    stepSize: 1

                }

            }

        }

    }

});

</script>


</body>

</html>