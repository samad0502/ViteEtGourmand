<?php include '../includes/navbar.php';
include '../includes/header.php'; ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h3 class="fw-bold text-center mb-4">Mot de passe oublié</h3>
                <p class="small text-muted text-center">Entrez votre email pour recevoir un lien de réinitialisation.</p>
                <form action="process_forgot.php" method="POST">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="votre@email.com" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>