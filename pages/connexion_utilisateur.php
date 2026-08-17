
<?php

session_start();

include "../php/connexion.php";

$message = "";


/* =====================================================
   TRAITEMENT DE LA CONNEXION
   ===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = trim($_POST['nom'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';


    if ($nom === "" || $mot_de_passe === "") {

        $message = "Veuillez remplir tous les champs.";

    } else {

        /* Recherche de l'utilisateur */

        $sql = "SELECT *
                FROM utilisateur
                WHERE nom = ?";

        $stmt = $connexion->prepare($sql);

        $stmt->bind_param("s", $nom);

        $stmt->execute();

        $resultat = $stmt->get_result();


        /* Vérifier si l'utilisateur existe */

        if ($resultat->num_rows === 1) {

            $utilisateur = $resultat->fetch_assoc();


            /*
             * Pour l'instant, on compare directement
             * le mot de passe enregistré en base.
             */

            if ($mot_de_passe === $utilisateur['mot_de_passe']) {

                /* Enregistrer l'utilisateur dans la session */

                $_SESSION['id_utilisateur'] =
                    $utilisateur['id_utilisateur'];

                $_SESSION['nom'] =
                    $utilisateur['nom'];

                $_SESSION['role'] =
                    $utilisateur['role'];

                $_SESSION['contact'] =
                    $utilisateur['contact'];


                /* Redirection vers le dashboard */

                header("Location: dashboard.php");

                exit();

            } else {

                $message = "Mot de passe incorrect.";

            }

        } else {

            $message = "Utilisateur introuvable.";

        }

    }

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
        Connexion utilisateur - Café Resto
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"  href="../css/style.css">


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            min-height: 100vh;

            font-family: Arial, sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #198754,
                    #0f5132
                );

            display: flex;

            align-items: center;

            justify-content: center;

        }


        .connexion-container {

            width: 90%;

            max-width: 450px;

        }


        .carte {

            background: white;

            padding: 35px;

            border-radius: 20px;

            box-shadow:
                0 10px 30px
                rgba(0, 0, 0, 0.25);

        }


        .logo {

            text-align: center;

            font-size: 60px;

            margin-bottom: 5px;

        }


        h2 {

            text-align: center;

            color: #198754;

            font-weight: bold;

            margin-bottom: 5px;

        }


        .sous-titre {

            text-align: center;

            color: #777;

            margin-bottom: 30px;

        }


        .form-label {

            font-weight: bold;

        }


        .btn-connexion {

            width: 100%;

            background: #198754;

            color: white;

            border: none;

            padding: 12px;

            border-radius: 10px;

            font-weight: bold;

        }


        .btn-connexion:hover {

            background: #146c43;

            color: white;

        }


        .retour {

            display: block;

            text-align: center;

            margin-top: 20px;

            text-decoration: none;

            color: #198754;

        }


        .retour:hover {

            text-decoration: underline;

        }

    </style>

</head>


<body>


<div class="connexion-container">


    <div class="carte">


        <div class="logo">
            ☕
        </div>


        <h2>
            Café Resto
        </h2>


        <p class="sous-titre">

            Connexion à l'espace utilisateur

        </p>


        <?php if ($message !== ""): ?>

            <div class="alert alert-danger">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="">


            <!-- NOM -->

            <div class="mb-3">

                <label
                    class="form-label"
                    for="nom">

                    Nom

                </label>


                <input
                    type="text"
                    id="nom"
                    name="nom"
                    class="form-control"
                    placeholder="Entrez votre nom"
                    >

            </div>


            <!-- MOT DE PASSE -->

            <div class="mb-4">

                <label
                    class="form-label"
                    for="mot_de_passe">

                    Mot de passe

                </label>


                <input
                    type="password"
                    id="mot_de_passe"
                    name="mot_de_passe"
                    class="form-control"
                    placeholder="Entrez votre mot de passe"
                   >

            </div>


            <!-- BOUTON -->

            <button
                type="submit"
                class="btn-connexion">

                🔐 Se connecter

            </button>


        </form>


        <!-- RETOUR -->

        <a
            href="accueil.php"
            class="retour">

            ← Retour à l'accueil

        </a>


    </div>

</div>


</body>

</html>

