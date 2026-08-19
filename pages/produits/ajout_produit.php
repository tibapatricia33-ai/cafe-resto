<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit - Café Resto</title>

    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

<?php
include(__DIR__ . "/../partials/sidebar.php");
?>


<div class="main">

    <div class="topbar">
        <h3>Ajouter un produit</h3>
    </div>


    <div class="content">

        <form action="../../php/ajouter_produit.php" method="POST">

            <label>Nom du produit :</label>
            <input type="text" name="nom" >


            <label>Prix :</label>
            <input type="number" name="prix" >


            <label>Quantité en stock :</label>
            <input type="number" name="quantite_stock" >


            <label>Seuil d'alerte :</label>
            <input type="number" name="seuil_alert" >


            <button type="submit" class="btn-ajouter">+ Ajouter un produit</button>

        </form>

    </div>

</div>


</body>
</html>