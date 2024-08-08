document.getElementById("invoiceForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Предотвращаем обычное поведение отправки формы

    var formData = new FormData(this); // Получаем данные формы

    // Отправляем данные формы на сервер
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "invoice_form.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Парсим ответ как JSON
            var response = JSON.parse(xhr.responseText);

            // Получаем значения totalPrice и excelLink из объекта response
            var totalPrice = response.totalPrice;
            var excelLink = response.excelFilePath;

            // Отображаем значения на странице
            document.getElementById("totalPrice").textContent = "Общая стоимость: " + totalPrice;
            document.getElementById("excelLink").href = excelLink;
            document.getElementById("result").style.display = "block";

        }
    };

    xhr.send(formData);
});
