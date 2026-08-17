
<?php

include "../php/connexion.php";


/* =========================================================
   STATISTIQUES COMMANDES
   ========================================================= */

$sql = "SELECT COUNT(*) AS total FROM commande";
$result = $connexion->query($sql);
$totalCommandes = $result->fetch_assoc()['total'];


/* Commandes terminées */

$sql = "SELECT COUNT(*) AS total
        FROM commande
        WHERE statut = 'Terminée'";

$result = $connexion->query($sql);
$commandesTerminees = $result->fetch_assoc()['total'];


/* Commandes en attente */

$sql = "SELECT COUNT(*) AS total
        FROM commande
        WHERE statut = 'En attente'";

$result = $connexion->query($sql);
$commandesAttente = $result->fetch_assoc()['total'];


/* Quantité totale */

$sql = "SELECT COALESCE(SUM(quantite), 0) AS total
        FROM commande";

$result = $connexion->query($sql);
$quantiteTotale = $result->fetch_assoc()['total'];


/* =========================================================
   FACTURES
   ========================================================= */

$sql = "SELECT COUNT(*) AS total FROM facture";

$result = $connexion->query($sql);
$totalFactures = $result->fetch_assoc()['total'];


/* Chiffre d'affaires */

$sql = "SELECT COALESCE(SUM(montant), 0) AS total
        FROM facture";

$result = $connexion->query($sql);
$chiffreAffaires = $result->fetch_assoc()['total'];


/* =========================================================
   DEPENSES
   ========================================================= */

$sql = "SELECT COUNT(*) AS total FROM depense";

$result = $connexion->query($sql);
$totalDepensesNombre = $result->fetch_assoc()['total'];


/* Total dépenses */

$sql = "SELECT COALESCE(SUM(montant), 0) AS total
        FROM depense";

$result = $connexion->query($sql);
$totalDepenses = $result->fetch_assoc()['total'];


/* =========================================================
   BENEFICE
   ========================================================= */

$benefice = $chiffreAffaires - $totalDepenses;


/* =========================================================
   COMMANDES PAR TYPE
   ========================================================= */

$typesLabels = [];
$typesValues = [];

$sql = "SELECT type, COUNT(*) AS total
        FROM commande
        GROUP BY type
        ORDER BY total DESC";

$result = $connexion->query($sql);

while ($row = $result->fetch_assoc()) {

    $typesLabels[] = $row['type'] ?: 'Non défini';
    $typesValues[] = (int) $row['total'];

}


/* =========================================================
   COMMANDES PAR DATE
   ========================================================= */

$dateLabels = [];
$dateValues = [];

$sql = "SELECT date, COUNT(*) AS total
        FROM commande
        GROUP BY date
        ORDER BY date ASC";

$result = $connexion->query($sql);

while ($row = $result->fetch_assoc()) {

    $dateLabels[] = $row['date'];
    $dateValues[] = (int) $row['total'];

}


/* =========================================================
   DERNIERES FACTURES
   ========================================================= */

$sql = "SELECT *
        FROM facture
        ORDER BY id_facture DESC
        LIMIT 10";

$dernieresFactures = $connexion->query($sql);


/* =========================================================
   DERNIERES DEPENSES
   ========================================================= */

$sql = "SELECT *
        FROM depense
        ORDER BY id_depense DESC
        LIMIT 10";

$dernieresDepenses = $connexion->query($sql);

?>


