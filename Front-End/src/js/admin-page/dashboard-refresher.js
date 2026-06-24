let latestOrderId = null;
let pollInterval  = null;

// Get initial latest order ID on page load
async function initPolling() {
  try {
    const res  = await fetch('/Back-End/admin.php?latest_order=1');
    const data = await res.json();
    latestOrderId = data.latest_id;
  } catch (e) {
    console.error('Failed to get latest order ID:', e);
  }

  // Poll every 30 seconds
  pollInterval = setInterval(checkNewOrders, 30000);
}

async function checkNewOrders() {
  try {
    const res  = await fetch('/Back-End/admin.php?latest_order=1');
    const data = await res.json();

    if (latestOrderId !== null && data.latest_id > latestOrderId) {
      latestOrderId = data.latest_id;
      showBanner();
    }
  } catch (e) {
    console.error('Polling error:', e);
  }
}

function showBanner() {
  document.getElementById('newOrderBanner').style.display = 'block';
  // Also push browser notification if permission granted
  if (Notification.permission === 'granted') {
    new Notification('Bake & Co. — New Order!', {
      body:  'A new order has been placed.',
      icon:  '/Front-End/src/img/picture1.jpg'
    });
  }
}

function dismissBanner() {
  document.getElementById('newOrderBanner').style.display = 'none';
}

function refreshDashboard() {
  window.location.reload();
}

// Request browser notification permission on page load
if (Notification.permission === 'default') {
  Notification.requestPermission();
}

initPolling();