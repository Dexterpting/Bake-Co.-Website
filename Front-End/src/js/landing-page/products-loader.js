const BACKEND = 'https://bake-co.infinityfree.me/Back-End';
const ASSETS  = 'https://bake-co.infinityfree.me/Front-End/src/img';

const CATEGORY_LABELS = {
  pastries:     'Sweet Breads / Pastries',
  cheese:       'Cheese Cakes',
  moist:        'Moist Cakes',
  'custom-made':'Custom Made Order'
};

async function loadProducts() {
  try {
    const response = await fetch('/api/products');
    const products = await response.json();

    const container = document.getElementById('productsInner');
    const categorySelect = document.getElementById('categorySelect');
    const modalSelect = document.getElementById('modalProductSelect');

    if (products.length === 0) {
      container.innerHTML = '<p style="color:var(--text-muted);text-align:center;width:100%;padding:32px 0;">No products available yet.</p>';
      return;
    }

    // Build product cards
    container.innerHTML = products.map(p => `
      <div class="card" data-category="${p.category}">
        <h2>${escapeHtml(p.name)}</h2>
        <img src="${ASSETS}/${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}">
        <p>${escapeHtml(p.description)}</p>
        <p class="card-price">₱${parseFloat(p.price).toLocaleString('en-PH', {minimumFractionDigits:2})} <span class="card-unit">${escapeHtml(p.unit)}</span></p>
        <button onclick="openOrderModal('${escapeHtml(p.name)}')">Order Now</button>
      </div>
    `).join('');

    // Build modal select options grouped by category
    let currentCategory = '';
    let optionsHtml = '<option value="">Select a product</option>';
    products.forEach(p => {
      if (p.category !== currentCategory) {
        if (currentCategory !== '') optionsHtml += '</optgroup>';
        currentCategory = p.category;
        optionsHtml += `<optgroup label="${CATEGORY_LABELS[p.category] ?? p.category}">`;
      }
      optionsHtml += `<option value="${escapeHtml(p.name)}">${escapeHtml(p.name)}</option>`;
    });
    if (currentCategory !== '') optionsHtml += '</optgroup>';

    // Apply to all product selects on the page
    document.querySelectorAll('select[name="product[]"]').forEach(sel => {
      sel.innerHTML = optionsHtml;
    });

    // Re-init category filter
    initFilter();

  } catch (err) {
    console.error('Failed to load products:', err);
  }
}

function escapeHtml(str) {
  if (!str) return '';
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

loadProducts();