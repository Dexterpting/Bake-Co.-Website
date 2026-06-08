const form = document.querySelector('.contact-form');

  form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const btn = form.querySelector('button[type="submit"]');
  const originalText = btn.textContent;
  btn.textContent = 'Sending...';
  btn.disabled = true;

  const formData = new FormData(form);

  try {
    const response = await fetch('https://bake-co.infinityfree.me/Back-End/submit-order.php', {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      alert('Order submitted! We will contact you soon.');
      form.reset();
    } else {
      alert('Error: ' + data.message);
    }
  } catch (err) {
    alert('Something went wrong. Please try again.');
  } finally {
    btn.textContent = originalText;
    btn.disabled = false;
  }
});

// Add item button
document.getElementById('add-item-btn').addEventListener('click', () => {
  const container = document.getElementById('order-items');
  const firstItem = container.querySelector('.order-item');
  const newItem = firstItem.cloneNode(true);

  // Reset values
  newItem.querySelector('select').value = '';
  newItem.querySelector('input[type="number"]').value = 1;

  // Re-attach remove button
  newItem.querySelector('.remove-item-btn').onclick = function() {
    removeItem(this);
  };

  container.appendChild(newItem);
});

function removeItem(btn) {
  const items = document.querySelectorAll('.order-item');
  if (items.length > 1) {
    btn.parentElement.remove();
  } else {
    alert('At least one product is required.');
  }
}