<?php
session_start();

require_once '../classes/database.php';

//  PROTECTION SÉCURISÉE (Vérifie role OU role_name)
$user = $_SESSION['user'] ?? null;
$role = strtolower($user['role'] ?? $user['role_name'] ?? '');

if (!$user || $role !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$db = (new Database())->getConnection();

//  Statistiques 
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalEmployees = $db->query("SELECT COUNT(*) FROM users WHERE role_id = (SELECT id FROM roles WHERE name = 'employee')")->fetchColumn();

// Récupération des commandes en cours 
$queryOrders = "SELECT o.*, u.firstname, m.title as menu_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                JOIN menus m ON o.menu_id = m.id 
                WHERE o.order_status NOT IN ('finished', 'cancelled')
                ORDER BY o.id DESC";

$orders = $db->query($queryOrders)->fetchAll(PDO::FETCH_ASSOC);

function getStatusColor($status)
{
    return match ($status) {
        'pending'          => 'warning text-dark',
        'accepted'         => 'info text-white',
        'preparing'        => 'primary',
        'shipping'         => 'info',
        'delivered'        => 'success',
        'waiting_material' => 'danger',
        default            => 'secondary',
    };
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold"><i class="bi bi-shield-lock me-2 text-danger"></i>Espace Administrateur</h2>
            <p class="text-muted">Bienvenue José. Gérez ici l'équipe et les flux de commandes.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-center p-4">
                <div class="display-6 text-primary mb-3"><i class="bi bi-people"></i></div>
                <h5 class="card-title">Équipe</h5>
                <p class="small text-muted"><?= $totalEmployees ?> employé(s) actif(s)</p>
                <a href="admin_users.php" class="btn btn-primary w-100 mt-auto">Gérer l'équipe</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-white bg-dark text-center p-4">
                <div class="display-6 mb-3"><i class="bi bi-clock-history"></i></div>
                <h5 class="card-title">Historique</h5>
                <p class="small opacity-75"><?= $totalOrders ?> commandes totales</p>
                <a href="../employee/employee_dashboard.php" class="btn btn-outline-light w-100 mt-auto">Voir tout</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm border-start border-success border-4 text-center p-4">
                <div class="display-6 text-success mb-3"><i class="bi bi-bar-chart-line"></i></div>
                <h5 class="card-title">Statistiques</h5>
                <p class="small text-muted">Analyses NoSQL (MongoDB)</p>
                <a href="admin_stats.php" class="btn btn-success w-100 mt-auto">Consulter</a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-0">
            <h4 class="fw-bold mb-0"><i class="bi bi-lightning-charge text-warning me-2"></i>Commandes en attente</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N°</th>
                        <th>Client</th>
                        <th>Menu</th>
                        <th>Statut</th>
                        <th>Changement rapide</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Aucune commande active.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="fw-bold">#<?= $o['order_number'] ?></td>
                                <td><?= htmlspecialchars($o['firstname']) ?></td>
                                <td><?= htmlspecialchars($o['menu_name']) ?></td>
                                <td>
                                    <span class="badge bg-<?= getStatusColor($o['order_status']) ?>">
                                        <?= htmlspecialchars($o['order_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="../ajax/update_status_complex.php" method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <select name="new_status" class="form-select form-select-sm">
                                            <option value="accepted">Accepter</option>
                                            <option value="preparing">En cuisine</option>
                                            <option value="shipping">En livraison</option>
                                            <option value="delivered">Livrée</option>
                                            <option value="finished">Terminée</option>
                                        </select>
                                        <button class="btn btn-sm btn-primary">Valider</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>