document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-address-link').forEach(button => {
        button.addEventListener('click', function () {
            const addressId = this.getAttribute('data-address-id');

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
                    Swal.fire({
                        title: 'Deleting...',
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false,
                    });

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', `/address/delete/${addressId}`, true); // Utilisation du chemin généré par Symfony
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                Swal.fire(
                                    'Deleted!',
                                    'The address has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the address.',
                                    'error'
                                );
                            }
                        }
                    };
                    xhr.send();
                }
            });
        });
    });
});
