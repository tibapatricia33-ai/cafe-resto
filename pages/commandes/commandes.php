<?php

include "../../php/connexion.php";

/* =========================================================
   RECHERCHE
   ========================================================= */

$recherche = trim($_GET['recherche'] ?? "");

if ($recherche !== "") {

    $sql = "
        SELECT
            commande.id_commande,
            commande.type,
            commande.statut,
            commande.date,
            commande.nom,
            commande.quantite,
            commande.montant,
            commande.id_client,

            client.nom AS nom_client,
            client.telephone,
            client.adresse

        FROM commande

        LEFT JOIN client
            ON commande.id_client = client.id_client

        WHERE
            commande.type LIKE ?
            OR commande.statut LIKE ?
            OR commande.nom LIKE ?
            OR client.nom LIKE ?
            OR client.telephone LIKE ?

        ORDER BY commande.id_commande DESC
    ";

    $stmt = $connexion->prepare($sql);

    $mot = "%" . $recherche . "%";

    $stmt->bind_param(
        "sssss",
        $mot,
        $mot,
        $mot,
        $mot,
        $mot
    );

    $stmt->execute();

    $resultat = $stmt->get_result();

} else {

    $sql = "
        SELECT
            commande.id_commande,
            commande.type,
            commande.statut,
            commande.date,
            commande.nom,
            commande.quantite,
            commande.montant,
            commande.id_client,

            client.nom AS nom_client,
            client.telephone,
            client.adresse

        FROM commande

        LEFT JOIN client
            ON commande.id_client = client.id_client

        ORDER BY commande.id_commande DESC
    ";

    $resultat = $connexion->query($sql);
}


/* =========================================================
   STATISTIQUES
   ========================================================= */

$sqlTotal = "
    SELECT COUNT(*) AS total
    FROM commande
";

$resultTotal = $connexion->query($sqlTotal);
$dataTotal = $resultTotal->fetch_assoc();

$totalCommandes = $dataTotal['total'];


/* COMMANDES EN ATTENTE */

$sqlAttente = "
    SELECT COUNT(*) AS total
    FROM commande
    WHERE statut = 'En attente'
";

$resultAttente = $connexion->query($sqlAttente);
$dataAttente = $resultAttente->fetch_assoc();

$commandesAttente = $dataAttente['total'];


/* COMMANDES LIVRÉES */

$sqlLivrees = "
    SELECT COUNT(*) AS total
    FROM commande
    WHERE statut = 'Livrée'
";

$resultLivrees = $connexion->query($sqlLivrees);
$dataLivrees = $resultLivrees->fetch_assoc();

$commandesLivrees = $dataLivrees['total'];

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Commandes - Café Resto</title>


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
        href="../../css/style.css">


    <style>

        .client-info {
            line-height: 1.6;
        }

        .commande-details {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            min-width: 230px;
        }

        .montant {
            color: #198754;
            font-weight: bold;
            white-space: nowrap;
            font-size: 16px;
        }

        .btn-livree {
            margin-top: 5px;
        }

        .table td {
            vertical-align: middle;
        }

    </style>

</head>


<body>


<!-- =====================================================
     MENU LATERAL
     ===================================================== -->

<?php 

