
<?php

include "../php/connexion.php";


/* =====================================================
   VARIABLES
   ===================================================== */

$message = "";
$typeMessage = "";

$factureModification = null;


/* =====================================================
   AJOUTER / MODIFIER / SUPPRIMER
   ===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


    /* =================================================
       AJOUTER UNE FACTURE
       ================================================= */

    if ($action === "ajouter") {

        $nom = trim($_POST["nom"] ?? "");
        $montant = (float)($_POST["montant"] ?? 0);


        if (empty($nom) || $montant <= 0) {

            header("Location: factures.php?erreur=champs");
            exit;

        }


        /* ---------------------------------------------
           RÉCUPÉRER UN UTILISATEUR
           --------------------------------------------- */

        $sqlUser = "SELECT id_utilisateur
                    FROM utilisateur
                    ORDER BY id_utilisateur ASC
                    LIMIT 1";

        $resultUser = $connexion->query($sqlUser);


        if (!$resultUser || $resultUser->num_rows === 0) {

            header("Location: factures.php?erreur=utilisateur");
            exit;

        }


        $user = $resultUser->fetch_assoc();

        $id_utilisateur = $user["id_utilisateur"];


        /* ---------------------------------------------
           INSERTION
           --------------------------------------------- */

        $sql = "INSERT INTO facture
                (nom, montant, id_utilisateur)
                VALUES (?, ?, ?)";


        $stmt = $connexion->prepare($sql);


        if (!$stmt) {

            die("Erreur SQL : " . $connexion->error);

        }


        $stmt->bind_param(
            "sdi",
            $nom,
            $montant,
            $id_utilisateur
        );


        if ($stmt->execute()) {

            header("Location: factures.php?ajout=ok");
            exit;

        } else {

            die("Erreur lors de l'ajout : " . $stmt->error);

        }

    }



    /* =================================================
       MODIFIER UNE FACTURE
       ================================================= */

    if ($action === "modifier") {

        $id_facture = (int)($_POST["id_facture"] ?? 0);

        $nom = trim($_POST["nom"] ?? "");

        $montant = (float)($_POST["montant"] ?? 0);


        if (
            $id_facture <= 0 ||
            empty($nom) ||
            $montant <= 0
        ) {

            header("Location: factures.php?erreur=champs");
            exit;

        }


        $sql = "UPDATE facture
                SET nom = ?,
                    montant = ?
                WHERE id_facture = ?";


        $stmt = $connexion->prepare($sql);


        if (!$stmt) {

            die("Erreur SQL : " . $connexion->error);

        }


        $stmt->bind_param(
            "sdi",
            $nom,
            $montant,
            $id_facture
        );


        if ($stmt->execute()) {

            header("Location: factures.php?modification=ok");
            exit;

        } else {

            die(
                "Erreur lors de la modification : "
                . $stmt->error
            );

        }

    }



    /* =================================================
       SUPPRIMER UNE FACTURE
       ================================================= */

    if ($action === "supprimer") {

        $id_facture =
            (int)($_POST["id_facture"] ?? 0);


        if ($id_facture <= 0) {

            header("Location: factures.php?erreur=id");
            exit;

        }


        $sql = "DELETE FROM facture
                WHERE id_facture = ?";


        $stmt = $connexion->prepare($sql);


        if (!$stmt) {

            die("Erreur SQL : " . $connexion->error);

        }


        $stmt->bind_param(
            "i",
            $id_facture
        );


        if ($stmt->execute()) {

            header("Location: factures.php?suppression=ok");
            exit;

        } else {

            die(
                "Erreur lors de la suppression : "
                . $stmt->error
            );

        }

    }

}



/* =====================================================
   MESSAGES
   ===================================================== */

if (
    isset($_GET["ajout"]) &&
    $_GET["ajout"] === "ok"
) {

    $message =
        "Facture enregistrée avec succès.";

    $typeMessage = "success";

}


