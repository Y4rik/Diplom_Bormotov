<?php
session_start();
include 'db_connect.php';

try {
    if (!isset($_SESSION['username'])) {
        throw new Exception("Пользователь не авторизован.");
    }

    $username = $_SESSION['username'];
    $day = $_POST['day'];
    $oldMeal = $_POST['oldMeal'];
    $newMeal = $_POST['newMeal'];

    // Получаем id пользователя по его username
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user_id = $stmt->fetchColumn();

    if (!$user_id) {
        throw new Exception("Пользователь не найден.");
    }

    // Получаем текущий план питания пользователя
    $stmt = $pdo->prepare("SELECT diet_plan FROM training_and_nutrition WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $diet_plan = $stmt->fetchColumn();

    if (!$diet_plan) {
        throw new Exception("План питания не найден.");
    }

    // Разбиваем план на дни
    $days_meals = explode('day', $diet_plan);

    // Проверяем, существует ли указанный день
    if (!isset($days_meals[$day])) {
        throw new Exception("Неверный день.");
    }

    // Разбиваем блюда для указанного дня
    $day_meals = explode(',', trim($days_meals[$day]));

    // Ищем и заменяем блюдо
    $updated_meals = array_map(function($e) use ($oldMeal, $newMeal) {
        return trim($e) == trim($oldMeal) ? trim($newMeal) : $e;
    }, $day_meals);

    // Обновляем план для указанного дня
    $days_meals[$day] = implode(', ', $updated_meals);

    // Собираем обновленный план обратно
    $new_plan = implode('day', $days_meals);

    // Обновляем план в базе данных
    $stmt = $pdo->prepare("UPDATE training_and_nutrition SET diet_plan = ? WHERE user_id = ?");
    $stmt->execute([$new_plan, $user_id]);

    echo "Блюдо заменено.";

} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
