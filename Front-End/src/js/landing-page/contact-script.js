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