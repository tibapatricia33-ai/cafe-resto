
<?php
include "../../php/connexion.php";


if (isset($_GET['recherche']) && !empty($_GET['recherche'])) {

    $recherche = $_GET['recherche'];

    $sql = "SELECT * FROM employe
            WHERE nom LIKE ?
            OR poste LIKE ?";

    $stmt = $connexion->prepare($sql);

    $recherche = "%" . $recherche . "%";

    $stmt->bind_param("ss", $recherche, $recherche);

    $stmt->execute();

    $resultat = $stmt->get_result();

} else {

    $sql = "SELECT * FROM employe";

    $resultat = $connexion->query($sql);
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Employés - Café Resto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Si ton fichier style.css est dans cafe-resto/css/ -->
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <!-- MENU LATERAL -->
   
           <?php

           include "../../partials/sidebar.php";

           ?>


    <!-- CONTENU PRINCIPAL -->
    <main class="main-content">

        <div class="topbar">
            <span class="menu-icon">☰</span>

            <span class="admin">
                👤 Admin ▾
            </span>
        </div>


        <div class="content">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h2>Gestion des employés</h2>

                <a href="/employes/ajouter_employe.php" class="btn btn-success">
                    + Ajouter un employé
                </a>

            </div>


            <!-- RECHERCHE -->

            <form method="GET" class="search-form mb-4">

                <input
                    type="text"
                    name="recherche"
                    class="form-control"
                    placeholder="Rechercher un employé..."
                    value="<?php echo isset($_GET['recherche']) ? $_GET['recherche'] : ''; ?>"
                >

                <button type="submit" class="btn btn-success">
                    🔍 Rechercher
                </button>

            </form>


            <!-- TABLEAU -->

            <div class="table-container">

                <table class="table table-bordered table-hover mb-0">

                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Poste</th>
                            <th>Salaire</th>
                            <th>Horaire</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($employe = $resultat->fetch_assoc()) { ?>

                        <tr>

                            <td>
                                <?php echo $employe['nom']; ?>
                            </td>

                            <td>
                                <?php echo $employe['poste']; ?>
                            </td>

                            <td>
                                <?php echo $employe['salaire']; ?>
                            </td>

                            <td>
                                <?php echo $employe['horaire']; ?>
                            </td>

                            <td>

                                <a
                                    href="modifier_employe.php?id=<?php echo $employe['id_employe']; ?>"
                                    class="btn btn-primary btn-sm">
                                    ✏️ Modifier
                                </a>

                                <a
                                    href="supprimer_employe.php?id=<?php echo $employe['id_employe']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Voulez-vous vraiment supprimer cet employé ?');">
                                    🗑️ Supprimer
                                </a>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

            <div class="total-employes">
                Total employés :
                <strong>
                    <?php
                    // Cette partie peut être retirée si tu ne veux pas le compteur
                    ?>
                </strong>
            </div>

        </div>

    </main>

</body>

</html>