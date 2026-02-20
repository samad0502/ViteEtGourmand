<?php
session_start();
require_once '../includes/auth_guard.php';
require_once __DIR__ . '/../classes/database.php';
include '../includes/header.php';
include '../includes/navbar.php';

// Vérification du rôle (Sécurité)
$userRole = $_SESSION['user']['role_name'] ?? '';
if ($userRole !== 'employee' && $userRole !== 'admin') {
    header('Location: ../index.php?error=access_denied');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Récupération du menu à modifier
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: employee_dashboard.php");
    exit;
}

$menuId = (int)$_GET['id'];
$stmt = $db->prepare("SELECT * FROM menus WHERE id = ?");
$stmt->execute([$menuId]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

// Si le menu n'existe pas en base
if (!$menu) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Menu introuvable.</div></div>";
    exit;
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Modifier le Menu</h4>
                </div>
                <div class="card-body p-4">
                    <form action="../ajax/update_menu.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom du menu</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($menu['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Prix (€)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?= $menu['price'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($menu['description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Image actuelle</label>
                            <div class="mb-2">
                                <?php if (!empty($menu['image'])): ?>
                                    <img src="../assets/img/menus/<?= htmlspecialchars($menu['image']) ?>" width="150" class="img-thumbnail shadow-sm">
                                <?php else: ?>
                                    <p class="text-muted small">Aucune image enregistrée.</p>
                                <?php endif; ?>
                            </div>
                            <label class="form-label text-muted small">Remplacer l'image (optionnel)</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="employee_dashboard.php#menus-pane" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary shadow-sm">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>