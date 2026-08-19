<?php

require_once __DIR__ . '/../../php/connexion.php';

/*
=========================================================
 MODULE UNIQUE : GESTION DES DÉPENSES
=========================================================

Ce fichier permet de :
•⁠  ⁠Ajouter une dépense
•⁠  ⁠Modifier une dépense
•⁠  ⁠Supprimer une dépense
•⁠  ⁠Rechercher une dépense
•⁠  ⁠Afficher les statistiques
•⁠  ⁠Afficher toutes les dépenses

/* =====================================================
   MESSAGES
   ===================================================== */
    $message = "";
    $typeMessage = "";

/* =====================================================
   AJOUTER / MODIFIER / SUPPRIMER
   ===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";

    /* =================================================
       AJOUTER
       ================================================= */
    if ($action === "ajouter") {

        $description = trim($_POST["description"] ?? "");
        $quantite = (int)($_POST["quantite"] ?? 0);
        $unite = trim($_POST["unite"] ?? "");
        $montant = (float)($_POST["montant"] ?? 0);
        $date = $_POST["date"] ?? "";

        if (
            empty($description) ||
            $quantite <= 0 ||
            empty($unite) ||
            $montant <= 0 ||
            empty($date)
        ) {
            header("Location: depense.php?erreur=champs");
            exit;
        }

        $sqlUser = "SELECT id_utilisateur FROM utilisateur ORDER BY id_utilisateur ASC LIMIT 1";
        $resultUser = $connexion->query($sqlUser);

        if (!$resultUser || $resultUser->num_rows === 0) {
            header("Location: depense.php?erreur=utilisateur");
            exit;
        }

        $user = $resultUser->fetch_assoc();
        $id_utilisateur = $user["id_utilisateur"];

        $sql = "INSERT INTO depense (description, quantite, unite, montant, date, id_utilisateur) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connexion->prepare($sql);

        if (!$stmt) {
            die("Erreur SQL : " . $connexion->error);
        }

        $stmt->bind_param("sisdsi", $description, $quantite, $unite, $montant, $date, $id_utilisateur);

        if ($stmt->execute()) {
            header("Location: depense.php?ajout=ok");
            exit;
        } else {
            die("Erreur lors de l'ajout : " . $stmt->error);
        }
    }

    /* =================================================
       MODIFIER
       ================================================= */
    if ($action === "modifier") {

        $id_depense = (int)($_POST["id_depense"] ?? 0);
        $description = trim($_POST["description"] ?? "");
        $quantite = (int)($_POST["quantite"] ?? 0);
        $unite = trim($_POST["unite"] ?? "");
        $montant = (float)($_POST["montant"] ?? 0);
        $date = $_POST["date"] ?? "";

        if (
            $id_depense <= 0 ||
            empty($description) ||
            $quantite <= 0 ||
            empty($unite) ||
            $montant <= 0 ||
            empty($date)
        ) {
            header("Location: depense.php?erreur=champs");
            exit;
        }

        $sql = "UPDATE depense SET description = ?, quantite = ?, unite = ?, montant = ?, date = ? WHERE id_depense = ?";
        $stmt = $connexion->prepare($sql);

        if (!$stmt) {
            die("Erreur SQL : " . $connexion->error);
        }

        $stmt->bind_param("sisdsi", $description, $quantite, $unite, $montant, $date, $id_depense);

        if ($stmt->execute()) {
            header("Location: depense.php?modification=ok");
            exit;
        } else {
            die("Erreur lors de la modification : " . $stmt->error);
        }
    }

    /* =================================================
       SUPPRIMER
       ================================================= */
    if ($action === "supprimer") {

        $id_depense = (int)($_POST["id_depense"] ?? 0);

        if ($id_depense <= 0) {
            header("Location: depense.php?erreur=id");
            exit;
        }

        $sql = "DELETE FROM depense WHERE id_depense = ?";
        $stmt = $connexion->prepare($sql);

        if (!$stmt) {
            die("Erreur SQL : " . $connexion->error);
        }

        $stmt->bind_param("i", $id_depense);

        if ($stmt->execute()) {
            header("Location: depense.php?suppression=ok");
            exit;
        } else {
            die("Erreur lors de la suppression : " . $stmt->error);
        }
    }
}

/* =====================================================
   MESSAGES APRÈS REDIRECTION
   ===================================================== */
if (isset($_GET["ajout"]) && $_GET["ajout"] === "ok") {
    $message = "Dépense enregistrée avec succès.";
    $typeMessage = "success";
}

