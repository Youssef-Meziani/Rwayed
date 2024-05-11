document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('submit', function (e) {
        if (e.target.matches('#add-to-cart-form')) {
            e.preventDefault();
            var xhr = new XMLHttpRequest();
            var formData = new FormData(e.target);

            // Afficher le loader
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we add the item to your cart.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            xhr.open('POST', e.target.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');  // Important pour Symfony pour détecter une requête AJAX

            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        Swal.hideLoading();
                        Swal.fire({  // Utilisation de SweetAlert
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                updateCartDisplay(response.totalItems, response.prixTotal, response.items, response.shippingCost, response.tax, response.total);
                            }
                        });
                    }
                } else {
                    Swal.fire({  // Affichage d'une erreur avec SweetAlert
                        title: 'Error!',
                        text: 'The request failed!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error('The request failed!', xhr.status, xhr.statusText);
                }
            };

            xhr.onerror = function () {
                Swal.fire({  // SweetAlert pour les erreurs de connexion
                    title: 'Error!',
                    text: 'The request encountered an error.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            };

            xhr.send(formData);
        }
    });

});

function updateMobileMenuCartCounter(newCount) {
    const cartCounterElement = document.querySelectorAll('.mobile-indicator__counter[data-counter-cart]');
    console.log(cartCounterElement);
    if (!cartCounterElement.length) {
        console.error('Mobile Indicator menu cart counter elements with data-item-cart not found');
        return;
    }
    cartCounterElement.forEach(element => {
        element.textContent = newCount;
    });
}
function updateMobileIndicatorCartCounter(newCount) {
    // Correct the selector by removing the incorrect space and ensuring it targets elements properly
    const cartCounterElements = document.querySelectorAll('.mobile-menu__indicator-counter[data-counter-cart]');
    console.log(cartCounterElements);
    if (!cartCounterElements.length) {
        console.error('Mobile menu cart counter elements with data-item-cart not found');
        return;
    }
    cartCounterElements.forEach(element => {
        element.textContent = newCount;
    });
}
function updateCartDisplay(totalItems, prixTotal, itemsObject,shippingCost, tax, total) {

    document.getElementById('cart-item-count').textContent = totalItems;
    // Formatage du prix total
    const formattedPrice = prixTotal.toFixed(2); // Assurez-vous que c'est en format décimal avec deux chiffres après la virgule
    const parts = formattedPrice.split('.'); // Divisez le prix en parties entière et décimale
    const priceHtml = `${parts[0]}<small>.${parts[1]}</small> DH`;

    updateMobileMenuCartCounter(totalItems); // Update the cart count in the mobile menu
    updateMobileIndicatorCartCounter(totalItems); // Update the cart count in the mobile indicator button

    const checkoutButton = document.querySelector('.btn-checkout-header');
    // Update the mobile menu cart item count
    const cartCounterElement_1 = document.querySelector('.mobile-menu__indicator-counter');
    const cartCounterElement_2 = document.querySelector('.mobile-indicator__counter');
    cartCounterElement_1.textContent = totalItems;
    cartCounterElement_2.textContent = totalItems;
    cartCounterElement_1.setAttribute('data-counter', totalItems); // Ensure the data attribute is also updated
    cartCounterElement_2.setAttribute('data-counter', totalItems); // Ensure the data attribute is also updated

    document.getElementById('cart-total-price').innerHTML = priceHtml; // Utilisation de innerHTML pour respecter le formatage

    const cartList = document.querySelector('.dropcart__list');
    cartList.innerHTML = '';  // Vide la liste actuelle

    // Convertir les itemsObject en tableau
    const items = Object.values(itemsObject);
    if (items && items.length > 0) {
        // Re-enable checkout button and coupon section if they exist
        if (checkoutButton) {
            checkoutButton.removeAttribute('disabled');
            checkoutButton.classList.remove('btn-disabled');
        }
        items.forEach(item => {
            const repairStatus = item.withRepair ? '<li>Repaired</li>' : '<li>No Repair</li>';
            const itemElement = `
                <li class="dropcart__item" id="cart-item-${item.id}">
                    <div class="dropcart__item-image image image--type--product">
                        <a class="image__body" href="">
                            <img class="image__tag" src="${item.image}" alt="">
                        </a>
                    </div>
                    <div class="dropcart__item-info">
                        <div class="dropcart__item-name">
                            <a href="">${item.marque}</a>
                        </div>
                        <ul class="dropcart__item-features">
                            ${repairStatus}
                        </ul>
                        <div class="dropcart__item-meta">
                            <div class="dropcart__item-quantity">${item.quantity}</div>
                            <div class="dropcart__item-price">${item.prix.toFixed(2)} DH</div>
                        </div>
                    </div>
                    <button type="button" class="dropcart__item-remove" data-id="${item.id}" data-repair="${ item.withRepair ? 'true' : 'false' }">
                        <svg width="10" height="10">
                            <path d="M8.8,8.8L8.8,8.8c-0.4,0.4-1,0.4-1.4,0L5,6.4L2.6,8.8c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L3.6,5L1.2,2.6
                                    c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4,1-0.4,1.4,0L5,3.6l2.4-2.4c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L6.4,5l2.4,2.4
                                    C9.2,7.8,9.2,8.4,8.8,8.8z"/>
                            </svg>
                    </button>
                </li>
                <li class="dropcart__divider" role="presentation"></li>
            `;
            cartList.insertAdjacentHTML('beforeend', itemElement); // Ajoute le nouvel élément à la liste
        });
        document.querySelector('.dropcart__totals [data-subtotal]').textContent = prixTotal.toFixed(2) + ' DH';
        document.querySelector('.dropcart__totals [data-shipping]').textContent = shippingCost.toFixed(2) + ' DH';
        document.querySelector('.dropcart__totals [data-tax]').textContent = tax.toFixed(2) + ' DH';
        document.querySelector('.dropcart__totals [data-total]').textContent = total.toFixed(2) + ' DH';
    } else {
        if (checkoutButton) {
            checkoutButton.setAttribute('disabled', 'disabled');
            checkoutButton.classList.add('btn-disabled');
        }
        // Panier est vide, donc définissez tout à 0 et affichez le message approprié
        document.getElementById('cart-total-price').innerHTML = '0<small>.00</small> DH';
        document.querySelector('.dropcart__totals [data-subtotal]').textContent = '0.00 DH';
        document.querySelector('.dropcart__totals [data-shipping]').textContent = '0.00 DH';
        document.querySelector('.dropcart__totals [data-tax]').textContent = '0.00 DH';
        document.querySelector('.dropcart__totals [data-total]').textContent = '0.00 DH';
        cartList.innerHTML = '<span class="dropcart__empty">Your cart is empty.</span>';
    }
}
function updateCartBody(totalItems, prixTotal, itemsObject, shippingCost, tax, total) {
    const itemCountElement = document.getElementById('cart-item-count');
    if (itemCountElement) {
        itemCountElement.textContent = totalItems;
    }

    const priceHtml = `${parseInt(prixTotal).toFixed(2)}<small>.${(prixTotal % 1).toFixed(2).substring(2)}</small> DH`;
    const totalPriceElement = document.getElementById('cart-total-price');
    if (totalPriceElement) {
        totalPriceElement.innerHTML = priceHtml;
    }

    const cartTable = document.querySelector('.cart-table__table');
    const cartTotalsTable = document.querySelector('.cart__totals-table');
    const checkoutButton = document.querySelector('.btn-checkout');
    const cartTableBody = document.querySelector('.cart-table__body');

    if (Object.keys(itemsObject).length === 0) {  // Check if the items object is empty
        if (cartTable) {
            cartTable.innerHTML = '<span class="dropcart__empty">Your cart is empty.</span>';
        }

        if (cartTotalsTable) {
            cartTotalsTable.querySelectorAll('[data-subtotal], [data-shipping], [data-tax], [data-total]').forEach(element => {
                element.textContent = '0.00 DH';
            });
        }

        if (checkoutButton) {
            checkoutButton.setAttribute('disabled', 'disabled');
            checkoutButton.classList.add('btn-disabled');
        }

        const couponForm = document.querySelector('.cart-table__coupon-form');
        if (couponForm) {
            couponForm.style.display = 'none';
        }
    } else {
        if (cartTableBody) {
            cartTableBody.innerHTML = '';  // Clear existing rows
            const items = Object.values(itemsObject);
            items.forEach(item => {
                const itemElementTable = `
                    <tr class="cart-table__row" id="cart-item-${item.id}-table">
                <td class="cart-table__column cart-table__column--image">
                    <div class="image image--type--product">
                        <a href="#" class="image__body">
                            <img class="image__tag" src="${item.image}" alt="">
                        </a>
                    </div>
                </td>
                <td class="cart-table__column cart-table__column--product">
                    <a href="#" class="cart-table__product-name">${item.marque}</a>
                    <ul class="cart-table__options">${item.withRepair ? '<li>Repaired</li>' : '<li>No Repair</li>'}</ul>
                </td>
                <td class="cart-table__column cart-table__column--price">${item.prix.toFixed(2)} DH</td>
                <td class="cart-table__column cart-table__column--quantity">${item.quantity}</td>
                <td class="cart-table__column cart-table__column--total">${(item.prix * item.quantity).toFixed(2)} DH</td>
                <td class="cart-table__column cart-table__column--remove">
                    <button type="button" class="dropcart__item-remove btn btn-sm btn-icon btn-muted" data-id="${item.id}" data-repair="${item.withRepair ? 'true' : 'false'}">
                        <svg width="12" height="12">
                            <path d="M8.8,8.8L8.8,8.8c-0.4,0.4-1,0.4-1.4,0L5,6.4L2.6,8.8c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L3.6,5L1.2,2.6
                                    c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4,1-0.4,1.4,0L5,3.6l2.4-2.4c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L6.4,5l2.4,2.4
                                    C9.2,7.8,9.2,8.4,8.8,8.8z"/>
                            </svg>
                    </button>
                </td>
            </tr>
                `;
                cartTableBody.insertAdjacentHTML('beforeend', itemElementTable);
            });

            // Update totals if the element exists
            document.querySelector('.cart__totals-table [data-subtotal]').textContent = prixTotal.toFixed(2) + ' DH';
            document.querySelector('.cart__totals-table [data-shipping]').textContent = shippingCost.toFixed(2) + ' DH';
            document.querySelector('.cart__totals-table [data-tax]').textContent = tax.toFixed(2) + ' DH';
            document.querySelector('.cart__totals-table [data-total]').textContent = total.toFixed(2) + ' DH';

            // Re-enable checkout button and coupon section if they exist
            if (checkoutButton) {
                checkoutButton.removeAttribute('disabled');
                checkoutButton.classList.remove('btn-disabled');
            }

            const couponForm = document.querySelector('.cart-table__coupon-form');
            if (couponForm) {
                couponForm.style.display = 'flex';
            }
        }
    }
}
document.addEventListener('DOMContentLoaded', function() {
    // Attach event listener to the body and delegate to the target button
    document.body.addEventListener('click', function(event) {
        // Check if the clicked element or any of its parents have the specific class
        if (event.target.matches('.product-card__addtocart-icon') || event.target.closest('.product-card__addtocart-icon')) {
            const productId = event.target.closest('.product-card__addtocart-icon').getAttribute('data-product-id');
            const quantityAvailable = parseInt(event.target.closest('.product-card__addtocart-icon').getAttribute('data-quantity'));
            if (quantityAvailable <= 0) {
                Swal.fire({
                    title: 'Unavailable',
                    text: 'This tire is currently unavailable for purchase.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else {
            openQuantityModal(productId,quantityAvailable);
            }
        }
    });
    function openQuantityModal(productId, quantityAvailable) {
        Swal.fire({
            title: 'Select Quantity and Repair Option',
            html: `
            <div style="display: flex; justify-content: space-around; align-items: center;">
                <div style="margin-right: 20px;">
                    <label for="swal-input-quantity" class="swal2-input-label">Quantity:</label>
                    <input id="swal-input-quantity" type="number" min="1" value="1" max="${quantityAvailable}" class="swal2-input" required>
                </div>
                <div>
                    <div class="swal2-input-label">Repair service:</div>
                    <label class="input-radio-label__item">
                        <input type="radio" name="repair" value="true" class="input-radio-label__input">
                        <span class="input-radio-label__title">Yes</span>
                    </label>
                    <label class="input-radio-label__item">
                        <input type="radio" name="repair" value="false" class="input-radio-label__input" checked>
                        <span class="input-radio-label__title">No</span>
                    </label>
                </div>
            </div>
        `,
            showCancelButton: true,
            focusConfirm: false,
            preConfirm: () => {
                const quantity = document.getElementById('swal-input-quantity').value;
                const repair = document.querySelector('input[name="repair"]:checked').value;
                if (quantity && quantity > 0) {
                    return {
                        quantity: quantity,
                        repair: repair === 'true'
                    };
                } else {
                    Swal.showValidationMessage('Please enter a valid quantity.');
                    return false;
                }
            },
            confirmButtonText: 'Add to Cart'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                addToCart(productId, result.value.quantity, result.value.repair);
            }
        });
    }


    function addToCart(productId, quantity, repair) {
        var xhr = new XMLHttpRequest();
        var formData = new FormData();
        formData.append('id', productId);
        formData.append('quantity', quantity);
        formData.append('repair', repair);

        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we add the item to your cart.',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        xhr.open('POST', '/addToCart', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onload = function() {
            Swal.hideLoading();
            if (xhr.status >= 200 && xhr.status < 300) {
                var response = JSON.parse(xhr.responseText);
                console.log(response);
                if (response.maxQuantity && quantity > response.maxQuantity) {
                    Swal.fire({
                        title: 'Exceeded Quantity',
                        text: `Only ${response.maxQuantity} items available in stock.`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
                else {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateCartDisplay(response.totalItems, response.prixTotal, response.items, response.shippingCost, response.tax, response.total);
                            updateCartBody(response.totalItems, response.prixTotal, response.items, response.shippingCost, response.tax, response.total);
                        }
                    });
                }
                // if (response.message) {
                //     Swal.hideLoading();
                //     Swal.fire({
                //         title: 'Success!',
                //         text: response.message,
                //         icon: 'success',
                //         confirmButtonText: 'OK'
                //     }).then((result) => {
                //         if (result.isConfirmed) {
                //             updateCartDisplay(response.totalItems, response.prixTotal, response.items, response.shippingCost, response.tax, response.total);
                //         }
                //     });
                // }
            } else {
                Swal.fire('Error!', 'Failed to add tire to cart', 'error');
            }
        };

        xhr.onerror = function() {
            Swal.fire('Error!', 'The request encountered a network error.', 'error');
        };

        xhr.send(formData);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(event) {
        if (event.target.matches('.dropcart__item-remove, .cart-table__remove') || event.target.closest('.dropcart__item-remove, .cart-table__remove')) {
            const button = event.target.closest('.dropcart__item-remove, .cart-table__remove');
            const id = button.getAttribute('data-id');
            const isRepair = button.getAttribute('data-repair') === 'true';

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    removeItemFromCart(id, isRepair);
                }
            });
        }
    });

    function removeItemFromCart(id, isRepair) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/removeLigne', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                Swal.fire(
                    'Deleted!',
                    'Your tire has been deleted.',
                    'success'
                );

                // Supprimer l'élément de la liste
                const itemElement = document.getElementById(`cart-item-${id}`);
                if (itemElement) {
                    itemElement.parentNode.removeChild(itemElement);
                }

                // Mettre à jour le total du panier et autres détails si la réponse inclut ces données
                const response = JSON.parse(xhr.responseText);
                updateCartDisplay(response.totalItems, response.prixTotal, response.items, response.shippingCost, response.tax, response.total);
                updateCartBody(response.totalItems, response.prixTotal, response.items, response.shippingCost, response.tax, response.total);
                // Actualisez l'affichage du panier ici
            } else {
                Swal.fire(
                    'Error!',
                    'Failed to delete the item.',
                    'error'
                );
            }
        };
        console.log(encodeURIComponent(id),!!isRepair);
        xhr.send(`id=${encodeURIComponent(id)}&repair=${(!!isRepair)}`);
    }
});

