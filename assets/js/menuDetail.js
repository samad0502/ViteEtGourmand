/**
 * menuDetail.js - Version Optimisée
 * Gère la promo dynamique et l'ajout au panier
 */

document.addEventListener('DOMContentLoaded', () => {
    const inputPeople = document.getElementById('number_people');
    const minPeopleSpan = document.getElementById('minPeople');
    const promoMsg = document.getElementById('promo-message');
    const orderForm = document.getElementById('orderForm');

    // Sécurité : on vérifie que les éléments existent
    if (!inputPeople || !minPeopleSpan) return;

    const minPeople = parseInt(minPeopleSpan.innerText);
    const threshold = minPeople + 5;


       // LOGIQUE DE PROMO DYNAMIQUE

    function updatePromoMessage() {
    const current = parseInt(inputPeople.value);
    const stockRestant = parseInt(inputPeople.dataset.stock); 
    const msgBox = document.getElementById('orderMessage'); 
    const orderBtn = document.getElementById('orderBtn');

    promoMsg.classList.remove('d-none');
    
    // VERIFICATION DU STOCK
    if (current > stockRestant) {
        promoMsg.className = "mt-2 small shadow-sm p-2 rounded alert-danger border-danger text-danger fw-bold";
        promoMsg.innerHTML = `<i class="bi bi-exclamation-octagon"></i> Stock insuffisant (Max: ${stockRestant})`;
        orderBtn.disabled = true; 
        return; 
    } else {
        orderBtn.disabled = false; 
    }

    // LOGIQUE PROMO 
    if (current >= threshold) {
        promoMsg.className = "mt-2 small shadow-sm p-2 rounded alert-success border-success text-success fw-bold";
        promoMsg.innerHTML = '<i class="bi bi-patch-check-fill"></i> Félicitations ! Vous bénéficiez de -10% sur ce menu.';
    } else {
        const missing = threshold - current;
        promoMsg.className = "mt-2 small shadow-sm p-2 rounded alert-info border-info text-info";
        promoMsg.innerHTML = `<i class="bi bi-info-circle"></i> Ajoutez encore <strong>${missing} personnes</strong> pour obtenir 10% de réduction !`;
    }
}

    inputPeople.addEventListener('input', updatePromoMessage);
    updatePromoMessage(); 


      //  GESTION DE L'AJOUT AU PANIER

    if (orderForm) {
        orderForm.addEventListener('submit', event => {
            event.preventDefault();

            const orderBtn = document.getElementById('orderBtn');
            const isLogged = orderBtn.dataset.logged === '1';

            // Si non connecté -> Afficher Modal
            if (!isLogged) {
                const modalEl = document.getElementById('loginModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
                return;
            }

            // Préparation des données
            const data = new FormData();
            data.append('menu_id', document.getElementById('menu_id').value);
            data.append('number_people', inputPeople.value);
            data.append('equipment_ready', document.getElementById('equipment_ready').value);

            fetch('ajax/add_to_cart.php', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(result => {
                const msgBox = document.getElementById('orderMessage');
                if (result.success) {
                    msgBox.innerHTML = '<div class="alert alert-success mt-3"> Ajouté au panier ! <a href="panier.php" class="fw-bold">Voir mon panier</a></div>';
                    orderBtn.disabled = true;
                    orderBtn.innerText = "Produit ajouté";
                } else {
                    msgBox.innerHTML = `<div class="alert alert-danger mt-3">${result.message || 'Erreur lors de l\'ajout'}</div>`;
                }
            })
            .catch(err => console.error("Erreur fetch:", err));
        });
    }


       // CONNEXION AJAX DANS LA MODAL 

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', e => {
            e.preventDefault();
            const data = new FormData(loginForm);
            // Si les inputs n'ont pas d'attribut 'name', on les ajoute manuellement 
            if(!data.has('email')) data.append('email', document.getElementById('loginEmail').value);
            if(!data.has('password')) data.append('password', document.getElementById('loginPassword').value);

            fetch('ajax/login.php', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(result => {
                if (!result.success) {
                    const errorBox = document.getElementById('loginError');
                    errorBox.classList.remove('d-none');
                    errorBox.innerText = result.message;
                } else {
                    // Succès : fermer modal et soumettre le panier
                    const modalEl = document.getElementById('loginModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                    
                    document.getElementById('orderBtn').dataset.logged = '1';
                    orderForm.dispatchEvent(new Event('submit'));
                }
            });
        });
    }
});