if (isset($_GET["modification"]) && $_GET["modification"] === "ok") {
    $message = "Dépense modifiée avec succès.";
    $typeMessage = "success";
}

if (isset($_GET["suppression"]) && $_GET["suppression"] === "ok") {
    $message = "Dépense supprimée avec succès.";
    $typeMessage = "success";
}

if (isset($_GET["erreur"]) && $_GET["erreur"] === "utilisateur") {
    $message = "Aucun utilisateur n'existe dans la base de données.";
    $typeMessage = "danger";
}

if (isset($_GET["erreur"]) && $_GET["erreur"] === "champs") {
    $message = "Veuillez remplir correctement tous les champs.";
    $typeMessage = "danger";
}

/* =====================================================
   MODE MODIFICATION
   ===================================================== */
$depenseModification = null;

if (isset($_GET["modifier"])) {
    $idModifier = (int)$_GET["modifier"];

    $sql = "SELECT * FROM depense WHERE id_depense = ?";
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param("i", $idModifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $depenseModification = $result->fetch_assoc();
    }
}

/* =====================================================
   RECHERCHE
   ===================================================== */
$recherche = trim($_GET["recherche"] ?? "");

if ($recherche !== "") {
    $sql = "SELECT d.*, u.nom AS nom_utilisateur FROM depense d LEFT JOIN utilisateur u ON d.id_utilisateur = u.id_utilisateur WHERE d.description LIKE ? OR d.unite LIKE ? ORDER BY d.id_depense DESC";
    $stmt = $connexion->prepare($sql);
    $mot = "%" . $recherche . "%";
    $stmt->bind_param("ss", $mot, $mot);
    $stmt->execute();
    $depenses = $stmt->get_result();
} else {
    $sql = "SELECT d.*, u.nom AS nom_utilisateur FROM depense d LEFT JOIN utilisateur u ON d.id_utilisateur = u.id_utilisateur ORDER BY d.id_depense DESC";
    $depenses = $connexion->query($sql);
}

/* =====================================================
   STATISTIQUES
   ===================================================== */
$sql = "SELECT COUNT(*) AS total FROM depense";
$result = $connexion->query($sql);
$data = $result->fetch_assoc();
$totalDepenses = $data["total"];

$sql = "SELECT COALESCE(SUM(montant), 0) AS total FROM depense";
$result = $connexion->query($sql);
$data = $result->fetch_assoc();
$totalMontant = $data["total"];

$sql = "SELECT COALESCE(SUM(montant), 0) AS total FROM depense WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
$result = $connexion->query($sql);
$data = $result->fetch_assoc();
$depensesMois = $data["total"];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dépenses - Café Resto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<aside class="sidebar">
    <div class="logo">
        ☕ <span>Café Resto</span>
    </div>
    <nav class="menu">
        <a href="dashboard.php">🏠 <span>Dashboard</span></a>
        <a href="produits/produits.php">📦 <span>Produits</span></a>
        <a href="commandes/commandes.php">📝 <span>Commandes</span></a>
        <a href="employes/employes.php">👨‍🍳 <span>Employés</span></a>
        <a href="clients/clients.php">👥 <span>Clients</span></a>
        <a href="paiements/paiements.php">💳 <span>Paiements</span></a>
        <a href="depenses/depense.php" class="active">💰 <span>Dépenses</span></a>
        <a href="factures.php">🧾 <span>Factures</span></a>
        <a href="rapports.php">📊 <span>Rapports</span></a>
    </nav>
    <a href="deconnexion.php" class="logout">🚪 <span>Déconnexion</span></a>
</aside>

