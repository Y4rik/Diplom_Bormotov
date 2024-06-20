<?php
session_start();

// Подключаемся к базе данных
include 'db_connect.php';

// Получаем username пользователя из сессии
$username = $_SESSION['username'];

try {
    // Получаем информацию о сохраненных тренировках пользователя из таблицы users
    $stmt = $pdo->prepare("SELECT other_inf FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверяем, есть ли сохраненные тренировки у пользователя
    if ($user && isset($user['other_inf'])) {
        // Возвращаем данные о тренировках пользователя в формате JSON
        echo $user['other_inf'];
    } else {
        echo json_encode(['error' => 'No workouts found for the user']);
    }
} catch (PDOException $e) {
    // В случае ошибки выводим сообщение об ошибке
    echo json_encode(['error' => 'Error fetching workouts: ' . $e->getMessage()]);
}

// Закрываем соединение с базой данных
$pdo = null;
?>
