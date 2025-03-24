

let cart = JSON.parse(localStorage.getItem("cart")) || []

function updateCart() {
    const cartCount = document.getElementById("cart-count")

    cartCount.textContent = cart.length
}
updateCart();

// founction pour afficher la liste des produits dans le panier
function displayCart() {
    const cartItemsContainer = document.getElementById("cart-items");
    //    Vider le conteneur avant d'afficher les articles
    cartItemsContainer.innerHTML = "";
    let total = 0;
    cart.forEach((item, index) => {
        const cartItem = document.createElement("div");
        cartItem.classList.add("card-item");
        cartItem.innerHTML = `
         <div>
            <h3>${item.name} <img src="../uploads/${item.image}" width="40px" style="border-radius: 5px; margin-bottom:-10px" /> </h3>
            <p>Prix: €${item.price.toFixed(2)}</p>
            <div class="quantity-controls">
                <button onclick="updateQuantity(${item.id},-1)">-</button>
                <input type="text" value="${item.quantity}" readonly />
                <button onclick="updateQuantity(${item.id},1)">+</button>
            </div>
        </div>
        <button onclick="removeItemFromCart(${item.id})">Retirer</button>
        
        `
        cartItemsContainer.appendChild(cartItem);
        total +=item.price * item.quantity
    })
    document.getElementById("total-price").textContent = `Total: €${total.toFixed(2)}`
}

displayCart()

// Fonction pour changer la quantité de l'article
function updateQuantity(productId, change) {
    const cartItem = cart.find(item => item.id === productId);
    if (cartItem) {
        // modifier la quantité
        cartItem.quantity += change;
        
        // si la quantité est inférieur à 0 , je retire l'article du panier
        if (cartItem.quantity <= 0) {
            removeItemFromCart(cartItem.id)
        }else{
            localStorage.setItem("cart", JSON.stringify(cart))
            displayCart()
        }
    }
}

// Fonction pour retirer l'article du panier
function removeItemFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem("cart", JSON.stringify(cart));
    displayCart();
    updateCart()
}