
<?php

include "../../php/connexion.php";

$id = $_GET['id'];

$sql = "DELETE FROM employe WHERE id_employe = ?";

$stmt = $connexion->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: employes.php");
    exit();

} else {

    echo "Erreur lors de la suppression : " . $stmt->error;
}

?>

