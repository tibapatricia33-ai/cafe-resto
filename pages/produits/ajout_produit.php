<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit - Café Resto</title>

    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

<aside class="sidebar">

    <div class="logo">
        ☕ <span>Café Resto</span>
    </div>

    <nav class="menu">

        <a href="dashboard.php">
            🏠 <span>Dashboard</span>
        </a>

        <a href="produits.php">
            📦 <span>Produits</span>
        </a>

        <a href="commandes.php">
            🛒 <span>Commandes</span>
        </a>

        <a href="employes.php" class="active">
            👥 <span>Employés</span>
        </a>

        <a href="clients.php">
            👤 <span>Clients</span>
        </a>

        <a href="paiements.php">
            💳 <span>Paiements</span>
        </a>

        <a href="depenses.php">
            💰 <span>Dépenses</span>
        </a>

        <a href="factures.php">
            📄 <span>Factures</span>
        </a>

        <a href="rapports.php">
            📊 <span>Rapports</span>
        </a>

    </nav>

    <a href="deconnexion.php" class="logout">
        🚪 <span>Déconnexion</span>
    </a>

</aside>


<div class="main">

    <div class="topbar">
        <h3>Ajouter un produit</h3>
    </div>


    <div class="content">

        <form action="ajouter_produit.php" method="POST">

            <label>Nom du produit :</label>
            <input type="text" name="nom" >


            <label>Prix :</label>
            <input type="number" name="prix" >


            <label>Quantité en stock :</label>
            <input type="number" name="quantite_stock" >


            <label>Seuil d'alerte :</label>
            <input type="number" name="seuil_alert" >


            <a href="../../php/ajouter_produit.php">
    <button class="btn-ajouter">
        + Ajouter un produit
    </button>
</a>

        </form>

    </div>

</div>


</body>
</html>