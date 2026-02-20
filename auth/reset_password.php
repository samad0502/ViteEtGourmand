<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h3 class="fw-bold text-center mb-4">Nouveau mot de passe</h3>

                <?php if (isset($_GET['error']) && $_GET['error'] === 'match'): ?>
                    <div class="alert alert-danger py-2">
                        <i class="bi bi-exclamation-circle me-2"></i>Les mots de passe ne correspondent pas.
                    </div>
                <?php endif; ?>

                <form action="process_reset.php" method="POST">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

                    <div class="mb-3">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>