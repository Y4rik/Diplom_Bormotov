<?php
session_start();
include 'db_connect.php';

try {
    if (!isset($_SESSION['username'])) {
        throw new Exception("Пользователь не авторизован.");
    }

    $username = $_SESSION['username'];
    $day = (int)$_POST['day']; // Преобразуем день в целое число
    $meal = $_POST['meal'];

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

    // Используем регулярное выражение для разделения на дни
    preg_match_all('/day\d+:([^day]*)/', $diet_plan, $matches);

    if (empty($matches[0])) {
        throw new Exception("Не удалось найти дни в плане питания.");
    }

    $days_meals_txt = $matches[0];
    $days_meals_content = $matches[1];

    // Проверяем, существует ли указанный день
    if (!isset($days_meals_content[$day])) {
        throw new Exception("Неверный день.");
    }

    // Разбиваем блюда для указанного дня
    $day_meals_arr = array_filter(array_map('trim', explode(',', $days_meals_content[$day])));

    // Удаляем блюдо
    $updated_meals = array_filter($day_meals_arr, function($e) use ($meal) {
        return trim($e) != trim($meal);
    });

    // Обновляем план для указанного дня
    $days_meals_txt[$day] = "day" . ($day + 1) . ":," . implode(', ', $updated_meals);

    // Собираем обновленный план обратно
    $new_plan = implode('', $days_meals_txt);

    // Обновляем план в базе данных
    $stmt = $pdo->prepare("UPDATE training_and_nutrition SET diet_plan = ? WHERE user_id = ?");
    $stmt->execute([$new_plan, $user_id]);

    echo "Блюдо удалено.";

} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
