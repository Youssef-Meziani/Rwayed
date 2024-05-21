document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: 'Congratulations!',
        text: 'You have earned 5 loyalty points. Thank you for your purchase!',
        icon: 'success',
        confirmButtonText: 'Awesome!',
        customClass: {
            confirmButton: 'btn btn-primary',
        },
        buttonsStyling: false,
        imageUrl: '/images/gift.png', // Ensure the path to the image is correct
        imageWidth: 100,
        imageHeight: 100,
        imageAlt: 'Custom image',
    });
});
