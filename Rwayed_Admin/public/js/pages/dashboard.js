document.addEventListener('DOMContentLoaded', function () {
    // Fetch total sales amount
    fetch("{{ path('stats_total_sales') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-sales-amount').textContent = `${data.totalSales.toFixed(2)}`+' DH';
        });
    // Fetch total techniciens count
    fetch("{{ path('stats_total_techniciens') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-techniciens-count').textContent = `${data.totalTechniciens}`;
        });
    // Fetch total adherents count
    fetch("{{ path('stats_total_adherents') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-adherents-count').textContent = `${data.totalAdherents}`;
        });
    // Fetch total orders count
    fetch("{{ path('stats_total_orders') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-orders-count').textContent = `${data.totalOrders}`;
        });
    fetch("{{ path('stats_orders') }}")
        .then(response => response.json())
        .then(data => {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const chartData = new Array(12).fill(0);

            data.forEach(item => {
                chartData[item.month - 1] = item.count;
            });

            const options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Orders',
                    data: chartData
                }],
                xaxis: {
                    categories: months
                },
                colors: JSON.parse(document.getElementById('overview').getAttribute('data-colors')),
                title: {
                    text: 'Monthly Orders',
                    align: 'left'
                }
            };

            const chart = new ApexCharts(document.querySelector("#overview"), options);
            chart.render();
        });

    const seasonalData = JSON.parse(document.getElementById('seasonal-data').textContent);
    var options = {
        series: seasonalData.map(data => data.count),
        labels: seasonalData.map(data => data.season),
        chart: {
            type: 'pie',
            height: 380
        },
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#pneus-by-season"), options);
    chart.render();

});

function processOrder(orderId, currentStatus) {
    const statuses = {
        'Pending': 'Pending',
        'Shipped': 'Shipped',
        'Delivered': 'Delivered'
    };

    delete statuses[currentStatus]; // Remove current status from options

    const options = Object.entries(statuses).reduce((acc, [key, value]) => {
        acc[key] = value;
        return acc;
    }, {});

    Swal.fire({
        title: 'Select new status',
        input: 'select',
        inputOptions: options,
        inputPlaceholder: 'Choose status',
        showCancelButton: true,
        preConfirm: (newStatus) => {
            if (!newStatus) {
                Swal.showValidationMessage('You need to choose a status!');
            }
            return newStatus; // Ensure to return the value for next promise processing
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            updateOrderStatus(orderId, result.value);
        }
    });
}

function updateOrderStatus(orderId, newStatus) {
    Swal.fire({
        title: 'Processing...',
        text: 'Please wait...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading(); // Show loading indicator
        },
        didOpen: () => {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", `/update-order-status/${orderId}`, true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) { // Ensure the request is complete
                    Swal.close(); // Close the Swal loading spinner
                    if (xhr.status === 200) {
                        Swal.fire('Success', 'Order status updated successfully.', 'success').then(() => {
                            location.reload(); // Optionally reload the page
                        });
                    } else {
                        Swal.fire('Error', 'There was a problem updating the order status: ' + xhr.responseText, 'error');
                    }
                }
            };

            xhr.onerror = function () {
                Swal.fire('Error', 'Network or server error occurred.', 'error');
            };

            xhr.send(JSON.stringify({ status: newStatus }));
        }
    });
}
