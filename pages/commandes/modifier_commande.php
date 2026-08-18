<?php

include "../../php/connexion.php";

$id = $_GET['id'];

if(isset($_GET['recherche']) && $_GET['recherche'] != ""){

    $recherche = $_GET['recherche'];

    $sql = "SELECT * FROM commande
            WHERE type LIKE '%$recherche%'
            OR statut LIKE '%$recherche%'";

}else{

    $sql = "SELECT * FROM commande";

}

$resultat = $connexion->query($sql);

$commande = $resultat->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une commande</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<?php

include "../../partials/sidebar.php";

?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">
    <h3>Modifier une commande</h3>
</div>

<div class="card-body">

<form action="/../commandes/update_commande.php" method="POST">

<input type="hidden" name="id_commande"
value="<?php echo $commande['id_commande']; ?>">

<div class="mb-3">

<label>nom du client</label>
<input type ="text", 
name="nom du client", 
class="form-control" 
value="<?php echo $commande['nom']; ?>">


<label>Type</label>

<input
type="text"
name="type"
class="form-control"
value="<?php echo $commande['type']; ?>">

</div>

<div class="mb-3">

<label>Statut</label>

<input
type="text"
name="statut"
class="form-control"
value="<?php echo $commande['statut']; ?>">

</div>

<div class="mb-3">

<label>Date</label>

<input
type="date"
name="date"
class="form-control"
value="<?php echo $commande['date']; ?>">

</div>

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
        value="<?php echo $commande['nom']; ?>"
    >

</div>

<div class="mb-3">

<label>Quantité</label>

<input
type="number"
name="quantite"
class="form-control"
value="<?php echo $commande['quantite']; ?>">

</div>

<button type="submit" class="btn btn-success">
    Enregistrer les modifications
</button>

<a href="commandes.php" class="btn btn-secondary">
    Annuler
</a>

</form>

</div>

</div>

</div>

</body>
</html>