<?php

include "../../php/connexion.php";

/* =========================================
   RÉCUPÉRER L'ID DU PAIEMENT
   ========================================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {

    die("Paiement introuvable.");

}

$id = (int) $_GET['id'];


/* =========================================
   RÉCUPÉRER LE PAIEMENT
   ========================================= */

$sql = "SELECT *
        FROM transaction_paiement
        WHERE id_transaction = ?";

$stmt = $connexion->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$resultat = $stmt->get_result();

if ($resultat->num_rows == 0) {

    die("Ce paiement n'existe pas.");

}

$paiement = $resultat->fetch_assoc();

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Modifier un paiement - Café Resto</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="../../css/style.css">

</head>


<body>


<!-- =========================================
     MENU
     ========================================= -->

     <?php

     include "../../partials/sibebar.php";

?>


<!-- =========================================
     CONTENU
     ========================================= -->

<main class="main-content">


    <div class="topbar">

        <span class="menu-icon">

            ☰

        </span>

        <span class="admin">

            👤 Admin ▾

        </span>

    </div>



    <div class="content">


        <div class="card shadow-sm">


            <div class="card-header bg-success text-white">

                <h5 class="mb-0">

                    <i class="bi bi-pencil-square"></i>

                    Modifier le paiement

                </h5>

            </div>


            <div class="card-body">


                <form
                    action="update_paiement.php"
                    method="POST">


                    <!-- ID DU PAIEMENT -->

                    <input
                        type="hidden"
                        name="id_transaction"
                        value="<?php echo $paiement['id_transaction']; ?>">



                    <!-- COMMANDE -->

                    <div class="mb-3">

                        <label
                            for="id_commande"
                            class="form-label">

                            Numéro de commande

                        </label>

                        <input
                            type="number"
                            name="id_commande"
                            id="id_commande"
                            class="form-control"
                            min="1"
                            value="<?php echo htmlspecialchars($paiement['id_commande']); ?>"
                            >

                    </div>



                    <!-- MONTANT -->

                    <div class="mb-3">

                        <label
                            for="montant"
                            class="form-label">

                            Montant

                        </label>


                        <div class="input-group">

                            <input
                                type="number"
                                name="montant"
                                id="montant"
                                class="form-control"
                                min="0"
                                value="<?php echo htmlspecialchars($paiement['montant']); ?>"
                                >

                            <span class="input-group-text">

                                FCFA

                            </span>

                        </div>

                    </div>



                    <!-- TYPE -->

                    <div class="mb-3">

                        <label
                            for="type"
                            class="form-label">

                            Type de paiement

                        </label>


                        <select
                            name="type"
                            id="type"
                            class="form-select"
                            >


                            <option
                                value="Espèces"
                                <?php
                                if ($paiement['type'] == "Espèces")
                                    echo "selected";
                                ?>>

                                Espèces

                            </option>


                            <option
                                value="Mobile Money"
                                <?php
                                if ($paiement['type'] == "Mobile Money")
                                    echo "selected";
                                ?>>

                                Mobile Money

                            </option>


                            <option
                                value="Carte bancaire"
                                <?php
                                if ($paiement['type'] == "Carte bancaire")
                                    echo "selected";
                                ?>>

                                Carte bancaire

                            </option>


                        </select>

                    </div>



                    <!-- STATUT -->

                    <div class="mb-3">

                        <label
                            for="statut"
                            class="form-label">

                            Statut

                        </label>


                        <select
                            name="statut"
                            id="statut"
                            class="form-select"
                            required>


                            <option
                                value="Payé"
                                <?php
                                if ($paiement['statut'] == "Payé")
                                    echo "selected";
                                ?>>

                                Payé

                            </option>


                            <option
                                value="En attente"
                                <?php
                                if ($paiement['statut'] == "En attente")
                                    echo "selected";
                                ?>>

                                En attente

                            </option>


                        </select>

                    </div>



                    <!-- RÉFÉRENCE -->

                    <div class="mb-3">

                        <label
                            for="reference_mobile_money"
                            class="form-label">

                            Référence Mobile Money

                        </label>


                        <input
                            type="text"
                            name="reference_mobile_money"
                            id="reference_mobile_money"
                            class="form-control"
                            value="<?php echo htmlspecialchars($paiement['reference_mobile_money'] ?? ''); ?>">

                    </div>



                    <!-- BOUTONS -->

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-check-circle"></i>

                        Enregistrer les modifications

                    </button>


                    <a
                        href="paiements.php"
                        class="btn btn-secondary">

                        Annuler

                    </a>


                </form>


            </div>

        </div>


    </div>


</main>


</body>

</html>