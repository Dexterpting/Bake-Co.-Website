function openEditModal(id, name, description, category, price, unit) {
    document.getElementById('edit_id').value          = id;
    document.getElementById('edit_name').value        = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_price').value       = price;
    document.getElementById('edit_category').value    = category;
    document.getElementById('edit_unit').value        = unit;
    document.getElementById('editModal').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
    document.body.style.overflow = '';
  }

  document.getElementById('editModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('editModal')) closeEditModal();
  });