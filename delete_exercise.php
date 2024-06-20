<?php
session_start();
include 'db_connect.php';

try {
    if (!isset($_SESSION['username'])) {
        throw new Exception("Пользователь не авторизован.");
    }

    $username = $_SESSION['username'];
    $day = $_POST['day'];
    $exercise = $_POST['exercise'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user_id = $stmt->fetchColumn();

    if (!$user_id) {
        throw new Exception("Пользователь не найден.");
    }

    $stmt = $pdo->prepare("SELECT exercise_plan FROM training_and_nutrition WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $exercise_plan = $stmt->fetchColumn();

    if (!$exercise_plan) {
        throw new Exception("Тренировка не найдена.");
    }

    $days_exercises = explode('day', $exercise_plan);
    $day_exercises = explode(',', trim($days_exercises[$day]));

    $updated_exercises = array_filter($day_exercises, function($e) use ($exercise) {
        return trim($e) != trim($exercise);
    });

    $days_exercises[$day] = implode(',', $updated_exercises);
    $new_plan = implode('day', $days_exercises);

    $stmt = $pdo->prepare("UPDATE training_and_nutrition SET exercise_plan = ? WHERE user_id = ?");
    $stmt->execute([$new_plan, $user_id]);

    echo "Упражнение удалено.";

} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
