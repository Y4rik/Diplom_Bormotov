<?php
// Подключение к базе данных
require_once 'db_connect.php';

// Проверяем, существует ли сессия пользователя
session_start();
if (!isset($_SESSION['username'])) {
    echo "Не удалось определить пользователя.";
    exit;
}

// Получаем имя пользователя из сессии
$username = $_SESSION['username'];

// Проверяем, были ли выбраны ингредиенты для удаления
if (!isset($_POST['options'])) {
    echo "Нет выбранных ингредиентов для удаления.";
    exit;
}

// Получаем выбранные ингредиенты
$ingredientsToRemove = $_POST['options'];

try {
    // Получаем текущие настройки пользователя
    $query = "SELECT other_info FROM users WHERE username = :username";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':username', $username, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    $currentOptions = $row['other_info'];

    // Преобразуем текущие настройки в массив
    $currentOptionsArray = explode(",", $currentOptions);

    // Удаляем выбранные ингредиенты из текущих настроек
    $updatedOptionsArray = array_diff($currentOptionsArray, $ingredientsToRemove);

    // Обновляем настройки в базе данных
    $updatedOptions = implode(",", $updatedOptionsArray);
    $query = "UPDATE users SET other_info = :other_info WHERE username = :username";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':other_info', $updatedOptions, PDO::PARAM_STR);
    $statement->bindParam(':username', $username, PDO::PARAM_STR);
    $statement->execute();

    echo "Выбранные ингредиенты были успешно разрешены и удалены.";
} catch (PDOException $e) {
    echo "Ошибка выполнения запроса: " . $e->getMessage();
}
?>
