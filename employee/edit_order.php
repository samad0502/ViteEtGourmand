<?php
session_start();
require_once '../classes/database.php';
include '../includes/header.php';
include '../includes/navbar.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Sécurisation de l'ID (on s'assure que c'est un nombre)
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// On récupère la commande en vérifiant qu'elle appartient bien à l'utilisateur
$stmt = $db->prepare("SELECT o.*, m.title, m.price, m.min_people 
                    FROM orders o 
                    JOIN menus m ON o.menu_id = m.id 
                    WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$orderId, $_SESSION['user']['id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);


// Si la commande n'existe pas ou n'est plus en "pending", redirection immédiate
if (!$order || $order['order_status'] !== 'pending') {
    header('Location: ../orders.php?error=not_modifiable');
    exit;
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <i class="bi bi-info-circle-fill me-2"></i>
                Le prix total sera automatiquement mis à jour si vous modifiez le nombre de convives.
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark fw-bold py-3">
                    <i class="bi bi-pencil-square me-2"></i> Modifier ma commande #<?= htmlspecialchars($order['order_number']) ?>
                </div>
                <div class="card-body p-4">
                    <form action="../ajax/update_order_process.php" method="POST">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small uppercase">Menu choisi (Non modifiable)</label>
                            <input type="text" class="form-control bg-light border-0 fw-bold" value="<?= htmlspecialchars($order['title']) ?>" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre de convives</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-people"></i></span>
                                    <input type="number" name="number_people" class="form-control bg-light"
                                        value="<?= $order['number_people'] ?>" readonly>
                                </div>
                                <div class="form-text text-muted small">Ce champ n'est plus modifiable après validation.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Option Matériel</label>
                                <select name="equipment_ready" class="form-select">
                                    <option value="1" <?= $order['equipment_ready'] ? 'selected' : '' ?>>Avec prêt de matériel </option>
                                    <option value="0" <?= !$order['equipment_ready'] ? 'selected' : '' ?>>Livraison seule</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4 p-3 bg-light rounded border">
                            <label class="form-label fw-bold"><i class="bi bi-plus-circle me-1"></i> Suppléments gratuits (Max 3)</label>
                            <div class="row g-2">
                                <?php
                                $supps = ['Sauce Algérienne', 'Pain supplémentaire', 'Couverts jetables', 'Serviettes', 'Sauce Blanche', 'Oignons frits'];
                                foreach ($supps as $s): ?>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input supp-check" type="checkbox" name="supplements[]" value="<?= $s ?>" id="check_<?= md5($s) ?>">
                                            <label class="form-check-label small" for="check_<?= md5($s) ?>">
                                                <?= $s ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div id="supp-error" class="text-danger small mt-2" style="display:none;">
                                <i class="bi bi-exclamation-triangle"></i> Limite de 3 suppléments atteinte.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Adresse de livraison</label>
                            <textarea name="delivery_address" class="form-control" rows="2" required><?= htmlspecialchars($order['delivery_address']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date de livraison</label>
                                <input type="date" name="delivery_date" class="form-control" value="<?= $order['delivery_date'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Heure de livraison</label>
                                <input type="time" name="delivery_time" class="form-control" value="<?= $order['delivery_time'] ?>" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="../orders.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Retour</a>
                            <button type="submit" class="btn btn-warning px-5 text-dark fw-bold shadow-sm">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Logique de limitation des suppléments
    document.addEventListener('DOMContentLoaded', function() {
        const limit = 3;
        const checkboxes = document.querySelectorAll('.supp-check');
        const errorMsg = document.getElementById('supp-error');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.supp-check:checked').length;

                if (checkedCount > limit) {
                    this.checked = false;
                    errorMsg.style.display = 'block';
                    setTimeout(() => {
                        errorMsg.style.display = 'none';
                    }, 3000);
                }
            });
        });
    });
</script>

<?php include '../includes/footer.php'; ?>