include "../../partials/sidebar.php"; 

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
            👤 Admin ▾
        </span>

    </div>



    <div class="content">


        <!-- TITRE -->

        <div
            class="d-flex justify-content-between align-items-center mb-4">

            <h2>
                Gestion des commandes
            </h2>


            <a
                href="ajout_commande.php"
                class="btn btn-success">

                <i class="bi bi-plus-circle"></i>

                Ajouter une commande

            </a>

        </div>



        <!-- =================================================
             RECHERCHE
             ================================================= -->

        <form
            method="GET"
            action="commandes.php"
            class="search-form mb-4">


            <input
                type="text"
                name="recherche"
                class="form-control"
                placeholder="Rechercher une commande ou un client..."
                value="<?php echo htmlspecialchars($recherche); ?>">


            <button
                type="submit"
                class="btn btn-success">

                <i class="bi bi-search"></i>

                Rechercher

            </button>

        </form>



        <!-- =================================================
             STATISTIQUES
             ================================================= -->

        <div class="row mb-4">


            <!-- TOTAL -->

            <div class="col-md-4 mb-3">

                <div
                    class="card text-white bg-primary shadow h-100">

                    <div class="card-body">

                        <h5>
                            Total des commandes
                        </h5>

                        <h2>
                            <?php echo $totalCommandes; ?>
                        </h2>

                    </div>

                </div>

            </div>



            <!-- EN ATTENTE -->

            <div class="col-md-4 mb-3">

                <div
                    class="card text-dark bg-warning shadow h-100">

                    <div class="card-body">

                        <h5>
                            En attente
                        </h5>

                        <h2>
                            <?php echo $commandesAttente; ?>
                        </h2>

                    </div>

                </div>

            </div>



            <!-- LIVRÉES -->

            <div class="col-md-4 mb-3">

                <div
                    class="card text-white bg-success shadow h-100">

                    <div class="card-body">

                        <h5>
                            Livrées
                        </h5>

                        <h2>
                            <?php echo $commandesLivrees; ?>
                        </h2>

                    </div>

                </div>

            </div>

        </div>



        <!-- =================================================
             TABLEAU
             ================================================= -->

        <div class="table-container">

            <div class="table-responsive">

                <table
                    class="table table-bordered table-hover mb-0">


                    <thead>

                        <tr>

                            <th>Client</th>

                            <th>Commande</th>

                            <th>Quantité</th>

                            <th>Total</th>

                            <th>Type</th>

                            <th>Date</th>

                            <th>Statut</th>

                            <th>Actions</th>

                        </tr>

                    </thead>



                    <tbody>


                    <?php

                    if (
                        $resultat &&
                        $resultat->num_rows > 0
                    ) {

                        while (
                            $commande =
                            $resultat->fetch_assoc()
                        ) {

                    ?>


                        <tr>


                            <!-- =========================
                                 CLIENT
                                 ========================= -->

                            <td>

                                <div class="client-info">

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $commande['nom_client']
                                            ?? 'Client'
                                        );

                                        ?>

                                    </strong>

                                    <br>

                                    <small>

                                        📞

                                        <?php

                                        echo htmlspecialchars(
                                            $commande['telephone']
                                            ?? ''
                                        );

                                        ?>

                                    </small>

                                    <br>

                                    <small>

                                        📍

                                        <?php

                                        echo htmlspecialchars(
                                            $commande['adresse']
                                            ?? ''
                                        );

                                        ?>

                                    </small>

                                </div>

                            </td>



                            <!-- =========================
                                 COMMANDE
                                 ========================= -->

                            <td>

                                <div
                                    class="commande-details">

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $commande['nom']
                                            ?? 'Aucun détail'
                                        );

                                        ?>

                                    </strong>

                                </div>

                            </td>



                            <!-- =========================
                                 QUANTITÉ
                                 ========================= -->

                            <td>

                                <strong>

                                    <?php

                                    echo (int)(
                                        $commande['quantite']
                                        ?? 0
                                    );

                                    ?>

                                </strong>

                            </td>



                            <!-- =========================
                                    MONTANT
                                 ========================= -->

                            <td>

                                <span class="montant">

                                    <?php

                                    echo number_format(
                                        (float)(
                                            $commande['montant']
                                            ?? 0
                                        ),
                                        0,
                                        ',',
                                        ' '
                                    );

                                    ?>

                                    FCFA

                                </span>

                            </td>



                            <!-- =========================
                                 TYPE
                                 ========================= -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $commande['type']
                                    ?? ''
                                );

                                ?>

                            </td>



                            <!-- =========================
                                 DATE
                                 ========================= -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $commande['date']
                                    ?? ''
                                );

                                ?>

                            </td>



                            <!-- =========================
                                 STATUT
                                 ========================= -->

                            <td>

                                <?php

                                $statut =
                                    $commande['statut']
                                    ?? '';

                                if (
                                    $statut ===
                                    "En attente"
                                ) {

                                ?>

                                    <span
                                        class="badge bg-warning text-dark">

                                        🟠 En attente

                                    </span>

                                <?php

                                } elseif (
                                    $statut ===
                                    "Livrée"
                                ) {

                                ?>

                                    <span
                                        class="badge bg-success">

                                        🟢 Livrée

                                    </span>

                                <?php

                                } else {

                                ?>

                                    <span
                                        class="badge bg-secondary">

                                        <?php

                                        echo htmlspecialchars(
                                            $statut
                                        );

                                        ?>

                                    </span>

                                <?php

                                }

                                ?>

                            </td>



                            <!-- =========================
                                 ACTIONS
                                 ========================= -->

                            <td>


                                <!-- MODIFIER -->

                                <a
                                    href="modifier_commande.php?id=<?php echo $commande['id_commande']; ?>"
                                    class="btn btn-primary btn-sm">

                                    <i class="bi bi-pencil"></i>

                                    Modifier

                                </a>



                                <!-- SUPPRIMER -->

                                <a
                                    href="supprimer_commande.php?id=<?php echo $commande['id_commande']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Voulez-vous vraiment supprimer cette commande ?');">

                                    <i class="bi bi-trash"></i>

                                    Supprimer

                                </a>



                                <!-- LIVRER -->

                                <?php

                                if (
                                    $statut !==
                                    "Livrée"
                                ) {

                                ?>

                                    <a
                                        href="livrer_commande.php?id=<?php echo $commande['id_commande']; ?>"
                                        class="btn btn-success btn-sm btn-livree"
                                        onclick="return confirm('Confirmer que cette commande a été livrée ?');">

                                        <i class="bi bi-check-circle"></i>

                                        Livrée

                                    </a>

                                <?php

                                } else {

                                ?>

                                    <span
                                        class="badge bg-success mt-1">

                                        ✓ Déjà livrée

                                    </span>

                                <?php

                                }

                                ?>


                            </td>


                        </tr>


                    <?php

                        }

                    } else {

                    ?>

                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-4">

                                Aucune commande trouvée.

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

</main>


</body>

</html>

