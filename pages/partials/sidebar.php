<?php 
include('constant.php');
?>

<aside class="sidebar">


    <div class="logo">

        ☕ Café Resto

    </div>


    <nav class="menu">


        <a href="<?php echo $_ROOT; ?>/dashboard.php">

            🏠 Dashboard

        </a>


        <a href="<?php echo $_ROOT; ?>/produits/produits.php">

            📦 Produits

        </a>


        <a href="<?php echo $_ROOT; ?>/commandes/commandes.php">

            📝 Commandes

        </a>


        <a href="<?php echo $_ROOT; ?>/employes/employes.php">

            👨‍🍳 Employés

        </a>


        <a
            href="<?php echo $_ROOT; ?>/clients/clients.php"
            class="active">

            👥 Clients

        </a>


        <a href="<?php echo $_ROOT; ?>/paiements/paiements.php">

            💳 Paiements

        </a>


        <a href="<?php echo $_ROOT; ?>/depenses/depense.php">

            💰 Dépenses

        </a>


        <a href="<?php echo $_ROOT; ?>/factures.php">

            🧾 Factures

        </a>


        <a href="<?php echo $_ROOT; ?>/rapports.php">

            📊 Rapports

        </a>


    </nav>


    <a
        href="<?php echo $_ROOT; ?>/deconnexion/deconnexion.php"
        class="logout">

        🚪 Déconnexion

    </a>


</aside>
