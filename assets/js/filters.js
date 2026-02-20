/**
 * filters.js
 * Gère les filtres asynchrones des menus
 */

document.addEventListener('DOMContentLoaded', () => {


    // Chargement thèmes et régimes

  fetchFilters().then(data => {

    const themeSelect = document.getElementById('theme');
    const dietSelect  = document.getElementById('diet');

    themeSelect.innerHTML = '<option value="">Tous les thèmes</option>'; 
    data.themes.forEach(theme => {
      const option = document.createElement('option');
      option.value = theme.id;
      option.textContent = theme.name;
      themeSelect.appendChild(option);
    });

    dietSelect.innerHTML  = '<option value="">Tous les régimes</option>'; 
    data.diets.forEach(diet => {
      const option = document.createElement('option');
      option.value = diet.id;
      option.textContent = diet.name;
      dietSelect.appendChild(option);
    });
  });


    // Soumission des filtres

  document
    .getElementById('filtersForm')
    .addEventListener('submit', event => {

      event.preventDefault();

      const filters = {
        priceMin:  document.getElementById('priceMin').value,
        priceMax:  document.getElementById('priceMax').value,
        minPeople: document.getElementById('minPeople').value,
        theme:     document.getElementById('theme').value,
        diet:      document.getElementById('diet').value
      };

      loadMenus(filters);
    });
});
