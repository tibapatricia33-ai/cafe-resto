<?php

include "../../php/connexion.php";


/* =========================================
   RECHERCHE DES PAIEMENTS
   ========================================= */

if (isset($_GET['recherche']) && !empty(trim($_GET['recherche']))) {

    $recherche = trim($_GET['recherche']);

    $sql = "SELECT transaction_paiement.*
            FROM transaction_paiement
            INNER JOIN commande
            ON transaction_paiement.id_commande = commande.id_commande
            WHERE transaction_paiement.id_transaction LIKE ?
            OR transaction_paiement.id_commande LIKE ?
            OR transaction_paiement.montant LIKE ?
            OR transaction_paiement.type LIKE ?
            OR transaction_paiement.statut LIKE ?
            OR transaction_paiement.reference_mobile_money LIKE ?
            OR commande.nom LIKE ?
            ORDER BY transaction_paiement.id_transaction DESC";

    $stmt = $connexion->prepare($sql);

    $mot = "%" . $recherche . "%";

    $stmt->bind_param(
        "sssssss",
        $mot,
        $mot,
        $mot,
        $mot,
        $mot,
        $mot,
        $mot
    );

    $stmt->execute();

    $resultat = $stmt->get_result();

} else {

    $sql = "SELECT *
            FROM transaction_paiement
            ORDER BY id_transaction DESC";

    $resultat = $connexion->query($sql);
}



/* =========================================
   STATISTIQUES DES PAIEMENTS
   ========================================= */

/* Total encaissé */

$sqlTotal = "SELECT COALESCE(SUM(montant), 0) AS total
             FROM transaction_paiement
             WHERE statut = 'Payé'";

$resultTotal = $connexion->query($sqlTotal);

$dataTotal = $resultTotal->fetch_assoc();

$totalEncaisse = $dataTotal['total'];


/* Nombre de paiements */

$sqlNombre = "SELECT COUNT(*) AS total
              FROM transaction_paiement";

$resultNombre = $connexion->query($sqlNombre);

$dataNombre = $resultNombre->fetch_assoc();

$nombrePaiements = $dataNombre['total'];


/* Total Espèces */

$sqlEspeces = "SELECT COALESCE(SUM(montant), 0) AS total
               FROM transaction_paiement
               WHERE type = 'Espèces'
               AND statut = 'Payé'";

$resultEspeces = $connexion->query($sqlEspeces);

$dataEspeces = $resultEspeces->fetch_assoc();

$totalEspeces = $dataEspeces['total'];


/* Total Mobile Money */

$sqlMobile = "SELECT COALESCE(SUM(montant), 0) AS total
              FROM transaction_paiement
              WHERE type = 'Mobile Money'
              AND statut = 'Payé'";

$resultMobile = $connexion->query($sqlMobile);

$dataMobile = $resultMobile->fetch_assoc();

$totalMobile = $dataMobile['total'];
?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Paiements - Café Resto</title>

    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS -->

    <link rel="stylesheet"
          href="../../css/style.css">

</head>


<body>


<!-- =========================================
     MENU LATERAL
     ========================================= -->

<?php
include "../../partials/sibebar.php";

?>


<!-- =========================================
     CONTENU PRINCIPAL
     ========================================= -->

<main class="main-content">


    <!-- BARRE DU HAUT -->

    <div class="topbar">

        <span class="menu-icon">

            ☰

        </span>


        <span class="admin">

            👤 Admin ▾

        </span>

    </div>



    <!-- CONTENU -->

    <div class="content">

<!-- =========================================
     TITRE + RECHERCHE COMMANDE
     ========================================= -->

<div class="d-flex justify-content-between align-items-center mb-3">

    <h2 class="mb-0">
        Gestion des paiements
    </h2>

</div>


<!-- =========================================
     RECHERCHE
     ========================================= -->

<form
    method="GET"  action="/../paiements/paiements.php"
    class="mb-4">

    <div class="row g-2">

        <div class="col-md-8">

            <input
                type="text"
                name="recherche"
                class="form-control"
                placeholder="Rechercher une commande ou un paiement..."
                value="<?php
                    echo isset($_GET['recherche'])
                        ? htmlspecialchars($_GET['recherche'])
                        : '';
                ?>">

        </div>


        <div class="col-md-2">

            <button
                type="submit"
                class="btn btn-success w-100">

                <i class="bi bi-search"></i>

                Rechercher

            </button>

        </div>

    </div>

</form>


    

<!-- =========================================
     STATISTIQUES
     ========================================= -->

