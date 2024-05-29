
$(document).ready(function () {
    $('.see-full-spec').click(function (e) {
        e.preventDefault(); // Empêche le lien de naviguer directement

        // Change l'onglet actif
        $('.product-tabs__item').removeClass('product-tabs__item--active');
        $('a[href="#product-tab-specification"]').parent().addClass('product-tabs__item--active');

        // Affiche le contenu de l'onglet de spécification
        $('.product-tabs__pane').removeClass('product-tabs__pane--active');
        $('#product-tab-specification').addClass('product-tabs__pane--active');

        // Anime le défilement jusqu'à la section
        $('html, body').animate({
            scrollTop: $("#product-tab-specification").offset().top - 100 // Ajustez -100 selon les besoins pour le décalage
        }, 600); // 600 ms pour l'animation de défilement
    });

    // Ajouter la fonction addToWishlist
    $('.product__actions-item--wishlist').click(function () {
        var pneuId = parseInt($(this).data('pneu-id'));
        if (!isNaN(pneuId)) {
            addToWishlist(pneuId);
        } else {
            console.error('Invalid pneuId:', pneuId);
        }
    });
});

// Fonction addToWishlist
function addToWishlist(pneuId) {
    // Convertir la chaîne pneuId en entier
    pneuId = parseInt(pneuId);
    // Vérifiez si l'identifiant du pneu est valide
    if (!isNaN(pneuId)) {
        // Afficher l'indicateur de chargement
        Swal.fire({
            title: 'Please wait',
            text: 'Adding the tire to your wishlist...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });
        $.ajax({
            type: "POST",
            url: wishlistAddUrl.replace('pneuId', pneuId),
            //url: "{{ path('wishlist_add', {'id': 'pneuId'}) }}".replace('pneuId', pneuId),
            success: function (response) {
                // Fermer l'indicateur de chargement
                Swal.close();

                // Vérifier si l'ajout a réussi
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The tire has been added to your wishlist.',
                    });

                    // Mettre à jour dynamiquement le compteur d'articles
                    $.ajax({
                        type: "GET",
                        url: '/wishlist/total',
                        success: function (response) {
                            if (response.totalItems !== undefined) {
                                $('.indicator__counter').text(response.totalItems);
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please log in to add the tire in wishlist',//cas erreur ajout
                    });
                    $.post('/store-redirect-url', {url: window.location.href}, function() {
                        window.location.href = '/login';
                    });
                }
            },
            error: function (xhr, status, error) {
                // Fermer l'indicateur de chargement
                Swal.close();

                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'This tire is already on your wishlist',
                });
            }
        });
    } else {
        console.error('Invalid pneuId:', pneuId);
    }
}

// wishlist remove
$(document).ready(function () {
    $('.wishlist__remove').click(function () {
        // Récupérer l'ID du pneu
        var pneuId = $(this).data('pneu-id');

        // Vérifier si l'ID du pneu est un nombre entier
        if (!isNaN(pneuId) && pneuId !== '') {
            // L'ID est valide, continuez avec la requête AJAX
            showAlert('confirm', "Are you sure you want to remove this item from your wishlist?", function (confirmed) {
                if (confirmed) {
                    // Afficher l'indicateur de chargement
                    Swal.fire({
                        title: 'Please wait',
                        text: 'Removing the tire from your wishlist...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: wishlistRemoveUrl.replace('pneuId', pneuId),
                        success: function (response) {
                            // Fermer l'indicateur de chargement
                            Swal.close();
                            if (response.success) {
                                // Afficher une alerte Sweet Alert pour indiquer le succès
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Tire removed from wishlist successfully.'
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                // Afficher une alerte Sweet Alert pour indiquer l'erreur
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error removing tire from wishlist: ' + response.error
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            // Fermer l'indicateur de chargement
                            Swal.close();
                            // Afficher une alerte Sweet Alert pour indiquer l'erreur
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while removing the tire from wishlist: ' + error
                            });
                        }
                    });
                }
            });
        } else {
            // Afficher une alerte si l'ID du pneu est invalide
            showAlert('error', "Invalid tire ID: " + pneuId);
        }
    });
});

// Fonction pour afficher Sweet Alert ou une alerte Bootstrap
function showAlert(type, message, confirmCallback, cancelCallback) {
    if (type === 'confirm') {
        // Afficher une alerte Sweet Alert de confirmation avec le message spécifié
        Swal.fire({
            icon: 'question',
            title: 'Confirmation',
            text: message,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) {
                if (confirmCallback) {
                    confirmCallback(true);
                }
            } else {
                if (cancelCallback) {
                    cancelCallback(false);
                }
            }
        });
    } else {
        // Afficher une alerte Sweet Alert avec le type spécifié
        Swal.fire({
            icon: type === 'success' ? 'success' : 'error',
            title: type === 'success' ? 'Success' : 'Error',
            text: message
        });
    }
}
