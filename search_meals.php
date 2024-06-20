<!-- search_exercises.php -->
<?php
include 'db_connect.php'; // Подключаемся к базе данных

$searchTerm = $_GET['search_term']; // Получаем поисковый запрос от пользователя

try {
    // Подготовленный запрос для выборки данных
    $stmt = $pdo->prepare("SELECT name, composition, image_path FROM products WHERE LOWER(name) LIKE LOWER(:searchTerm) OR LOWER(composition) LIKE LOWER(:searchTerm)");
    $stmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Выводим результаты
    foreach ($results as $result) {
        echo '<div>';
        echo '<img src="' . $result['image_path'] . '" alt="' . $result['name'] . '">';
        echo '<div class="text-container">';
        echo '<h3>' . $result['name'] . '</h3>';
        
        echo '<p>' . $result['composition'] . '</p>';
        echo '</div>'; // Закрываем div.text-container
        echo '</div>'; // Закрываем внешний div
    }
} catch (PDOException $e) {
    // В случае ошибки выводим сообщение об ошибке
    echo "Ошибка выполнения запроса: " . $e->getMessage();
}
?>