<div class="row mb-4">


    <!-- TOTAL ENCAISSÉ -->

    <div class="col-md-3 mb-3">

        <div class="card text-white bg-success shadow h-100">

            <div class="card-body">

                <h5>

                    💰 Total encaissé

                </h5>

                <h3>

                    <?php
                    echo number_format(
                        $totalEncaisse,
                        0,
                        ',',
                        ' '
                    );
                    ?>

                    FCFA

                </h3>

            </div>

        </div>

    </div>



    <!-- NOMBRE DE PAIEMENTS -->

    <div class="col-md-3 mb-3">

        <div class="card text-white bg-primary shadow h-100">

            <div class="card-body">

                <h5>

                    💳 Paiements

                </h5>

                <h3>

                    <?php
                    echo $nombrePaiements;
                    ?>

                </h3>

            </div>

        </div>

    </div>



    <!-- ESPÈCES -->

    <div class="col-md-3 mb-3">

        <div class="card text-white bg-warning shadow h-100">

            <div class="card-body">

                <h5>

                    💵 Espèces

                </h5>

                <h3>

                    <?php
                    echo number_format(
                        $totalEspeces,
                        0,
                        ',',
                        ' '
                    );
                    ?>

                    FCFA

                </h3>

            </div>

        </div>

    </div>



    <!-- MOBILE MONEY -->

    <div class="col-md-3 mb-3">

        <div class="card text-white bg-info shadow h-100">

            <div class="card-body">

                <h5>

                    📱 Mobile Money

                </h5>

                <h3>

                    <?php
                    echo number_format(
                        $totalMobile,
                        0,
                        ',',
                        ' '
                    );
                    ?>

                    FCFA

                </h3>

            </div>

        </div>

    </div>


</div>



            <a
                href="ajout_paiement.php"
                class="btn btn-success">

                <i class="bi bi-plus-circle"></i>

                Ajouter un paiement

            </a>


        </div>



        <!-- =====================================
             TABLEAU
             ===================================== -->

        <div class="table-container">


            <table class="table table-bordered
                          table-hover mb-0">


                <thead>

                    <tr>

                        <th>N°</th>

                        <th>Commande</th>

                        <th>Montant</th>

                        <th>Type</th>

                        <th>Statut</th>

                        <th>Référence Mobile Money</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if ($resultat->num_rows > 0) {

                    while ($paiement =
                           $resultat->fetch_assoc()) {

                ?>


                    <tr>


                        <td>

                            #<?php
                            echo $paiement['id_transaction'];
                            ?>

                        </td>


                        <td>

                            #<?php
                            echo $paiement['id_commande'];
                            ?>

                        </td>


                        <td>

                            <?php

                            echo number_format(
                                $paiement['montant'],
                                0,
                                ',',
                                ' '
                            );

                            ?>

                            FCFA

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $paiement['type']
                            );
                            ?>

                        </td>


                        <td>

                            <?php

                            if ($paiement['statut']
                                == "Payé") {

                                echo '<span class="badge bg-success">
                                      Payé
                                      </span>';

                            } elseif (
                                $paiement['statut']
                                == "En attente"
                            ) {

                                echo '<span class="badge bg-warning text-dark">
                                      En attente
                                      </span>';

                            } else {

                                echo '<span class="badge bg-secondary">'
                                    . htmlspecialchars(
                                        $paiement['statut']
                                    )
                                    . '</span>';

                            }

                            ?>

                        </td>


                        <td>

                            <?php

                            if (
                                !empty(
                                    $paiement[
                                        'reference_mobile_money'
                                    ]
                                )
                            ) {

                                echo htmlspecialchars(
                                    $paiement[
                                        'reference_mobile_money'
                                    ]
                                );

                            } else {

                                echo "-";

                            }

                            ?>

                        </td>


                        <td>

                            <a
                                href="modifier_paiement.php?id=<?php echo $paiement['id_transaction']; ?>"
                                class="btn btn-primary btn-sm">

                                <i class="bi bi-pencil"></i>

                                Modifier

                            </a>


                            <a
                                href="supprimer_paiement.php?id=<?php echo $paiement['id_transaction']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Voulez-vous vraiment supprimer ce paiement ?');">

                                <i class="bi bi-trash"></i>

                                Supprimer

                            </a>

                        </td>


                    </tr>


                <?php

                    }

                } else {

                ?>


                    <tr>

                        <td
                            colspan="7"
                            class="text-center">

                            Aucun paiement enregistré.

                        </td>

                    </tr>


                <?php

                }

                ?>


                </tbody>

            </table>

        </div>


    </div>


</main>


</body>

</html>