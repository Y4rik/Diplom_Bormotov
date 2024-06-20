<?php
session_start();
include 'db_connect.php';

try {
    if (!isset($_SESSION['username'])) {
        throw new Exception("Пользователь не авторизован.");
    }

    $username = $_SESSION['username'];
    $day = $_POST['day'];
    $newExercise = $_POST['newExercise'];

    // Получаем id пользователя по его username
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user_id = $stmt->fetchColumn();

    if (!$user_id) {
        throw new Exception("Пользователь не найден.");
    }

    // Получаем текущий план упражнений пользователя
    $stmt = $pdo->prepare("SELECT exercise_plan FROM training_and_nutrition WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $exercise_plan = $stmt->fetchColumn();

    if (!$exercise_plan) {
        throw new Exception("Тренировка не найдена.");
    }

    // Разбиваем план на дни
    $days_exercises = explode('day', $exercise_plan);

    // Проверяем, существует ли указанный день
    if (!isset($days_exercises[$day])) {
        throw new Exception("Неверный день тренировки.");
    }

    // Разбиваем упражнения для указанного дня
    $day_exercises = explode(',', trim($days_exercises[$day]));

    // Добавляем новое упражнение
    $day_exercises[] = trim($newExercise);

    // Обновляем план для указанного дня
    $days_exercises[$day] = implode(', ', $day_exercises);

    // Собираем обновленный план обратно
    $new_plan = implode('day', $days_exercises);

    // Обновляем план в базе данных
    $stmt = $pdo->prepare("UPDATE training_and_nutrition SET exercise_plan = ? WHERE user_id = ?");
    $stmt->execute([$new_plan, $user_id]);

    echo "Упражнение добавлено.";

} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
