<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Ajouter un achat - Café Resto</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="../css/style.css">

</head>


<body>

<div class="container mt-5">

    <div
        class="card shadow mx-auto"
        style="max-width: 650px;">

        <div class="card-header bg-success text-white">

            <h4 class="mb-0">

                <i class="bi bi-cart-plus"></i>

                Enregistrer un achat

            </h4>

        </div>


        <div class="card-body">

            <form
                action="/depenses/enregistrer_depense.php"
                method="POST">


                <!-- DESCRIPTION -->

                <div class="mb-3">

                    <label
                        for="description"
                        class="form-label">

                        Description de l'achat

                    </label>

                    <input
                        type="text"
                        name="description"
                        id="description"
                        class="form-control"
                        placeholder="Exemple : Achat de 2 sacs de riz"
                       >

                </div>


                <!-- MATIÈRE PREMIÈRE -->

                <div class="mb-3">

                    <label
                        for="nom"
                        class="form-label">

                        Matière première

                    </label>

                    <input
                        type="text"
                        name="nom"
                        id="nom"
                        class="form-control"
                        placeholder="Exemple : Riz, huile, viande..."
                        >

                </div>


                <!-- QUANTITÉ -->

                <div class="mb-3">

                    <label
                        for="quantite"
                        class="form-label">

                        Quantité achetée

                    </label>

                    <input
                        type="number"
                        name="quantite"
                        id="quantite"
                        class="form-control"
                        min="0.01"
                        step="0.01"
                        placeholder="Exemple : 2"
                      >

                </div>


                <!-- UNITÉ -->

                <div class="mb-3">

                    <label
                        for="unite"
                        class="form-label">

                        Unité

                    </label>

                    <select
                        name="unite"
                        id="unite"
                        class="form-select"
                       > choisir une unité

                        <option value="panier">
                            panier
                        </option>

                        <option value="sac">
                            Sac
                        </option>

                        <option value="kg">
                            Kilogramme (kg)
                        </option>

                        <option value="litre">
                            Litre
                        </option>

                        <option value="carton">
                            Carton
                        </option>

                        <option value="piece">
                            Pièce
                        </option>

                        <option value="paquet">
                            Paquet
                        </option>

                        <option value="boites">
                            boites
                        </option>

                        <option value="panier">
                            Panier
                        </option>

                    </select>

                </div>


                <!-- MONTANT -->

                <div class="mb-3">

                    <label
                        for="montant"
                        class="form-label">

                        Montant total de l'achat

                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            name="montant"
                            id="montant"
                            class="form-control"
                            min="0"
                            step="1"
                            placeholder="Exemple : 50000"
                            >

                        <span class="input-group-text">

                            FCFA

                        </span>

                    </div>

                </div>


                <!-- DATE -->

                <div class="mb-3">

                    <label
                        for="date"
                        class="form-label">

                        Date de l'achat

                    </label>

                    <input
                        type="date"
                        name="date"
                        id="date"
                        class="form-control"
                        value="<?php echo date('Y-m-d'); ?>"
                        >

                </div>


                <!-- BOUTONS -->

                <button
                    type="submit"
                    class="btn btn-success">

                    <i class="bi bi-check-circle"></i>

                    Enregistrer l'achat

                </button>


                <a
                    href="/depenses/depenses.php"
                    class="btn btn-secondary">

                    Annuler

                </a>

            </form>

        </div>

    </div>

</div>

</body>

</html>