<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Rapports - Café Resto</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="../css/style.css">


    <!-- Chart.js -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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


        <a href="produits.php">

            📦 <span>Produits</span>

        </a>


        <a href="commandes.php">

            📝 <span>Commandes</span>

        </a>


        <a href="employes.php">

            👨‍🍳 <span>Employés</span>

        </a>


        <a href="clients.php">

            👥 <span>Clients</span>

        </a>


        <a href="paiements.php">

            💳 <span>Paiements</span>

        </a>


        <a href="depense.php">

            💰 <span>Dépenses</span>

        </a>


        <a href="factures.php">

            🧾 <span>Factures</span>

        </a>


        <a
            href="rapports.php"
            class="active">

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
     CONTENU
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



    <div class="content">


        <!-- TITRE -->

        <div class="mb-4">

            <h2>

                📊 Rapports et statistiques

            </h2>

            <p class="text-muted">

                Vue générale de l'activité du Café Resto

            </p>

        </div>



        <!-- =================================================
             CARTES STATISTIQUES
             ================================================= -->

        <div class="row mb-4">


            <!-- COMMANDES -->

            <div class="col-md-3 mb-3">

                <div class="card text-white bg-primary shadow h-100">

                    <div class="card-body">

                        <h6>
                            Total commandes
                        </h6>

                        <h2>

                            <?php
                            echo $totalCommandes;
                            ?>

                        </h2>

                        <small>
                            Toutes les commandes
                        </small>

                    </div>

                </div>

            </div>



            <!-- TERMINEES -->

            <div class="col-md-3 mb-3">

                <div class="card text-white bg-success shadow h-100">

                    <div class="card-body">

                        <h6>
                            Commandes terminées
                        </h6>

                        <h2>

                            <?php
                            echo $commandesTerminees;
                            ?>

                        </h2>

                        <small>
                            Commandes finalisées
                        </small>

                    </div>

                </div>

            </div>



            <!-- CHIFFRE AFFAIRES -->

            <div class="col-md-3 mb-3">

                <div class="card text-white bg-info shadow h-100">

                    <div class="card-body">

                        <h6>
                            Chiffre d'affaires
                        </h6>

                        <h3>

                            <?php

                            echo number_format(
                                $chiffreAffaires,
                                0,
                                ",",
                                " "
                            );

                            ?>

                            FCFA

                        </h3>

                    </div>

                </div>

            </div>



            <!-- DEPENSES -->

            <div class="col-md-3 mb-3">

                <div class="card text-white bg-danger shadow h-100">

                    <div class="card-body">

                        <h6>
                            Dépenses
                        </h6>

                        <h3>

                            <?php

                            echo number_format(
                                $totalDepenses,
                                0,
                                ",",
                                " "
                            );

                            ?>

                            FCFA

                        </h3>

                    </div>

                </div>

            </div>


        </div>



        <!-- =================================================
             BENEFICE
             ================================================= -->

        <div class="card shadow mb-4">

            <div class="card-body text-center">

                <h5>
                    💰 Bénéfice estimé
                </h5>

                <h1>

                    <?php

                    echo number_format(
                        $benefice,
                        0,
                        ",",
                        " "
                    );

                    ?>

                    FCFA

                </h1>

                <p class="text-muted mb-0">

                    Chiffre d'affaires − dépenses

                </p>

            </div>

        </div>



        <!-- =================================================
             GRAPHIQUES
             ================================================= -->

        <div class="row">


            <!-- GRAPHIQUE COMMANDES PAR TYPE -->

            <div class="col-md-6 mb-4">

                <div class="card shadow h-100">

                    <div class="card-header">

                        <h5 class="mb-0">

                            📋 Commandes par type

                        </h5>

                    </div>


                    <div class="card-body">

                        <canvas
                            id="graphiqueTypes">
                        </canvas>

                    </div>

                </div>

            </div>



            <!-- GRAPHIQUE FINANCIER -->

            <div class="col-md-6 mb-4">

                <div class="card shadow h-100">

                    <div class="card-header">

                        <h5 class="mb-0">

                            💰 Situation financière

                        </h5>

                    </div>


                    <div class="card-body">

                        <canvas
                            id="graphiqueFinances">
                        </canvas>

                    </div>

                </div>

            </div>


        </div>



        <!-- =================================================
             EVOLUTION DES COMMANDES
             ================================================= -->

        <div class="card shadow mb-4">

            <div class="card-header">

                <h5 class="mb-0">

                    📈 Évolution des commandes

                </h5>

            </div>


            <div class="card-body">

                <canvas
                    id="graphiqueEvolution">
                </canvas>

            </div>

        </div>



        <!-- =================================================
             DERNIERES FACTURES
             ================================================= -->

        <div class="card shadow mb-4">


            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">

                    🧾 Dernières factures

                </h5>

            </div>


            <div class="card-body">


                <div class="table-responsive">


                    <table class="table table-bordered table-hover">


                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Détails</th>

                                <th>Montant</th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        if (
                            $dernieresFactures &&
                            $dernieresFactures->num_rows > 0
                        ) {

                            while (
                                $facture =
                                $dernieresFactures->fetch_assoc()
                            ) {

                        ?>


                            <tr>

                                <td>

                                    #<?php
                                    echo $facture['id_facture'];
                                    ?>

                                </td>


                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $facture['nom']
                                    );

                                    ?>

                                </td>


                                <td>

                                    <?php

                                    echo number_format(
                                        $facture['montant'],
                                        0,
                                        ",",
                                        " "
                                    );

                                    ?>

                                    FCFA

                                </td>

                            </tr>


                        <?php

                            }

                        } else {

                        ?>


                            <tr>

                                <td
                                    colspan="3"
                                    class="text-center">

                                    Aucune facture.

                                </td>

                            </tr>


                        <?php } ?>


                        </tbody>

                    </table>

                </div>

            </div>

        </div>



        <!-- =================================================
             DERNIERES DEPENSES
             ================================================= -->

        <div class="card shadow mb-4">


            <div class="card-header bg-danger text-white">

                <h5 class="mb-0">

                    💸 Dernières dépenses

                </h5>

            </div>


            <div class="card-body">


                <div class="table-responsive">


                    <table class="table table-bordered table-hover">


                        <thead>

                            <tr>

                                <th>Description</th>

                                <th>Quantité</th>

                                <th>Unité</th>

                                <th>Montant</th>

                                <th>Date</th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        if (
                            $dernieresDepenses &&
                            $dernieresDepenses->num_rows > 0
                        ) {

                            while (
                                $depense =
                                $dernieresDepenses->fetch_assoc()
                            ) {

                        ?>


                            <tr>

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $depense['description']
                                    );

                                    ?>

                                </td>


                                <td>

                                    <?php
                                    echo $depense['quantite'];
                                    ?>

                                </td>


                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $depense['unite']
                                    );

                                    ?>

                                </td>


                                <td>

                                    <?php

                                    echo number_format(
                                        $depense['montant'],
                                        0,
                                        ",",
                                        " "
                                    );

                                    ?>

                                    FCFA

                                </td>


                                <td>

                                    <?php
                                    echo $depense['date'];
                                    ?>

                                </td>

                            </tr>


                        <?php

                            }

                        } else {

                        ?>


                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center">

                                    Aucune dépense.

                                </td>

                            </tr>


                        <?php } ?>


                        </tbody>

                    </table>

                </div>

            </div>

        </div>


    </div>

