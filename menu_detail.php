<?php
session_start();
require_once 'classes/database.php';

$isLogged = isset($_SESSION['user']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: menus.php');
    exit;
}

$menuId = (int) $_GET['id'];
$database = new Database();
$pdo = $database->getConnection();

// Requête avec jointure pour le régime
$query = "SELECT m.*, d.name as diet_name 
        FROM menus m 
        LEFT JOIN diets d ON m.diet_id = d.id 
        WHERE m.id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$menuId]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    header('Location: menus.php');
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">
    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <?php
            // On récupère toutes les images (séparées par des virgules dans la BDD)
            $images = explode(',', $menu['image']);
            ?>
            <div id="menuCarousel" class="carousel slide shadow-sm rounded" data-bs-ride="carousel">
                <div class="carousel-inner rounded">
                    <?php foreach ($images as $index => $img): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="assets/img/menus/<?= trim($img) ?>" class="d-block w-100" style="height: 500px; object-fit: cover;" alt="Image menu">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($images) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#menuCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#menuCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 mt-3">
                <span class="badge bg-primary p-2"><i class="bi bi-egg-fried"></i> <?= htmlspecialchars($menu['diet_name'] ?? 'Classique') ?></span>
                <span class="badge <?= $menu['remaining_quantity'] > 0 ? 'bg-success' : 'bg-danger' ?> p-2">
                    <i class="bi bi-box-seam"></i> Stock : <?= $menu['remaining_quantity'] ?>
                </span>
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="display-6 fw-bold"><?= htmlspecialchars($menu['title']) ?></h1>
            <p class="h3 text-success mb-4"><?= number_format($menu['price'], 2) ?> € / pers.</p>

            <div class="mb-4">
                <h6>Description</h6>
                <p class="text-muted"><?= nl2br(htmlspecialchars($menu['description'] ?? 'Pas de description.')) ?></p>
            </div>

            <div class="card border-0 shadow-sm bg-light mb-4">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="fw-bold text-primary"><i class="bi bi-journal-text"></i> Composition du menu</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-12 d-flex align-items-start">
                            <div class="bg-primary-subtle text-primary rounded p-2 me-3">
                                <img src="/assets/img/salad.png" alt="entrée" width="30px">
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Entrée</h6>
                                <p class="text-muted small mb-0"><?= htmlspecialchars($menu['starter'] ?? 'Salade de saison et ses condiments') ?></p>
                            </div>
                        </div>

                        <hr class="my-2 opacity-25">

                        <div class="col-12 d-flex align-items-start">
                            <div class="bg-success-subtle text-success rounded p-2 me-3">
                                <img src="/assets/img/tray.png" alt="plat" width="30px">
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Plat de résistance</h6>
                                <p class="text-muted small mb-0"><?= htmlspecialchars($menu['main_course'] ?? 'Plat signature du chef et son accompagnement') ?></p>
                            </div>
                        </div>

                        <hr class="my-2 opacity-25">

                        <div class="col-12 d-flex align-items-start">
                            <div class="bg-warning-subtle text-warning rounded p-2 me-3">
                                <img src="/assets/img/gelato.png" alt="dessert" width="30px">
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Dessert</h6>
                                <p class="text-muted small mb-0"><?= htmlspecialchars($menu['dessert'] ?? 'Douceur sucrée et gourmandise') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning border-0 bg-light">
                <h6 class="fw-bold"><i class="bi bi-exclamation-triangle"></i> Allergènes</h6>
                <p class="mb-0 small">
                    <?= isset($menu['allergens']) ? htmlspecialchars($menu['allergens']) : 'Aucun allergène répertorié dans la base.' ?>
                </p>
            </div>

            <form id="orderForm" class="card p-4 shadow-sm border-0">
                <input type="hidden" id="menu_id" value="<?= $menuId ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre de convives (Min: <span id="minPeople"><?= $menu['min_people'] ?></span>)</label>
                    <input type="number" name="number_people" id="number_people" class="form-control"
                        value="<?= $menu['min_people'] ?>" min="<?= $menu['min_people'] ?>" data-stock="<?= $menu['remaining_quantity'] ?>">
                    <div id="promo-message" class="mt-2 small p-2 rounded d-none"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Matériel de service</label>
                    <select id="equipment_ready" class="form-select">
                        <option value="0">Livraison seule</option>
                        <option value="1">Avec prêt de matériel</option>
                    </select>
                </div>

                <div id="orderMessage" class="mt-2"></div>
                <button type="submit" class="btn btn-success btn-lg w-100 mt-2" id="orderBtn"
                    data-logged="<?= $isLogged ? '1' : '0' ?>"
                    <?= $menu['remaining_quantity'] <= 0 ? 'disabled' : '' ?>>
                    Ajouter au panier
                </button>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Connectez-vous pour commander</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="loginError" class="alert alert-danger d-none"></div>

                <form id="loginForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" id="loginEmail" class="form-control" placeholder="exemple@mail.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mot de passe</label>
                        <input type="password" id="loginPassword" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2">Se connecter</button>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted mb-0">Pas encore de compte ?</p>
                    <a href="auth/register.php" class="text-success fw-bold">Créer un compte ici</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/api.js"></script>
<script src="assets/js/menuDetail.js"></script>
<?php include 'includes/footer.php'; ?>