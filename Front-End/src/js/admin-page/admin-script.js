async function loadOrders() {
  const response = await fetch('/VA-Development/Lorena-Landing-Page/Landing-Page/Back-End/admin.php?api=1');
  const orders = await response.json();
  const tbody = document.getElementById('ordersBody');

  if (orders.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6">No orders yet.</td></tr>';
    return;
  }

  tbody.innerHTML = orders.map(row => `
    <tr>
      <td>${row.id}</td>
      <td>${escapeHtml(row.name)}</td>
      <td>${escapeHtml(row.phone)}</td>
      <td>${escapeHtml(row.address)}</td>
      <td>${escapeHtml(row.landmark)}</td>
      <td>${escapeHtml(row.order).replace(/\n/g, '<br>')}</td>
      <td>${row.submitted_at}</td>
      <td><a class="delete" href="?delete=${row.id}" data-id="${row.id}">Delete</a></td>
    </tr>
  `).join('');

  document.querySelector('.count').textContent = `Total: ${orders.length} order(s)`;
  attachDeleteListeners();
}

function escapeHtml(str) {
  if (!str) return '';
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function attachDeleteListeners() {
  document.querySelectorAll('.delete').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (confirm('Delete this order?')) {
        window.location.href = btn.href;
      }
    });
  });
}

loadOrders();