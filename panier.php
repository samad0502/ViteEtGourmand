<?php
session_start();
require_once 'classes/database.php';

$database = new Database();
$pdo = $database->getConnection();

$cart = $_SESSION['cart'] ?? [];
$totalGeneral = 0;

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">
    <h2 class="mb-4"><i class="bi bi-cart3"></i> Votre Panier</h2>

    <?php if (empty($cart)): ?>
        <div class="alert alert-info p-5 text-center">
            <h4>Votre panier est vide...</h4>
            <p>Découvrez nos menus gourmands pour commencer votre commande.</p>
            <a href="menus.php" class="btn btn-primary">Voir la carte</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Menu</th>
                                    <th>Convives</th>
                                    <th>Prix</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $index => $item):
                                    $stmt = $pdo->prepare("SELECT title, price, min_people, image FROM menus WHERE id = ?");
                                    $stmt->execute([$item['menu_id']]);
                                    $m = $stmt->fetch(PDO::FETCH_ASSOC);

                                    $nbPers = (int)$item['number_people'];
                                    $prixUnitaire = (float)$m['price'];
                                    $sousTotal = $prixUnitaire * $nbPers;

                                    // Application de la remise de 10%
                                    $isPromo = ($nbPers >= ($m['min_people'] + 5));
                                    if ($isPromo) {
                                        $sousTotal *= 0.9;
                                    }
                                    $totalGeneral += $sousTotal;
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="assets/img/menus/<?= explode(',', $m['image'])[0] ?>" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($m['title']) ?></h6>
                                                    <small class="text-muted"><?= $item['equipment_ready'] ? 'Avec matériel' : 'Livraison seule' ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <form action="ajax/update_cart.php" method="POST" class="d-flex align-items-center">
                                                <input type="hidden" name="index" value="<?= $index ?>">
                                                <input type="number" name="quantity" class="form-control form-control-sm"
                                                    value="<?= $nbPers ?>" min="<?= $m['min_people'] ?>" style="width: 70px;" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td>
                                            <span class="<?= $isPromo ? 'text-success fw-bold' : '' ?>">
                                                <?= number_format($sousTotal, 2) ?> €
                                            </span>
                                            <?php if ($isPromo): ?>
                                                <br><span class="badge bg-success-subtle text-success small">-10% inclus</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="ajax/remove_from_cart.php?index=<?= $index ?>" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <a href="menus.php" class="btn btn-link text-decoration-none mt-3">
                    <i class="bi bi-arrow-left"></i> Continuer mes achats
                </a>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Récapitulatif</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sous-total</span>
                            <span><?= number_format($totalGeneral, 2) ?> €</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Livraison</span>
                            <span class="text-muted small">Calculé à l'étape suivante</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5">Total</span>
                            <span class="h4 text-primary fw-bold"><?= number_format($totalGeneral, 2) ?> €</span>
                        </div>

                        <a href="checkout.php" class="btn btn-success btn-lg w-100 shadow-sm">
                            Passer à la livraison <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>