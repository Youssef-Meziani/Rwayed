document.addEventListener('DOMContentLoaded', function() {
    const couponForm = document.getElementById('couponForm');
    const applyCouponButton = document.getElementById('applyCouponButton');
    const couponCodeInput = document.querySelector('[name="coupon[coupon_code]"]');

    // Prevent form submission on Enter key press
    couponForm.addEventListener('submit', function(event) {
        event.preventDefault();
    });

    // Handle Apply Coupon button click
    applyCouponButton.addEventListener('click', function() {
        const couponCode = couponCodeInput.value.trim();

        if (couponCode === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a coupon code!',
            });
            return;
        }

        Swal.fire({
            title: 'Please wait...',
            text: 'Verifying your coupon code...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/verify-coupon?coupon_code=' + couponCode)
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        title: 'Coupon Applied!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Great!'
                    }).then(() => {
                        couponForm.submit();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Coupon',
                        text: data.message,
                        footer: '<a href="/contact">Need help? Contact our support team</a>'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while verifying the coupon code. Please try again.',
                    footer: '<a href="/contact">Need help? Contact our support team</a>'
                });
            });
    });

    // Handle Enter key press in coupon code input
    couponCodeInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            applyCouponButton.click();
        }
    });
});