if (
    isset($_GET["modification"]) &&
    $_GET["modification"] === "ok"
) {

    $message =
        "Facture modifiée avec succès.";

    $typeMessage = "success";

}


if (
    isset($_GET["suppression"]) &&
    $_GET["suppression"] === "ok"
) {

    $message =
        "Facture supprimée avec succès.";

    $typeMessage = "success";

}


if (
    isset($_GET["erreur"]) &&
    $_GET["erreur"] === "champs"
) {

    $message =
        "Veuillez remplir correctement tous les champs.";

    $typeMessage = "danger";

}


if (
    isset($_GET["erreur"]) &&
    $_GET["erreur"] === "utilisateur"
) {

    $message =
        "Aucun utilisateur n'existe dans la base de données.";

    $typeMessage = "danger";

}



/* =====================================================
   RÉCUPÉRER LA FACTURE À MODIFIER
   ===================================================== */

if (isset($_GET["modifier"])) {

    $idModifier =
        (int)$_GET["modifier"];


    $sql = "SELECT *
            FROM facture
            WHERE id_facture = ?";


    $stmt = $connexion->prepare($sql);

    $stmt->bind_param(
        "i",
        $idModifier
    );

    $stmt->execute();

    $result =
        $stmt->get_result();


    if ($result->num_rows > 0) {

        $factureModification =
            $result->fetch_assoc();

    }

}



/* =====================================================
   RECHERCHE
   ===================================================== */

$recherche =
    trim($_GET["recherche"] ?? "");


if ($recherche !== "") {

    $sql = "SELECT
                f.*,
                u.nom AS nom_utilisateur
            FROM facture f
            LEFT JOIN utilisateur u
                ON f.id_utilisateur =
                   u.id_utilisateur
            WHERE f.nom LIKE ?
            ORDER BY f.id_facture DESC";


    $stmt =
        $connexion->prepare($sql);


    $mot =
        "%" . $recherche . "%";


    $stmt->bind_param(
        "s",
        $mot
    );


    $stmt->execute();


    $factures =
        $stmt->get_result();

} else {


    $sql = "SELECT
                f.*,
                u.nom AS nom_utilisateur
            FROM facture f
            LEFT JOIN utilisateur u
                ON f.id_utilisateur =
                   u.id_utilisateur
            ORDER BY f.id_facture DESC";


    $factures =
        $connexion->query($sql);

}



/* =====================================================
   STATISTIQUES
   ===================================================== */


/* Nombre de factures */

$sql = "SELECT COUNT(*) AS total
        FROM facture";


$result =
    $connexion->query($sql);


$data =
    $result->fetch_assoc();


$totalFactures =
    $data["total"];



/* Montant total */

$sql = "SELECT
            COALESCE(SUM(montant), 0) AS total
        FROM facture";


$result =
    $connexion->query($sql);


$data =
    $result->fetch_assoc();


$totalMontant =
    $data["total"];



