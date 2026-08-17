
<?php
include "../../php/connexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = $_POST['nom'];
    $poste = $_POST['poste'];
    $salaire = $_POST['salaire'];
    $horaire = $_POST['horaire'];
    $id_restaurant = $_POST['id_restaurant'];

    $sql = "INSERT INTO employe
            (nom, poste, salaire, horaire, id_restaurant)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $connexion->prepare($sql);

    $stmt->bind_param(
        "ssisi",
        $nom,
        $poste,
        $salaire,
        $horaire,
        $id_restaurant
    );

    if ($stmt->execute()) {
        header("Location: employes.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout : " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ajouter un employé - Café Resto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" href="../../css/style.css">
</head>

<body>

<div class="container mt-5">

    <h2 class="mb-4">Ajouter un employé</h2>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Nom de l'employé</label>
            <input type="text" name="nom" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Poste</label>
            <input type="text"  name="poste" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Salaire</label>
            <input type="number"name="salaire"class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Horaire</label>
            <input type="text"name="horaire"class="form-control"placeholder="Ex : 7h00 - 15h00">
        </div>

        <button type="submit" class="btn btn-success">
            Ajouter
        </button>

        <a href="employes.php" class="btn btn-secondary">
            Annuler
        </a>

    </form>

</div>

</body>
</html>