<main class="main-content">

    <div class="topbar">
        <span class="menu-icon">☰</span>
        <span class="admin">👤 Administrateur ▾</span>
    </div>

    <div class="content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestion des dépenses</h2>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ajoutModal">
                <i class="bi bi-plus-circle"></i> Ajouter une dépense
            </button>
        </div>

        <?php if ($message !== "") { ?>
            <div class="alert alert-<?php echo $typeMessage; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <form method="GET" action="depense.php" class="d-flex gap-2 mb-4">
            <input type="text" name="recherche" class="form-control" placeholder="Rechercher une dépense..." value="<?php echo htmlspecialchars($recherche); ?>">
            <button type="submit" class="btn btn-success"><i class="bi bi-search"></i> Rechercher</button>
            <?php if ($recherche !== "") { ?>
                <a href="depense.php" class="btn btn-secondary">Réinitialiser</a>
            <?php } ?>
        </form>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-primary shadow">
                    <div class="card-body">
                        <h5>Nombre de dépenses</h5>
                        <h2><?php echo $totalDepenses; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-danger shadow">
                    <div class="card-body">
                        <h5>Montant total</h5>
                        <h2><?php echo number_format($totalMontant, 0, ",", " "); ?> FCFA</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-warning shadow">
                    <div class="card-body">
                        <h5>Dépenses du mois</h5>
                        <h2><?php echo number_format($depensesMois, 0, ",", " "); ?> FCFA</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Liste des dépenses</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Description</th>
                                <th>Quantité</th>
                                <th>Unité</th>
                                <th>Montant</th>
                                <th>Date</th>
                                <th>Utilisateur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($depenses && $depenses->num_rows > 0) { ?>
                            <?php while ($depense = $depenses->fetch_assoc()) { ?>
                                <tr>
                                    <td>#<?php echo $depense["id_depense"]; ?></td>
                                    <td><?php echo htmlspecialchars($depense["description"]); ?></td>
                                    <td><?php echo $depense["quantite"]; ?></td>
                                    <td><?php echo htmlspecialchars($depense["unite"]); ?></td>
                                    <td><strong><?php echo number_format($depense["montant"], 0, ",", " "); ?> FCFA</strong></td>
                                    <td><?php echo htmlspecialchars($depense["date"]); ?></td>
                                    <td><?php echo htmlspecialchars($depense["nom_utilisateur"] ?? "Inconnu"); ?></td>
                                    <td>
                                        <a href="depense.php?modifier=<?php echo $depense['id_depense']; ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="depense.php" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cette dépense ?');">
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="id_depense" value="<?php echo $depense['id_depense']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" class="text-center">Aucune dépense enregistrée.</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- MODAL AJOUT -->
<div class="modal fade" id="ajoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une dépense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="depense.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="ajouter">

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="Exemple : Achat de sacs de riz" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantité</label>
                        <input type="number" name="quantite" class="form-control" min="1" placeholder="Exemple : 2" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Unité</label>
                        <select name="unite" class="form-control" required>
                            <option value="">Choisir une unité</option>
                            <option value="sac">Sac</option>
                            <option value="kg">Kg</option>
                            <option value="litre">Litre</option>
                            <option value="carton">Carton</option>
                            <option value="unité">Unité</option>
                            <option value="paquet">Paquet</option>
                            <option value="panier">Panier</option>
                            <option value="palette">Palette</option>
                            <option value="boite">Boîte</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Montant total</label>
                        <input type="number" name="montant" class="form-control" min="1" placeholder="Exemple : 50000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL MODIFICATION -->
<?php if ($depenseModification) { ?>
<div class="modal fade show" id="modifierModal" tabindex="-1" style="display:block; background:rgba(0,0,0,.5);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la dépense</h5>
                <a href="depense.php" class="btn-close"></a>
            </div>
            <form method="POST" action="depense.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="modifier">
                    <input type="hidden" name="id_depense" value="<?php echo $depenseModification['id_depense']; ?>">

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($depenseModification['description']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantité</label>
                        <input type="number" name="quantite" class="form-control" min="1" value="<?php echo $depenseModification['quantite']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Unité</label>
                        <select name="unite" class="form-control" required>
                            <option value="sac" <?php if ($depenseModification['unite'] === 'sac') echo 'selected'; ?>>Sac</option>
                            <option value="kg" <?php if ($depenseModification['unite'] === 'kg') echo 'selected'; ?>>Kg</option>
                            <option value="litre" <?php if ($depenseModification['unite'] === 'litre') echo 'selected'; ?>>Litre</option>
                            <option value="carton" <?php if ($depenseModification['unite'] === 'carton') echo 'selected'; ?>>Carton</option>
                            <option value="unité" <?php if ($depenseModification['unite'] === 'unité') echo 'selected'; ?>>Unité</option>
                            <option value="paquet" <?php if ($depenseModification['unite'] === 'paquet') echo 'selected'; ?>>Paquet</option>
                            <option value="panier" <?php if ($depenseModification['unite'] === 'panier') echo 'selected'; ?>>Panier</option>
                            <option value="palette" <?php if ($depenseModification['unite'] === 'palette') echo 'selected'; ?>>Palette</option>
                            <option value="boite" <?php if ($depenseModification['unite'] === 'boite') echo 'selected'; ?>>Boîte</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Montant</label>
                        <input type="number" name="montant" class="form-control" min="1" value="<?php echo $depenseModification['montant']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="<?php echo $depenseModification['date']; ?>" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="depense.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>