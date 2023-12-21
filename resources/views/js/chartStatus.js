url = 'http://127.0.0.1:8000/api/v1/admin/status';
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
        var labels = data.totals1.map(item => item.order_date); // Đặt tên thuộc tính 'order_date' tương ứng với dữ liệu của totals1
        var values1 = data.totals1.map(item => item.total_price);
        var values2 = data.totals2.map(item => item.total_price);
        var values3 = data.totals3.map(item => item.total_price);

        console.log(length(labels))
        // Làm mới canvas để vẽ biểu đồ
        var ctx = document.getElementById('myLineChart').getContext('2d');

        // Tạo biểu đồ đường
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Data 1',
                        data: values1,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        fill: false
                    },
                    {
                        label: 'Data 2',
                        data: values2,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        fill: false
                    },
                    {
                        label: 'Data 3',
                        data: values3,
                        borderColor: 'rgba(255, 255, 0, 1)',
                        borderWidth: 1,
                        fill: false
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }).catch(error => console.error('Error fetching data:', error));
