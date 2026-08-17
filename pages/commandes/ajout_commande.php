<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter une commande</title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
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


<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">
    <h3>Ajouter une commande</h3>
</div>


<div class="card-body">


<form action="/commandes/enregistrer_commande.php" method="POST">


<!-- nom du client -->
    <div class="mb-3">
        <label class="form-label">Nom du client</label>

        <input
            type="text"
            name="nom"
            class="form-control"
            placeholder="Entrez le nom du client"
        >
    </div>


<!-- numero du client -->
 <div class=""mb-3">
        <label class="form-label">Numéro du client</label>

        <input
            type="text"
            name="numero"
            class="form-control"
            placeholder="Entrez le numéro du client"
        >


    </div>
    <!-- email du client -->
    <div class="mb-3">
        <label class="form-label">Email du client</label>

        <input
            type="email"
            name="email"
            class="form-control"
            placeholder="Entrez l'email du client"
        >


    <!-- Type -->
    <div class="mb-3">
        <label class="form-label">Type</label>

        <select name="type" class="form-control" required>
            <option value="">Choisir le type</option>
            <option value="Sur place">Sur place</option>
            <option value="À emporter">À emporter</option>
            <option value="Livraison">Livraison</option>
        </select>
    </div>


    <!-- Statut -->
    <div class="mb-3">
        <label class="form-label">Statut</label>

        <select name="statut" class="form-control" required>
            <option value="En attente">En attente</option>
            <option value="Terminée">Terminée</option>
        </select>
    </div>


    <!-- Date -->
    <div class="mb-3">
        <label class="form-label">Date</label>

        <input
            type="date"
            name="date"
            class="form-control"
            value="<?php echo date('Y-m-d'); ?>"
            
        >
    </div>


    <!-- NOM / DÉTAILS -->
    <div class="mb-3">

        <label for="nom" class="form-label">
            Détails de la commande
        </label>

        <input
            type="text"
            name="nom"
            id="nom"
            class="form-control"
            placeholder="Exemple : Coca-Cola × 1 + Macaroni × 2"
           
        >

    </div>


    <!-- QUANTITÉ -->
    <div class="mb-3">

        <label class="form-label">
            Quantité totale
        </label>

        <input
            type="number"
            name="quantite"
            class="form-control"
            min="1"
        >

    </div>

<!-- montant total -->
 <div class="mb-3">
        <label class="form-label">Montant total</label>

        <input
            type="number"
            name="montant"
            class="form-control"
            placeholder="Entrez le montant total">
            </div>

            
    <button
        type="submit"
        class="btn btn-success">

        Enregistrer la commande

    </button>

</form>
</body>

</html>