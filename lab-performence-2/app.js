const cart = {};

function updateCartIcon() {
  const count = Object.values(cart).reduce((sum, item) => sum + item.qty, 0);
  document.querySelector('.fa-cart-plus sub').textContent = count;
}

function updateCartModal() {
  const cartList = document.getElementById('cart-list');
  cartList.innerHTML = '';
  let total = 0;
  Object.values(cart).forEach(item => {
    total += item.qty * item.price;
    const li = document.createElement('li');
    li.innerHTML = `
      <div style="display:flex;align-items:center;gap:10px;">
        <img src="${item.img}" alt="${item.name}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;">
        <span>${item.name} (${item.price} tk)</span>
      </div>
      <div class="cart-item-controls">
        <button onclick="changeQty('${item.name}', -1)">-</button>
        <span>${item.qty}</span>
        <button onclick="changeQty('${item.name}', 1)">+</button>
        <button class="delete" onclick="removeFromCart('${item.name}')">Delete</button>
      </div>
    `;
    cartList.appendChild(li);
  });
  document.getElementById('cart-total').textContent = 'Total: ' + total + ' tk';
}

function addToCart(name, price, img) {
  if (cart[name]) {
    cart[name].qty += 1;
  } else {
    cart[name] = { name, price, qty: 1, img };
  }
  updateCartIcon();
}

window.changeQty = function(name, delta) {
  if (cart[name]) {
    cart[name].qty += delta;
    if (cart[name].qty <= 0) delete cart[name];
    updateCartIcon();
    updateCartModal();
  }
};

window.removeFromCart = function(name) {
  delete cart[name];
  updateCartIcon();
  updateCartModal();
};

document.querySelectorAll('.product-card').forEach(card => {
  const main = parseFloat(card.getAttribute('data-main'));
  const discount = parseFloat(card.getAttribute('data-discount'));
  const discounted = (main * (1 - discount / 100)).toFixed(2);
  const priceElem = card.querySelector('.price');
  if (priceElem) {
    priceElem.textContent = `${discounted} tk`;
  }
});
// Attach event listeners to "Order Now" buttons
document.querySelectorAll('.product-card').forEach(card => {
  const name = card.querySelector('h3').textContent;
  const price = parseFloat(card.querySelector('.price').textContent);
  const img = card.querySelector('img').getAttribute('src');
  card.querySelector('button').addEventListener('click', () => {
    addToCart(name, price, img);
  });
});

// Cart modal logic
const cartModal = document.getElementById('cart-modal');
document.querySelector('.fa-cart-plus').addEventListener('click', () => {
  updateCartModal();
  cartModal.style.display = 'flex';
});
document.getElementById('close-cart').onclick = () => {
  cartModal.style.display = 'none';
};
window.onclick = function(event) {
  if (event.target === cartModal) cartModal.style.display = 'none';
};

// Place Order button logic
document.getElementById('place-order-btn').onclick = function() {
  if (Object.keys(cart).length === 0) {
    alert('Your cart is empty!');
    return;
  }
  alert('Order placed successfully!');
  // Optionally, clear the cart:
  for (let key in cart) delete cart[key];
  updateCartIcon();
  updateCartModal();
  cartModal.style.display = 'none';
};

// Initialize cart icon
updateCartIcon();