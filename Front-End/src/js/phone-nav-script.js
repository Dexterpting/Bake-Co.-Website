  const toggle  = document.getElementById('navToggle');
  const menu    = document.getElementById('navMenu');
  const overlay = document.getElementById('navOverlay');

  // Inject close button only once
  if (!document.getElementById('navClose')) {
    const closeItem = document.createElement('li');
    closeItem.classList.add('nav-close-item');
    closeItem.innerHTML = '<button id="navClose" aria-label="Close menu">&#10005;</button>';
    menu.prepend(closeItem);
  }

  function openMenu() {
    menu.classList.add('open');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    menu.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  toggle.addEventListener('click', openMenu);
  overlay.addEventListener('click', closeMenu);
  document.getElementById('navClose').addEventListener('click', closeMenu);