?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">


    <title>
        Factures - Café Resto
    </title>


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


        <a href="produits/produits.php">

            📦 <span>Produits</span>

        </a>


        <a href="commandes/commandes.php">

            📝 <span>Commandes</span>

        </a>


        <a href="employes/employes.php">

            👨‍🍳 <span>Employés</span>

        </a>


        <a href="clients/clients.php">

            👥 <span>Clients</span>

        </a>


        <a href="paiements/paiements.php">

            💳 <span>Paiements</span>

        </a>


        <a href="depenses/depense.php">

            💰 <span>Dépenses</span>

        </a>


        <a
            href="factures.php"
            class="active">

            🧾 <span>Factures</span>

        </a>


        <a href="rapports.php">

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

                Gestion des factures

            </h2>


            <button
                class="btn btn-success"
                data-bs-toggle="modal"
                data-bs-target="#ajoutModal">


                <i class="bi bi-plus-circle"></i>


                Ajouter une facture


            </button>


        </div>



        <!-- =================================================
             MESSAGE
             ================================================= -->

        <?php if ($message !== "") { ?>


            <div
                class="alert alert-<?php echo $typeMessage; ?> alert-dismissible fade show">


                <?php
                echo htmlspecialchars($message);
                ?>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>


            </div>


        <?php } ?>



        <!-- =================================================
             RECHERCHE
             ================================================= -->

        <form
            method="GET"
            action="factures.php"
            class="d-flex gap-2 mb-4">


            <input
                type="text"
                name="recherche"
                class="form-control"
                placeholder="Rechercher une facture..."
                value="<?php
                echo htmlspecialchars(
                    $recherche
                );
                ?>">


            <button
                type="submit"
                class="btn btn-success">


                <i class="bi bi-search"></i>


                Rechercher


            </button>



            <?php if ($recherche !== "") { ?>


                <a
                    href="factures.php"
                    class="btn btn-secondary">


                    Réinitialiser


                </a>


            <?php } ?>


        </form>



        <!-- =================================================
             STATISTIQUES
             ================================================= -->

        <div class="row mb-4">


            <!-- TOTAL FACTURES -->

            <div class="col-md-6 mb-3">


                <div
                    class="card text-white bg-primary shadow">


                    <div class="card-body">


                        <h5>

                            Nombre de factures

                        </h5>


                        <h2>

                            <?php
                            echo $totalFactures;
                            ?>

                        </h2>


                    </div>


                </div>


            </div>



            <!-- MONTANT TOTAL -->

            <div class="col-md-6 mb-3">


                <div
                    class="card text-white bg-success shadow">


                    <div class="card-body">


                        <h5>

                            Montant total des factures

                        </h5>


                        <h2>


                            <?php

                            echo number_format(
                                $totalMontant,
                                0,
                                ",",
                                " "
                            );

                            ?>


                            FCFA


                        </h2>


                    </div>


                </div>


            </div>


        </div>



        <!-- =================================================
             TABLEAU
             ================================================= -->

        <div class="card shadow-sm">


            <div
                class="card-header bg-success text-white">


                <h5 class="mb-0">

                    Liste des factures

                </h5>


            </div>



            <div class="card-body">


                <div class="table-responsive">


                    <table
                        class="table table-hover table-bordered">


                        <thead>


                            <tr>


                                <th>
                                    ID
                                </th>


                                <th>
                                    Nom / Détails
                                </th>


                                <th>
                                    Montant
                                </th>


                                <th>
                                    Utilisateur
                                </th>


                                <th>
                                    Actions
                                </th>


                            </tr>


                        </thead>



                        <tbody>


                        <?php

                        if (
                            $factures &&
                            $factures->num_rows > 0
                        ) {

                        ?>


                            <?php

                            while (
                                $facture =
                                $factures->fetch_assoc()
                            ) {

                            ?>


                                <tr>


                                    <td>

                                        #<?php
                                        echo $facture[
                                            "id_facture"
                                        ];
                                        ?>

                                    </td>



                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $facture["nom"]
                                        );
                                        ?>

                                    </td>



                                    <td>


                                        <strong>


                                            <?php

                                            echo number_format(
                                                $facture[
                                                    "montant"
                                                ],
                                                0,
                                                ",",
                                                " "
                                            );

                                            ?>


                                            FCFA


                                        </strong>


                                    </td>



                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $facture[
                                                "nom_utilisateur"
                                            ]
                                            ?? "Inconnu"
                                        );

                                        ?>

                                    </td>



                                    <td>


                                        <!-- MODIFIER -->

                                        <a
                                            href="factures.php?modifier=<?php echo $facture['id_facture']; ?>"
                                            class="btn btn-primary btn-sm">


                                            <i
                                                class="bi bi-pencil">
                                            </i>


                                        </a>



                                        <!-- SUPPRIMER -->

                                        <form
                                            method="POST"
                                            action="factures.php"
                                            style="display:inline;"
                                            onsubmit="return confirm('Voulez-vous vraiment supprimer cette facture ?');">


                                            <input
                                                type="hidden"
                                                name="action"
                                                value="supprimer">


                                            <input
                                                type="hidden"
                                                name="id_facture"
                                                value="<?php
                                                echo $facture[
                                                    'id_facture'
                                                ];
                                                ?>">


                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm">


                                                <i
                                                    class="bi bi-trash">
                                                </i>


                                            </button>


                                        </form>


                                    </td>


                                </tr>


                            <?php } ?>


                        <?php

                        } else {

                        ?>


                            <tr>


                                <td
                                    colspan="5"
                                    class="text-center">


                                    Aucune facture enregistrée.


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
     MODAL AJOUT
     ===================================================== -->

<div
    class="modal fade"
    id="ajoutModal"
    tabindex="-1">


    <div class="modal-dialog">


        <div class="modal-content">


            <div class="modal-header">


                <h5 class="modal-title">

                    Ajouter une facture

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>


            </div>



            <form
                method="POST"
                action="factures.php">


                <div class="modal-body">


                    <input
                        type="hidden"
                        name="action"
                        value="ajouter">



                    <!-- NOM -->

                    <div class="mb-3">


                        <label class="form-label">

                            Nom / Détails de la facture

                        </label>


                        <input
                            type="text"
                            name="nom"
                            class="form-control"
                            placeholder="Exemple : Coca-Cola × 2 + Riz × 1"
                            required>


                    </div>



                    <!-- MONTANT -->

                    <div class="mb-3">


                        <label class="form-label">

                            Montant

                        </label>


                        <input
                            type="number"
                            name="montant"
                            class="form-control"
                            min="1"
                            placeholder="Exemple : 5000"
                            required>


                    </div>


                </div>



                <div class="modal-footer">


                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">


                        Annuler


                    </button>


                    <button
                        type="submit"
                        class="btn btn-success">


                        <i
                            class="bi bi-check-circle">
                        </i>


                        Enregistrer


                    </button>


                </div>


            </form>


        </div>


    </div>


</div>



<!-- =====================================================
     MODAL MODIFICATION
     ===================================================== -->

<?php if ($factureModification) { ?>


<div
    class="modal fade show"
    style="
        display:block;
        background:rgba(0,0,0,.5);
    "
    tabindex="-1">


    <div class="modal-dialog">


        <div class="modal-content">


            <div class="modal-header">


                <h5 class="modal-title">

                    Modifier la facture

                </h5>


                <a
                    href="factures.php"
                    class="btn-close">
                </a>


            </div>



            <form
                method="POST"
                action="factures.php">


                <div class="modal-body">


                    <input
                        type="hidden"
                        name="action"
                        value="modifier">


                    <input
                        type="hidden"
                        name="id_facture"
                        value="<?php
                        echo $factureModification[
                            'id_facture'
                        ];
                        ?>">



                    <!-- NOM -->

                    <div class="mb-3">


                        <label class="form-label">

                            Nom / Détails

                        </label>


                        <input
                            type="text"
                            name="nom"
                            class="form-control"
                            value="<?php
                            echo htmlspecialchars(
                                $factureModification[
                                    'nom'
                                ]
                            );
                            ?>"
                            required>


                    </div>



                    <!-- MONTANT -->

                    <div class="mb-3">


                        <label class="form-label">

                            Montant

                        </label>


                        <input
                            type="number"
                            name="montant"
                            class="form-control"
                            min="1"
                            value="<?php
                            echo $factureModification[
                                'montant'
                            ];
                            ?>"
                            required>


                    </div>


                </div>



                <div class="modal-footer">


                    <a
                        href="factures.php"
                        class="btn btn-secondary">


                        Annuler


                    </a>


                    <button
                        type="submit"
                        class="btn btn-success">


                        <i
                            class="bi bi-check-circle">
                        </i>


                        Enregistrer les modifications


                    </button>


                </div>


            </form>


        </div>


    </div>


</div>


<?php } ?>



<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>

