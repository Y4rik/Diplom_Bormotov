<?php
// Подключение к базе данных
require_once 'db_connect.php';

// Массив запрещенных сочетаний
$forbiddenWords = ["без кожи"];

// Функция для получения уникальных слов из столбца composition с разделением по точке
function getUniqueWords($pdo, $forbiddenWords) {
    try {
        $query = "SELECT composition FROM products";
        $statement = $pdo->prepare($query);
        $statement->execute();
        $uniqueWords = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $words = explode(",", $row['composition']);
            foreach ($words as $word) {
                $word = trim($word);
                // Разделяем слово по точке
                $wordParts = explode(".", $word);
                foreach ($wordParts as $part) {
                    $part = trim($part);
                    // Проверяем, не начинается ли слово с цифры и не является ли запрещенным
                    if (!ctype_digit(substr($part, 0, 1)) && !in_array($part, $forbiddenWords)) {
                        if (!in_array($part, $uniqueWords)) {
                            $uniqueWords[] = $part;
                        }
                    }
                }
            }
        }

        // Сортируем полученные слова в алфавитном порядке
        sort($uniqueWords);

        return $uniqueWords;
    } catch (PDOException $e) {
        echo "Ошибка выполнения запроса: " . $e->getMessage();
        return [];
    }
}

// Получаем уникальные слова из столбца composition
$uniqueWords = getUniqueWords($pdo, $forbiddenWords);
?>

<!-- Отображаем форму с чекбоксами -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чекбоксы</title>
    <style>
        #checkbox-form-container {
            width: 100%;
            max-height: 600px; /* Максимальная высота области */
            overflow-y: auto; /* Вертикальный скроллинг */
            border: 1px solid #ccc; /* Рамка вокруг области */
            padding: 10px;
            box-sizing: border-box; /* Включает padding и border в размеры элемента */
        }
        #options-form {
            display: table;
            width: 100%;
        }
        .checkbox-item {
            display: table-row;
        }
        .checkbox-item div {
            display: table-cell;
            padding: 5px;
        }
        .checkbox-item input[type="checkbox"] {
            transform: scale(1.5); /* Увеличение размера чекбоксов */
            margin-right: 10px;
        }
        .checkbox-item label {
            font-size: 0.9em; /* Уменьшение размера текста */
        }
        .buttons {
            width: 100%;
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div id="checkbox-form-container">
    <?php if (!empty($uniqueWords)): ?>
        <form id="options-form">
            <?php foreach ($uniqueWords as $word): ?>
                <div class="checkbox-item">
                    <div><input type="checkbox" name="options[]" value="<?= $word ?>" id="checkbox-<?= $word ?>"></div>
                    <div><label for="checkbox-<?= $word ?>"><?= $word ?></label></div>
                </div>
            <?php endforeach; ?>
        </form>
    <?php else: ?>
        <p>Нет данных для отображения.</p>
    <?php endif; ?>
</div>

<div class="buttons">
    <button type="button" onclick="saveOptions()">Сохранить</button>
    <button type="button" onclick="clearOptions()">Очистить все настройки</button>
    <button type="button" onclick="allowIngredients()">Разрешить ингредиент</button>
</div>

<script>
    function saveOptions() {
        var formData = $('#options-form').serialize();
        $.ajax({
            type: 'POST',
            url: 'process_options.php',
            data: formData,
            success: function(response) {
                $('.results-container').html(response);
            }
        });
    }

    function clearOptions() {
        $.ajax({
            type: 'POST',
            url: 'clear_options.php',
            success: function(response) {
                $('.results-container').html(response);
            }
        });
    }

    function allowIngredients() {
        var formData = $('#options-form').serialize();
        $.ajax({
            type: 'POST',
            url: 'allow_ingredients.php',
            data: formData,
            success: function(response) {
                $('.results-container').html(response);
            }
        });
    }
</script>
</body>
</html>
