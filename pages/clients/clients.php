
<?php

include "../../php/connexion.php";


/* =====================================================
   RÉCUPÉRER LES CLIENTS ET LEURS COMMANDES
   ===================================================== */

$sql = "SELECT
            client.id_client,
            client.nom,
            client.telephone,
            client.adresse,

            commande.id_commande,
            commande.nom AS commande,
            commande.quantite,
            commande.type,
            commande.statut,
            commande.date

        FROM client

        LEFT JOIN commande
        ON client.id_client = commande.id_client

        ORDER BY commande.id_commande DESC";


$resultat = $connexion->query($sql);


if (!$resultat) {

    die(
        "Erreur SQL : "
        . $connexion->error
    );

}

?>


<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Clients - Café Resto
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <style>

        body {

            margin: 0;

            background: #f4f6f9;

            font-family: Arial, sans-serif;

        }


        .sidebar {

            position: fixed;

            left: 0;

            top: 0;

            width: 250px;

            height: 100vh;

            background: #198754;

            color: white;

            padding: 25px 15px;

        }


        .logo {

            font-size: 24px;

            font-weight: bold;

            text-align: center;

            margin-bottom: 35px;

        }


        .menu a {

            display: block;

            color: white;

            text-decoration: none;

            padding: 13px 15px;

            margin-bottom: 5px;

            border-radius: 8px;

        }


        .menu a:hover,

        .menu a.active {

            background: rgba(255,255,255,0.2);

        }


        .logout {

            position: absolute;

            bottom: 25px;

            left: 15px;

            right: 15px;

            color: white;

            text-decoration: none;

            padding: 13px;

            border-radius: 8px;

        }


        .logout:hover {

            background: rgba(255,255,255,0.2);

        }


        .main-content {

            margin-left: 250px;

            min-height: 100vh;

        }


        .topbar {

            background: white;

            height: 70px;

            display: flex;

            justify-content: flex-end;

            align-items: center;

            padding: 0 30px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.05);

        }


        .admin {

            font-weight: bold;

        }


        .content {

            padding: 30px;

        }


        .titre {

            color: #198754;

            font-weight: bold;

        }


        .card {

            border: none;

            border-radius: 12px;

            box-shadow: 0 4px 15px rgba(0,0,0,0.06);

        }


        .table th {

            background: #198754;

            color: white;

            white-space: nowrap;

        }


        .table td {

            vertical-align: middle;

        }


        .badge-attente {

            background: #ffc107;

            color: #212529;

        }


        .badge-preparation {

            background: #0dcaf0;

            color: #212529;

        }


        .badge-terminee {

            background: #198754;

            color: white;

        }


        .badge-annulee {

            background: #dc3545;

            color: white;

        }


        .aucune {

            padding: 40px;

            text-align: center;

            color: #777;

        }

    </style>

</head>


<body>


<!-- =====================================================
     MENU ADMINISTRATEUR
     ===================================================== -->

     <?php
 include "../../partials/sidebar.php";

 ?>



<!-- =====================================================
     CONTENU PRINCIPAL
     ===================================================== -->

<main class="main-content">


    <div class="topbar">

        <span class="admin">

            👤 Administrateur

        </span>

    </div>


    <div class="content">


        <h2 class="titre mb-4">

            👥 Gestion des clients

        </h2>


        <div class="card">


            <div class="card-body">


                <div class="table-responsive">


                    <table
                        class="table table-hover align-middle">


                        <thead>

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Nom
                                </th>

                                <th>
                                    Téléphone
                                </th>

                                <th>
                                    Adresse
                                </th>

                                <th>
                                    Commande
                                </th>

                                <th>
                                    Quantité
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Statut
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php


                        if (
                            $resultat->num_rows > 0
                        ) {


                            $numero = 1;


                            while (
                                $client =
                                $resultat->fetch_assoc()
                            ) {


                        ?>


                            <tr>


                                <td>

                                    <?php
                                    echo $numero++;
                                    ?>

                                </td>


                                <td>

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $client["nom"]
                                        );

                                        ?>

                                    </strong>

                                </td>


                                <td>

                                    📞

                                    <?php

                                    echo htmlspecialchars(
                                        $client["telephone"]
                                    );

                                    ?>

                                </td>


                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $client["adresse"]
                                    );

                                    ?>

                                </td>


                                <td>

                                    <?php


                                    if (
                                        !empty(
                                            $client["commande"]
                                        )
                                    ) {

                                        echo htmlspecialchars(
                                            $client["commande"]
                                        );

                                    } else {

                                        echo "<span class='text-muted'>
                                                Aucune commande
                                              </span>";

                                    }

                                    ?>

                                </td>


                                <td>

                                    <?php

                                    if (
                                        $client["quantite"]
                                        !== null
                                    ) {

                                        echo
                                            $client["quantite"];

                                    } else {

                                        echo "-";

                                    }

                                    ?>

                                </td>


                                <td>

                                    <?php

                                    if (
                                        !empty(
                                            $client["type"]
                                        )
                                    ) {

                                        echo htmlspecialchars(
                                            $client["type"]
                                        );

                                    } else {

                                        echo "-";

                                    }

                                    ?>

                                </td>


                                <td>

                                    <?php

                                    if (
                                        !empty(
                                            $client["date"]
                                        )
                                    ) {

                                        echo
                                            date(
                                                "d/m/Y",
                                                strtotime(
                                                    $client["date"]
                                                )
                                            );

                                    } else {

                                        echo "-";

                                    }

                                    ?>

                                </td>


                                <td>


                                    <?php


                                    $statut =
                                        $client["statut"]
                                        ?? "";


                                    if (
                                        $statut
                                        === "En attente"
                                    ) {

                                        $classe =
                                            "badge-attente";

                                    } elseif (
                                        $statut
                                        === "En préparation"
                                    ) {

                                        $classe =
                                            "badge-preparation";

                                    } elseif (
                                        $statut
                                        === "Terminée"
                                    ) {

                                        $classe =
                                            "badge-terminee";

                                    } elseif (
                                        $statut
                                        === "Annulée"
                                    ) {

                                        $classe =
                                            "badge-annulee";

                                    } else {

                                        $classe =
                                            "bg-secondary";

                                    }


                                    ?>


                                    <span
                                        class="badge <?php
                                            echo $classe;
                                        ?>">

                                        <?php

                                        echo htmlspecialchars(
                                            $statut
                                            ?: "Aucune commande"
                                        );

                                        ?>

                                    </span>


                                </td>


                            </tr>


                        <?php


                            }


                        } else {


                        ?>


                            <tr>

                                <td
                                    colspan="9"
                                    class="aucune">

                                    👥 Aucun client
                                    enregistré pour le moment.

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


    </div>

</main>


</body>

</html>

