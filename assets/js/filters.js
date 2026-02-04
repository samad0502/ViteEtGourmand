/**
 * Gère les filtres
 */
function initFilters() {

  document.getElementById('btnFiltrer').addEventListener('click', () => {

    const filters = {
      prixMin: prixMin.value,
      prixMax: prixMax.value,
      fourchette: fourchette.value,
      personnesMin: personnesMin.value,
      theme: theme.value,
      regime: regime.value
    };

    // Recharge les menus avec filtres
    loadMenus(filters);
  });
}
