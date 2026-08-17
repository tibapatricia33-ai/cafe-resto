
<?php

session_start();

// Détruire la session
session_unset();
session_destroy();

// Retourner vers connexion.php qui se trouve dans le dossier php
header("Location: ../pages/accueil.php");
exit();





