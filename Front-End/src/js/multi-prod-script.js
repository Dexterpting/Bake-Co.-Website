const categorySelect = document.getElementById('categorySelect');
const cards = document.querySelectorAll('.card[data-category]');

categorySelect.addEventListener('change', () => {
  const selected = categorySelect.value;

  cards.forEach(card => {
    if (selected === 'all' || card.dataset.category === selected) {
      card.classList.remove('hidden');
    } else {
      card.classList.add('hidden');
    }
  });
});