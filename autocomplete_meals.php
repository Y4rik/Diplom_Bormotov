<!-- autocomplete.php -->
<?php
include 'db_connect.php'; // Подключаемся к базе данных

$searchTerm = $_GET['search_term']; // Получаем поисковый запрос от пользователя

try {
    // Подготовленный запрос для выборки данных
    $stmt = $pdo->prepare("SELECT DISTINCT name FROM products WHERE LOWER(name) LIKE LOWER(:searchTerm) OR LOWER(composition) LIKE LOWER(:searchTerm)");
    $stmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Выводим результаты автозаполнения
    foreach ($results as $result) {
        echo '<div class="autocomplete-result">' . $result['name'] . '</div>';
    }
} catch (PDOException $e) {
    // В случае ошибки выводим сообщение об ошибке
    echo "Ошибка выполнения запроса: " . $e->getMessage();
}
?>
