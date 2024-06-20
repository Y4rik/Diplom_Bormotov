<?php
// Подключение к базе данных
include 'db_connect.php';

// Проверка, были ли данные отправлены через форму
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $new_data = $_POST['new_data'];

    try {
        // Выполнение запроса на обновление данных в базе данных
        $update_query = $pdo->prepare("UPDATE your_table SET column_name = :new_data");
        $update_query->bindParam(':new_data', $new_data);
        $update_query->execute();

        echo "Данные успешно обновлены.";
    } catch (PDOException $e) {
        // Вывод сообщения об ошибке
        echo "Ошибка: " . $e->getMessage();
    }
}
?>
