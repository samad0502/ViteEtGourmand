<?php
session_start();

// Vérification de la session
if (!isset($_SESSION['user'])) {
    header('Location: auth/login.php');
    exit;
}

require_once 'classes/database.php';
require_once 'classes/Order.php';

include 'includes/header.php';
include 'includes/navbar.php';

$database = new Database();
$db = $database->getConnection();

// Fonction pour gérer les couleurs des badges selon les nouveaux statuts
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

$orderManager = new Order($db);
$orders = $orderManager->getByUser($_SESSION['user']['id']);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-box-seam me-2"></i>Mes commandes</h1>
        <a href="menus.php" class="btn btn-outline-primary btn-sm">Nouvelle commande</a>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php
            echo match ($_GET['error']) {
                'not_modifiable' => "Cette commande ne peut plus être modifiée car elle est déjà en cours de traitement.",
                'update_failed'  => "La mise à jour a échoué. Le statut a probablement changé entre-temps.",
                default          => "Une erreur est survenue lors de l'opération."
            };
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) || isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php
            if (isset($_GET['msg']) && $_GET['msg'] === 'updated') echo "Commande mise à jour avec succès.";
            elseif (isset($_GET['success']) && $_GET['success'] == 'review_posted') echo "<strong>Merci !</strong> Votre avis a été pris en compte.";
            else echo "<strong>Félicitations !</strong> Action effectuée avec succès.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($orders)) : ?>
        <div class="alert alert-info py-5 text-center shadow-sm">
            <i class="bi bi-emoji-smile fs-1 d-block mb-3"></i>
            <p class="mb-0">Vous n'avez pas encore passé de commande. Nos chefs n'attendent que vous !</p>
        </div>
    <?php else : ?>
        <?php foreach ($orders as $o) : ?>
            <div class="card mb-4 shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <span class="text-muted small">Commande #<?= htmlspecialchars($o['order_number']) ?></span>
                        <h5 class="mb-0 text-primary fw-bold"><?= htmlspecialchars($o['title']) ?></h5>
                    </div>
                    <span class="badge rounded-pill px-3 py-2 bg-<?= getStatusColor($o['order_status']) ?>">
                        <?= ucfirst(str_replace('_', ' ', $o['order_status'])) ?>
                    </span>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><i class="bi bi-calendar-event me-2"></i>Date : <?= date('d/m/Y', strtotime($o['order_date'] ?? $o['created_at'])) ?></p>
                            <p class="mb-1"><i class="bi bi-people me-2"></i>Convives : <?= $o['number_people'] ?> personnes</p>
                            <p class="mb-1"><i class="bi bi-geo-alt me-2"></i>Livraison : <?= htmlspecialchars($o['delivery_address'] ?? 'Adresse non spécifiée') ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="h4 text-success mb-0 fw-bold"><?= number_format($o['total_price'], 2) ?> €</p>
                            <small class="text-muted">Total réglé</small>
                        </div>
                    </div>

                    <hr class="opacity-25">

                    <div class="d-flex flex-wrap gap-2 mb-3">

                        <?php if ($o['order_status'] === 'pending'): ?>
                            <a href="employee/edit_order.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil me-1"></i> Modifier
                            </a>
                            <button onclick="confirmCancel(<?= $o['id'] ?>)" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle me-1"></i> Annuler
                            </button>

                        <?php elseif ($o['order_status'] === 'finished' && empty($o['rating'])): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAvis<?= $o['id'] ?>">
                                <i class="bi bi-star-fill me-1"></i> Donner mon avis
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if ($o['order_status'] !== 'pending' && $o['order_status'] !== 'cancelled'): ?>
                        <div class="bg-light p-3 rounded border">
                            <h6 class="fw-bold mb-2 small"><i class="bi bi-clock-history me-2"></i>Suivi de la commande</h6>
                            <div class="timeline-small small">
                                <?php
                                $stmtHist = $db->prepare("SELECT * FROM order_history WHERE order_id = ? ORDER BY changed_at DESC");
                                $stmtHist->execute([$o['id']]);
                                $history = $stmtHist->fetchAll();

                                foreach ($history as $h): ?>
                                    <div class="mb-1 border-start ps-3 position-relative">
                                        <span class="text-muted" style="font-size: 0.8rem;"><?= date('d/m H:i', strtotime($h['changed_at'])) ?></span> —
                                        <strong><?= ucfirst(str_replace('_', ' ', $h['status'])) ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($o['order_status'] === 'waiting_material'): ?>
                        <div class="mt-3 p-3 bg-danger bg-opacity-10 rounded border-start border-danger border-4">
                            <span class="d-block fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Rappel Important :</span>
                            <p class="mb-0 small text-dark">Veuillez nous contacter pour restituer le matériel. Sans retour sous 10 jours, des frais de 600€ seront appliqués.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($o['order_status'] === 'finished' && empty($o['rating'])): ?>
                <div class="modal fade" id="modalAvis<?= $o['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="ajax/save_review.php" method="POST" class="modal-content border-0 shadow">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Votre avis nous aide !</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Note</label>
                                    <select name="rating" class="form-select" required>
                                        <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                                        <option value="4">⭐⭐⭐⭐ (Très bon)</option>
                                        <option value="3">⭐⭐⭐ (Correct)</option>
                                        <option value="2">⭐⭐ (Décevant)</option>
                                        <option value="1">⭐ (Mauvais)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Commentaire</label>
                                    <textarea name="comment" class="form-control" rows="3" required placeholder="Avez-vous aimé la cuisine de nos chefs ?"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success w-100 fw-bold">Envoyer mon avis</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    function confirmCancel(orderId) {
        if (confirm("Êtes-vous sûr de vouloir annuler cette commande ?\nLe stock sera automatiquement remis à jour.")) {
            window.location.href = "ajax/cancel_order.php?id=" + orderId;
        }
    }
</script>

<?php include 'includes/footer.php'; ?>