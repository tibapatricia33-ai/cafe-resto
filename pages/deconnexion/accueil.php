
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Bienvenue - Café Resto</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"  href="../../css/style.css">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: linear-gradient(
                135deg,
                #198754,
                #0f5132
            );

            display: flex;
            align-items: center;
            justify-content: center;
        }


        .container-accueil {
            width: 90%;
            max-width: 1000px;
            text-align: center;
        }


        .logo {
            font-size: 70px;
            margin-bottom: 10px;
        }


        .titre {
            color: white;
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 10px;
        }


        .sous-titre {
            color: white;
            font-size: 18px;
            margin-bottom: 40px;
        }


        .choix {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }


        .carte {
            background: white;
            width: 330px;
            min-height: 300px;

            border-radius: 20px;

            padding: 35px 25px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.2);

            transition: 0.3s;
        }


        .carte:hover {
            transform: translateY(-8px);

            box-shadow:
                0 15px 35px rgba(0, 0, 0, 0.3);
        }


        .icone {
            font-size: 65px;
            margin-bottom: 20px;
        }


        .carte h2 {
            font-size: 25px;
            margin-bottom: 15px;
        }


        .carte p {
            color: #666;
            min-height: 55px;
        }


        .btn-choix {
            display: inline-block;

            margin-top: 20px;

            padding: 12px 30px;

            border-radius: 10px;

            text-decoration: none;

            color: white;

            font-weight: bold;

            transition: 0.2s;
        }


        .btn-utilisateur {
            background: #198754;
        }


        .btn-utilisateur:hover {
            background: #146c43;
            color: white;
        }


        .btn-client {
            background: #0d6efd;
        }


        .btn-client:hover {
            background: #0b5ed7;
            color: white;
        }


        .footer {
            margin-top: 35px;
            color: white;
            font-size: 14px;
        }

    </style>

</head>


<body>


<div class="container-accueil">


    <!-- LOGO -->

    <div class="logo">
        ☕
    </div>


    <!-- TITRE -->

    <div class="titre">
        Café Resto
    </div>


    <div class="sous-titre">

        Bienvenue ! Que souhaitez-vous faire ?

    </div>



    <!-- CHOIX -->

    <div class="choix">


        <!-- ==========================================
             UTILISATEUR
             ========================================== -->

        <div class="carte">


            <div class="icone">
                👨‍💼
            </div>


            <h2>
                Espace utilisateur
            </h2>


            <p>

                Accédez à l'espace de gestion
                du Café Resto.

            </p>


            <a
                href="connexion_utilisateur.php"
                class="btn-choix btn-utilisateur">

                Se connecter

            </a>


        </div>



        <!-- ==========================================
             CLIENT
             ========================================== -->

        <div class="carte">


            <div class="icone">
                🛒
            </div>


            <h2>
                Commander en ligne
            </h2>


            <p>

                Consultez nos produits,
                ajoutez-les à votre panier
                et passez votre commande.

            </p>


            <a href="commande_client.php" class="btn-choix btn-client">
    Commander maintenant
</a>


        </div>


    </div>



    <!-- FOOTER -->

    <div class="footer">

        © Café Resto - Tous droits réservés

    </div>


</div>


</body>

</html>

