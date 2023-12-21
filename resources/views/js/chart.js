
url = 'http://127.0.0.1:8000/api/v1/admin/sales';
const options = {
    method: "GET",
    headers: {
        'Content-Type': 'application/json',
    },
}

// Gọi API với từ khóa tìm kiếm
fetch(url, options)
    .then(response => response.json())
    .then(data => {
        var labels = data.map(item => item.order_date);
        var values = data.map(item => item.total_price);

        // Làm mới canvas để vẽ biểu đồ
        var ctx = document.getElementById('myBarChart').getContext('2d');

        // Tạo biểu đồ cột
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Data',
                    data: values,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    })
    .catch(error => console.error('Error fetching data:', error));
