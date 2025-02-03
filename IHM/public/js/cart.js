function updateCartQuantity(productId, quantity) {
    $.ajax({
        url: '/IHM/api/cart.php',
        method: 'POST',
        data: { 
            action: 'update',
            productId: productId,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                // Refresh the cart content without page reload
                loadCartItems();
            }
        }
    });
}

function removeFromCart(productId) {
    if (confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
        $.ajax({
            url: '/IHM/api/cart.php',
            method: 'POST',
            data: { 
                action: 'remove',
                productId: productId
            },
            success: function(response) {
                if (response.success) {
                    // Refresh the cart content without page reload
                    loadCartItems();
                }
            }
        });
    }
}

// Add this new function to load and display cart items
function loadCartItems() {
    $.ajax({
        url: '/IHM/api/cart_items.php',
        method: 'GET',
        success: function(response) {
            if (response.items) {
                response.items.forEach(item => {
                    // Update quantity input for each item
                    $(`#quantity-${item.id}`).val(item.quantity);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading cart items:', error);
        }
    });
}

// Add this line to load cart items when the page loads
$(document).ready(function() {
    loadCartItems();
});
