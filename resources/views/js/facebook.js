document.addEventListener('DOMContentLoaded', function () {
    // Gọi API ở đây

    function fetchData(page) {
        // Hàm tải dữ liệu từ API dựa trên trang hiện tại
        url = `http://127.0.0.1:8000/api/v1/auth/facebook?page=${page}`
        const options = {
            method: "GET",
            headers: {
                'Content-Type': 'application/json',
            }
        }
        fetch(url, options)
            .then(response => response.json())
            .then(data => {
                const totalPages = data.meta.last_page;
                displayData(data.data);
                displayPagination(totalPages);
            })
            .catch(error => {
                console.error('Lỗi khi tải dữ liệu:', error);
            });
    }
    fetchData(currentPage);
});