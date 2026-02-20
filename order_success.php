<?php
session_start();
require_once 'includes/auth_guard.php';
include 'includes/header.php';
include 'includes/navbar.php';

// On récupère le numéro de commande passé dans l'URL
$orderRef = $_GET['order_ref'] ?? 'N/A';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow border-0 p-5 rounded-4">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>

                <h2 class="fw-bold mb-3">Commande validée !</h2>
                <p class="lead text-muted">Merci pour votre confiance. Votre festin est entre de bonnes mains.</p>

                <div class="bg-light p-3 rounded-3 my-4">
                    <span class="text-uppercase small fw-bold text-muted d-block mb-1">Numéro de commande</span>
                    <span class="h4 fw-bold text-primary"><?= htmlspecialchars($orderRef) ?></span>
                </div>

                <p class="small text-muted mb-4">
                    Un récapitulatif vient d'être envoyé à votre adresse email.<br>
                    Nos équipes préparent actuellement vos menus avec des produits frais.
                </p>

                <hr class="my-4 opacity-25">

                <div class="d-grid d-sm-flex justify-content-center gap-3">
                    <a href="index.php" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-house me-2"></i>Accueil
                    </a>
                    <a href="orders.php" class="btn btn-success px-4 shadow-sm">
                        <i class="bi bi-box-seam me-2"></i>Mes commandes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>