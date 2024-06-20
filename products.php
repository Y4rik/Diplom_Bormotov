<!-- products.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Спортивный сайт</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключаем файл со стилями -->
</head>
<body>
    <?php include 'header.php'; ?> <!-- Подключаем шапку сайта -->

    <div class="main-content-ex">
        <div class="search-container">
            <!-- Поле для ввода поискового запроса с автозаполнением -->
            <input type="text" id="search-input" placeholder="Поиск блюд..." class="search-input" autocomplete="off">
            <button id="search-button" class="search-button">Искать</button>
            <div class="autocomplete-results"></div> <!-- Контейнер для отображения результатов автозаполнения -->
            
        </div>

        <div class="results-container">
            <!-- Здесь будут выводиться результаты поиска -->
        </div>
    </div>

    <footer class="footer">
        <p>© 2024 Спортивный сайт</p>
    </footer>

    <script>
        document.getElementById("search-input").addEventListener("input", function() {
            var searchTerm = document.getElementById("search-input").value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "autocomplete_meals.php?search_term=" + searchTerm, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var autocompleteResults = document.querySelector(".autocomplete-results");
                    autocompleteResults.innerHTML = xhr.responseText;
                    autocompleteResults.style.display = searchTerm.length ? "block" : "none"; // Отображаем результаты только если есть введенный текст
                }
            };
            xhr.send();
        });

        // Обработчик клика на вариант автозаполнения
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("autocomplete-result")) {
                document.getElementById("search-input").value = e.target.textContent; // Заполняем поле поиска выбранным вариантом
                document.querySelector(".autocomplete-results").style.display = "none"; // Скрываем контейнер с вариантами
            }
        });
        // Обработчик клика на кнопку поиска
        document.getElementById("search-button").addEventListener("click", function() {
            // Скрываем блок результатов автозаполнения
            document.querySelector(".autocomplete-results").style.display = "none";
        
            // Другие действия, выполняемые при нажатии на кнопку поиска
        });

        document.getElementById("search-button").addEventListener("click", function() {
            var searchTerm = document.getElementById("search-input").value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "search_meals.php?search_term=" + searchTerm, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.querySelector(".results-container").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });
    </script>
</body>
</html>
