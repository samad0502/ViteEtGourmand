<?php
session_start();
require_once '../config/load_env.php';
require_once '../classes/database.php';
require_once '../includes/auth_guard.php';

// Sécurité : Seuls les employés et admins peuvent ajouter des menus
$role = strtolower($_SESSION['user']['role'] ?? $_SESSION['user']['role_name'] ?? '');
if ($role !== 'employee' && $role !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white p-3">
                    <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Ajouter un nouveau Menu</h4>
                </div>
                <div class="card-body p-4">
                    <form action="../ajax/process_add_menu.php" method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom du menu</label>
                            <input type="text" name="title" class="form-control" placeholder="Ex: Buffet Campagnard" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Prix (€)</label>
                                <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Minimum de personnes</label>
                                <input type="number" name="min_people" class="form-control" value="1" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Détaillez le contenu du menu..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Image du plat</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                            <div class="form-text">Format recommandé : JPG ou PNG (max 2Mo).</div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Créer le menu</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>