</main>



<!-- =====================================================
     GRAPHIQUES CHART.JS
     ===================================================== -->

<script>


/* =====================================================
   DONNEES PHP → JAVASCRIPT
   ===================================================== */

const typesLabels =
    <?php echo json_encode($typesLabels); ?>;

const typesValues =
    <?php echo json_encode($typesValues); ?>;


const dateLabels =
    <?php echo json_encode($dateLabels); ?>;

const dateValues =
    <?php echo json_encode($dateValues); ?>;


const chiffreAffaires =
    <?php echo (float) $chiffreAffaires; ?>;

const totalDepenses =
    <?php echo (float) $totalDepenses; ?>;

const benefice =
    <?php echo (float) $benefice; ?>;



/* =====================================================
   GRAPHIQUE COMMANDES PAR TYPE
   ===================================================== */

new Chart(
    document.getElementById("graphiqueTypes"),
    {

        type: "doughnut",

        data: {

            labels: typesLabels,

            datasets: [

                {

                    data: typesValues,

                    borderWidth: 1

                }

            ]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {

                    position: "bottom"

                }

            }

        }

    }
);



/* =====================================================
   GRAPHIQUE FINANCIER
   ===================================================== */

new Chart(
    document.getElementById("graphiqueFinances"),
    {

        type: "bar",

        data: {

            labels: [

                "Chiffre d'affaires",
                "Dépenses",
                "Bénéfice"

            ],

            datasets: [

                {

                    label: "Montant en FCFA",

                    data: [

                        chiffreAffaires,
                        totalDepenses,
                        benefice

                    ],

                    borderWidth: 1

                }

            ]

        },

        options: {

            responsive: true,

            scales: {

                y: {

                    beginAtZero: true

                }

            }

        }

    }
);



/* =====================================================
   EVOLUTION DES COMMANDES
   ===================================================== */

new Chart(
    document.getElementById("graphiqueEvolution"),
    {

        type: "line",

        data: {

            labels: dateLabels,

            datasets: [

                {

                    label: "Nombre de commandes",

                    data: dateValues,

                    tension: 0.3,

                    fill: false,

                    borderWidth: 2,

                    pointRadius: 4

                }

            ]

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

    }
);


</script>


</body>

</html>

