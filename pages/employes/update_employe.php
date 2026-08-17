
<?php

include "../../php/connexion.php";

$id_employe = $_POST['id_employe'];
$nom = $_POST['nom'];
$poste = $_POST['poste'];
$salaire = $_POST['salaire'];
$horaire = $_POST['horaire'];


$sql = "UPDATE employe SET
        nom = ?,
        poste = ?,
        salaire = ?,
        horaire = ?
        WHERE id_employe = ?";

$stmt = $connexion->prepare($sql);

$stmt->bind_param("ssdsi", $nom, $poste, $salaire, $horaire, $id_employe );

if ($stmt->execute()) {

    header("Location: employes.php");
    exit();

} else {

    echo "Erreur lors de la modification : " . $stmt->error;
}

?>

