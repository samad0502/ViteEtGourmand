<?php
session_start();
require_once '../includes/auth_guard.php';
require_once __DIR__ . '/../classes/database.php';
include '../includes/navbar.php';


$userRole = $_SESSION['user']['role_name'] ?? '';
if (!isset($_SESSION['user']) || ($userRole !== 'employee' && $userRole !== 'admin')) {
    header('Location: ../index.php?error=access_denied');
    exit;
}

// Fonction couleur por les statuts des commandes
function getStatusColor($status)
{
    return match ($status) {
        'pending'          => 'warning text-dark',
        'accepted'         => 'info text-white',
        'preparing'        => 'primary',
        'shipping'         => 'info',
        'delivered'        => 'success',
        'waiting_material' => 'danger',
        'finished'         => 'secondary',
        'cancelled'        => 'dark',
        default            => 'light text-dark',
    };
}

$database = new Database();
$db = $database->getConnection();

// Récupération des filtres pour les commandes
$statusFilter = $_GET['status'] ?? '';
$searchClient = $_GET['client'] ?? '';

$query = "SELECT o.*, u.firstname, u.lastname, u.phone, m.title
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN menus m ON o.menu_id = m.id WHERE 1=1";

$params = [];
if ($statusFilter) {
    $query .= " AND o.order_status = ?";
    $params[] = $statusFilter;
}
if ($searchClient) {
    $query .= " AND (u.lastname LIKE ? OR u.firstname LIKE ? OR o.order_number LIKE ?)";
    $searchParam = "%$searchClient%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}
$query .= " ORDER BY o.order_date DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Récupération des Menus
$menus = $db->query("SELECT * FROM menus ORDER BY title ASC")->fetchAll();

// Récupération des Horaires
$hours = $db->query("SELECT * FROM opening_hours ORDER BY id ASC")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<style>
    @keyframes pulse-info {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }

    .animate-pulse {
        animation: pulse-info 2s infinite ease-in-out;
    }
</style>

<div class="container-fluid my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-briefcase me-2"></i>Espace Employé</h2>
        <div class="badge bg-primary">Connecté : <?= $_SESSION['user']['firstname'] ?></div>
    </div>

    <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#orders-pane">Commandes</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#menus-pane">Menus & Plats</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hours-pane">Horaires</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews-pane">Avis</button>
        </li>
    </ul>

    <div class="tab-content" id="employeeTabsContent">

        <div class="tab-pane fade show active" id="orders-pane">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Gestion des Commandes</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" name="client" class="form-control" placeholder="Rechercher par numéro de commande" value="<?= htmlspecialchars($searchClient) ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>En attente</option>
                                <option value="finished" <?= $statusFilter == 'finished' ? 'selected' : '' ?>>Terminée</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>N°</th>
                                    <th>Client</th>
                                    <th>Menu</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $o): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $o['order_number'] ?></strong>
                                            <?php if (isset($o['is_modified_by_client']) && $o['is_modified_by_client'] == 1): ?>
                                                <span class="badge bg-info text-dark animate-pulse" style="font-size: 0.7rem;">
                                                    <i class="bi bi-pencil-fill"></i> MODIFIÉE
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $o['firstname'] ?> <?= strtoupper($o['lastname']) ?></td>
                                        <td><?= $o['title'] ?></td>
                                        <td><span class="badge bg-<?= getStatusColor($o['order_status']) ?>"><?= $o['order_status'] ?></span></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <form action="../ajax/update_status_complex.php" method="POST" class="d-flex gap-1">
                                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                                    <select name="new_status" class="form-select form-select-sm">
                                                        <option value="accepted">Accepter</option>
                                                        <option value="preparing">En cuisine</option>
                                                        <option value="shipping">En livraison</option>
                                                        <option value="delivered">Livrée (Prêt matériel)</option>
                                                        <option value="finished">Terminée (Vente directe / Matériel rendu)</option>
                                                        <option value="waiting_material">Relance matériel (En retard)</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-success">OK</button>
                                                </form>
                                                <button class="btn btn-sm btn-outline-danger" onclick="openCancelModal(<?= $o['id'] ?>, '<?= $o['order_number'] ?>')">Annuler</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="menus-pane">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between">
                    <h5 class="mb-0">Gestion de la Carte</h5>
                    <a href="add_menu.php" class="btn btn-sm btn-light">Ajouter un Menu</a>
                </div>
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Titre</th>
                                <th>Prix</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menus as $m): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($m['image'])): ?>
                                            <img src="../assets/img/menus/<?= htmlspecialchars($m['image']) ?>"
                                                alt="img"
                                                style="width: 50px; height: 50px; object-fit: cover;"
                                                class="rounded shadow-sm">
                                        <?php else: ?>
                                            <span class="text-muted small">Sans image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($m['title']) ?></td>
                                    <td><?= $m['price'] ?> €</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="edit_menu.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Éditer
                                            </a>
                                            <form action="../ajax/delete_menu.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce menu ?');" class="m-0">
                                                <input type="hidden" name="menu_id" value="<?= $m['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="hours-pane">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Gestion des Horaires</h5>
                </div>
                <div class="card-body">
                    <form action="../ajax/update_hours.php" method="POST">
                        <table class="table align-middle">
                            <?php foreach ($hours as $h): ?>
                                <tr>
                                    <td><strong><?= $h['day_name'] ?></strong></td>
                                    <td><input type="time" name="open[<?= $h['id'] ?>]" value="<?= $h['open_time'] ?>" class="form-control"></td>
                                    <td><input type="time" name="close[<?= $h['id'] ?>]" value="<?= $h['close_time'] ?>" class="form-control"></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="closed[<?= $h['id'] ?>]" value="1" <?= $h['is_closed'] ? 'checked' : '' ?>>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="reviews-pane">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Avis en attente</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Note</th>
                                <th>Commentaire</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmtRev = $db->query("SELECT r.*, u.firstname FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.is_published = 0");
                            while ($rev = $stmtRev->fetch()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($rev['firstname']) ?></td>
                                    <td><?= $rev['rating'] ?>/5</td>
                                    <td><?= htmlspecialchars($rev['comment']) ?></td>
                                    <td>
                                        <form action="../ajax/update_status_complex.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="validate_review">
                                            <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                            <button class="btn btn-success btn-sm">Valider</button>
                                        </form>
                                        <form action="../ajax/update_status_complex.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="refuse_review">
                                            <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                            <button class="btn btn-danger btn-sm">Refuser</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="../ajax/cancel_order_employee.php" method="POST" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Annulation Commande #<span id="cancelOrderNum"></span></h5>
            </div>
            <div class="modal-body">
                <input type="hidden" name="order_id" id="cancelOrderId">
                <div class="mb-3">
                    <label class="form-label">Mode de contact client</label>
                    <select name="contact_method" class="form-select" required>
                        <option value="GSM">Appel GSM</option>
                        <option value="Email">Email</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Motif de l'annulation</label>
                    <textarea name="reason" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="submit" class="btn btn-danger">Confirmer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModal(id, num) {
        document.getElementById('cancelOrderId').value = id;
        document.getElementById('cancelOrderNum').innerText = num;
        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var hash = window.location.hash;
        if (hash) {
            var triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (triggerEl) {
                bootstrap.Tab.getInstance(triggerEl) || new bootstrap.Tab(triggerEl).show();
            }
        }
    });
</script>

<?php include '../includes/footer.php'; ?>