document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const suggestionsList = document.getElementById("suggestionsList");

    searchInput.addEventListener("input", function () {
        const value = this.value.toLowerCase();
        suggestionsList.innerHTML = '';

        console.log(value)
        url = 'http://127.0.0.1:8000/api/v1/products/search';
        const options = {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({value})
        }

        // Gọi API với từ khóa tìm kiếm
        fetch(url, options)
            .then(response => response.json())
            .then(data => {
                const suggestions = data.data;

                suggestions.forEach(suggestion => {
                    const li = document.createElement("li");
                    li.textContent = suggestion.name; // Thay đổi dựa trên cấu trúc dữ liệu API
                    li.addEventListener("click", function () {
                        searchInput.value = suggestion.name;
                        suggestionsList.style.display = "none";
                    });
                    suggestionsList.appendChild(li);
                });

                suggestionsList.style.display = suggestions.length > 0 ? "block" : "none";
            })
            .catch(error => console.error("Error fetching data:", error));
    });

    document.addEventListener("click", function (event) {
        if (!event.target.closest(".search-container")) {
            suggestionsList.style.display = "none";
        }
    });
});
