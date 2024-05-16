$(document).ready(function () {
    $('.reviews-view__form').on('submit', function (e) {
        e.preventDefault(); // Empêche la soumission normale du formulaire

        var formData = new FormData(this); // Créez un FormData avec les données du formulaire

        Swal.fire({
            title: 'Submitting...',
            text: 'Please wait while we submit your review.',
            allowOutsideClick: false,
            showConfirmButton: false, // Ne pas afficher le bouton "OK"
            onBeforeOpen: () => {
                Swal.showLoading(); // Affiche le chargement de SweetAlert
            }
        });

        $.ajax({
            url: $(this).attr('action'), // URL de soumission
            type: 'POST',
            data: formData,
            processData: false, // Empêche jQuery de transformer les données en chaîne de requête
            contentType: false, // Empêche jQuery d'ajouter un en-tête Content-Type
            success: function (response) {
                Swal.close(); // Ferme le SweetAlert de chargement
                // Utilisez la réponse du serveur pour le titre ou le texte
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: response.message // Utilisez le message de la réponse JSON
                }).then(function() {
                    location.reload(); // Recharger la page après le succès
                });
                $('#formAvis')[0].reset();
            },
            error: function (xhr) {
                Swal.close(); // Ferme le SweetAlert de chargement
                var response = JSON.parse(xhr.responseText);
                if (response.formErrors) {
                    var errorMessages = Object.values(response.formErrors).join("\n");
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur...',
                        text: errorMessages // Affiche les erreurs de validation du formulaire
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur...',
                        text: response.error // Utilise le message d'erreur générique si les erreurs spécifiques du formulaire ne sont pas fournies
                    });
                }
            }
        });
    });
});