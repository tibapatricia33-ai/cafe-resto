
<?php

include "../../php/connexion.php";

$id = $_GET['id'];

$sql = "SELECT * FROM employe WHERE id_employe = '$id'";

$resultat = $connexion->query($sql);

$employe = $resultat->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Modifier un employé - Café Resto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
     rel="stylesheet" href="/../../css/style.css">
</head>

<body>


<?php


include(__DIR__ . "/../partials/sidebar.php");

?>

<div class="container mt-5">

    <h2 class="mb-4">Modifier un employé</h2>

    <form action="update_employe.php" method="POST">

        <input type="hidden" name="id_employe" value="<?php echo $employe['id_employe']; ?>">

        <div class="mb-3">
            <label class="form-label">Nom de l'employé</label>

            <input type="text" name="nom" class="form-control"  value="<?php echo $employe['nom']; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Poste</label>

            <input type="text" name="poste" class="form-control" value="<?php echo $employe['poste']; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Salaire</label>

            <input type="number"   name="salaire"   class="form-control"   value="<?php echo $employe['salaire']; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Horaire</label>

            <input type="text"   name="horaire"   class="form-control"  value="<?php echo $employe['horaire']; ?>">
        </div>


        <button type="submit" class="btn btn-success">
            Enregistrer les modifications
        </button>

        <a href="employes.php" class="btn btn-secondary">
            Annuler
        </a>

    </form>

</div>

</body>
</html>

