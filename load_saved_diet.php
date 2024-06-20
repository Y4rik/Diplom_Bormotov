<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сохраненное питание</title>
    <link rel="stylesheet" href="user_styles.css">
    <style>
        .calories-info {
            margin-left: 14%;
        }
    </style>
</head>
<body>

<?php
session_start();

include 'db_connect.php';

try {
    if (!isset($_SESSION['username'])) {
        throw new Exception("Пользователь не авторизован.");
    }

    $username = $_SESSION['username'];

    // Получаем данные пользователя
    $stmt = $pdo->prepare("SELECT id, user_weight, user_height, age, activity, user_aim, gender FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Данные пользователя не найдены.");
    }

    $user_id = $user['id'];
    $weight = $user['user_weight'];
    $height = $user['user_height'];
    $age = $user['age'];
    $activity = $user['activity'];
    $user_aim = $user['user_aim'];
    $gender = $user['gender'];

    // Рассчитываем дневную норму калорий
    if ($gender == 'male') {
        $calories = (10 * $weight + 6.25 * $height - 5 * $age + 5) * getActivityMultiplier($activity);
    } else {
        $calories = (10 * $weight + 6.25 * $height - 5 * $age - 161) * getActivityMultiplier($activity);
    }

    // Применяем коррекцию калорий в зависимости от цели пользователя
    switch ($user_aim) {
        case 'build_muscle':
            $calories *= 1.20;
            break;
        case 'lose_weight':
            $calories *= 0.80;
            break;
        case 'maintain_weight':
        default:
            break;
    }

    // Выводим информацию о дневной норме калорий
    echo "<div class='calories-info'>";
    echo "<label>Дневная норма калорий для пользователя $username: " . round($calories, 2) . " ккал</label>";
    echo "</div>";

    // Получаем текущий план питания пользователя
    $stmt = $pdo->prepare("SELECT diet_plan FROM training_and_nutrition WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $diet_plan_txt = $stmt->fetchColumn();

    if (!$diet_plan_txt) {
        throw new Exception("Сохраненное меню не найдено.");
    }

    // Убираем первый пустой элемент, если он есть
    $diet_plan_txt = ltrim($diet_plan_txt, ',');

    // Используем регулярное выражение для разделения текста по дням
    preg_match_all('/day\d+:([^day]*)/', $diet_plan_txt, $matches);

    $days_meals_txt = $matches[1];

    $daysOfWeek = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];

    foreach ($daysOfWeek as $i => $day) {
        echo "<div class='results-container'>";
        echo "<label>$day</label>";

        // Отображение всех блюд для добавления рядом с названием дня
        echo "<select onchange=\"addMeal($i, this.value)\">";
        echo "<option value=''>Добавить блюдо</option>";

        $stmt = $pdo->query("SELECT name FROM products");
        while ($new_meal = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='" . $new_meal['name'] . "'>" . $new_meal['name'] . "</option>";
        }

        echo "</select>";

        if (isset($days_meals_txt[$i])) {
            $day_meals_txt = trim($days_meals_txt[$i]);
            $day_meals_arr = array_filter(explode(',', $day_meals_txt));

            $totalMeals = count($day_meals_arr);
            if ($totalMeals > 0) {
                $mealCalories = $calories / $totalMeals;

                foreach ($day_meals_arr as $meal_name) {
                    $meal_name = trim($meal_name);

                    $stmt = $pdo->prepare("SELECT image_path, name, composition, calories FROM products WHERE name LIKE ?");
                    $stmt->execute(["%$meal_name%"]);
                    $meal = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($meal) {
                        $weight = ($mealCalories / $meal['calories']) * 100; // Вес в граммах

                        echo "<div class='meal'>";
                        echo "<div class='image-container'>";
                        echo "<img src='" . $meal['image_path'] . "' alt='" . $meal['name'] . "'>";
                        echo "</div>";
                        echo "<div class='text-container'>";
                        echo "<h3>" . $meal['name'] . "</h3>";
                        echo "<p>" . $meal['composition'] . "</p>";
                        echo "<p>Вес блюда: " . round($weight, 2) . " грамм</p>";
                        echo "<div class='actions'>";
                        echo "<button onclick=\"deleteMeal($i, '$meal_name')\">Удалить</button>";
                        echo "</div>"; // .actions
                        echo "</div>"; // .text-container
                        echo "</div>"; // .meal
                    }
                }
            }
        } else {
            echo "<pre>Нет данных для дня $day.</pre>";
        }

        echo "</div>"; // .results-container
    }

} catch (Exception $e) {
    echo "Ошибка выполнения запроса: " . $e->getMessage();
}

function getActivityMultiplier($activity) {
    switch ($activity) {
        case 'sedentary':
            return 1.2;
        case 'low':
            return 1.375;
        case 'moderate':
            return 1.55;
        case 'active':
            return 1.7;
        case 'very active':
            return 1.9;
        default:
            return 1.2; // По умолчанию минимальная активность
    }
}
?>
</body>
</html>
