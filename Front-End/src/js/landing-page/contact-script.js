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

// Modal open/close
function openOrderModal(productName) {
  const modal = document.getElementById('orderModal');
  modal.classList.add('open');
  document.body.style.overflow = 'hidden';

  // Pre-select product if passed
  if (productName) {
    const select = document.getElementById('modalProductSelect');
    if (select) {
      for (let opt of select.options) {
        if (opt.value === productName) {
          opt.selected = true;
          break;
        }
      }
    }
  }
}

function closeOrderModal() {
  document.getElementById('orderModal').classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('modalClose').addEventListener('click', closeOrderModal);
document.getElementById('orderModal').addEventListener('click', (e) => {
  if (e.target === document.getElementById('orderModal')) closeOrderModal();
});

// Modal add item button
document.getElementById('modal-add-item-btn').addEventListener('click', () => {
  const container = document.getElementById('modal-order-items');
  const firstItem = container.querySelector('.order-item');
  const newItem = firstItem.cloneNode(true);
  newItem.querySelector('select').value = '';
  newItem.querySelector('input[type="number"]').value = 1;
  newItem.querySelector('.remove-item-btn').onclick = function() {
    removeModalItem(this);
  };
  container.appendChild(newItem);
});

function removeModalItem(btn) {
  const items = document.querySelectorAll('#modal-order-items .order-item');
  if (items.length > 1) {
    btn.parentElement.remove();
  } else {
    alert('At least one product is required.');
  }
}

// Handle modal form submit
const modalForm = document.querySelector('.modal-form');
if (modalForm) {
  modalForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = modalForm.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.textContent = 'Sending...';
    btn.disabled = true;

    const formData = new FormData(modalForm);

    try {
      const response = await fetch('https://bake-co.infinityfree.me/Back-End/submit-order.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();
      if (data.success) {
        alert('Order submitted! We will contact you soon.');
        modalForm.reset();
        closeOrderModal();
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
}