<?php
session_start();
// Подключение к базе данных
require_once 'db_connect.php';

// Проверяем, передан ли username в запросе
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    try {
        // Запрос на обновление записи
        $query = "UPDATE users SET other_info = NULL WHERE username = :username";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->execute();

        // Возвращаем сообщение об успешном выполнении операции
        echo "Все настройки успешно очищены.";
    } catch (PDOException $e) {
        // В случае ошибки выводим сообщение об ошибке
        echo "Ошибка выполнения запроса: " . $e->getMessage();
    }
} else {
    // Если не передан username, выводим сообщение об ошибке
    echo "Не удалось определить пользователя.";
}
?>
