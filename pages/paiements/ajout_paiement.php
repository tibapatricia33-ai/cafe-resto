<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Ajouter un paiement - Café Resto</title>

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
          href="../css/style.css">

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


        <div class="card shadow-sm">


            <div class="card-header bg-success text-white">

                <h5 class="mb-0">

                    <i class="bi bi-credit-card"></i>

                    Ajouter un paiement

                </h5>

            </div>


            <div class="card-body">


                <form
                    action="enregistrer_paiement.php"
                    method="POST">


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
                            placeholder="Exemple : 15"
                            min="1"
                            required>

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
                                placeholder="Exemple : 2500"
                                min="0"
                                required>


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
                            required>

                            <option value="">

                                Choisir un mode de paiement

                            </option>

                            <option value="Espèces">

                                Espèces

                            </option>

                            <option value="Mobile Money">

                                Mobile Money

                            </option>

                            <option value="Carte bancaire">

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

                            <option value="Payé">

                                Payé

                            </option>

                            <option value="En attente">

                                En attente

                            </option>

                        </select>

                    </div>



                    <!-- RÉFÉRENCE MOBILE MONEY -->

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
                            placeholder="Exemple : TXN123456">


                        <small class="text-muted">

                            Laisser vide si le paiement n'est pas effectué par Mobile Money.

                        </small>

                    </div>



                    <!-- BOUTONS -->

                    <div class="d-flex gap-2">


                        <button
                            type="submit"
                            class="btn btn-success">

                            <i class="bi bi-check-circle"></i>

                            Enregistrer

                        </button>


                        <a
                            href="paiements.php"
                            class="btn btn-secondary">

                            Annuler

                        </a>


                    </div>


                </form>


            </div>

        </div>


    </div>


</main>


</body>

</html>