/**
 * Gère l’affichage du détail d’un menu
 */

// Récupération de l'id dans l'URL
const params = new URLSearchParams(window.location.search);
const menuId = params.get('id');

// Si aucun id → on ne fait rien
if (menuId) {

  fetchMenuDetail(menuId).then(menu => {

    const container = document.getElementById('menuDetail');

    container.innerHTML = `
      <div class="card">
        <img src="assets/images/menus/${menu.image}" class="card-img-top">
        <div class="card-body">
          <h3>${menu.titre}</h3>
          <p>${menu.description}</p>
          <p><strong>Prix :</strong> ${menu.prix} €</p>
          <p><strong>Personnes :</strong> ${menu.personnes}</p>
          <p><strong>Thème :</strong> ${menu.theme}</p>
          <p><strong>Régime :</strong> ${menu.regime}</p>
        </div>
      </div>
    `;
  });
}
