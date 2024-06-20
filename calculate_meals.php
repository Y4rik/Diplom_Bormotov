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

try {
    // Получаем данные пользователя из базы данных
    $query = "SELECT id, user_weight, user_height, age, activity, user_aim, other_info, gender FROM users WHERE username = :username";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':username', $username, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    // Проверяем, получены ли данные пользователя
    if (!$row) {
        echo "Данные пользователя не найдены.";
        exit;
    }

    // Получаем данные пользователя
    $userId = $row['id']; // ID пользователя
    $weight = $row['user_weight']; // Вес в кг
    $height = $row['user_height']; // Рост в см
    $age = $row['age']; // Возраст в годах
    $activity = $row['activity']; // Уровень активности
    $user_aim = $row['user_aim']; // Цель пользователя
    $other_info = $row['other_info']; // Информация о нежелательных продуктах
    $gender = $row['gender']; // Пол

    // Преобразуем other_info в массив, если оно не пустое
    $unwanted_ingredients = [];
    if (!empty($other_info)) {
        $unwanted_ingredients = explode(',', trim($other_info, ','));
    }

    // Рассчитываем дневную норму калорий по формуле с учетом пола
    if ($gender == 'male') {
        $calories = (10 * $weight + 6.25 * $height - 5 * $age + 5) * getActivityMultiplier($activity);
    } else {
        $calories = (10 * $weight + 6.25 * $height - 5 * $age - 161) * getActivityMultiplier($activity);
    }

    // Применяем коррекцию калорий в зависимости от цели пользователя
    switch ($user_aim) {
        case 'build_muscle':
            $calories *= 1.20; // Увеличиваем на 20%
            break;
        case 'lose_weight':
            $calories *= 0.80; // Уменьшаем на 20%
            break;
        case 'maintain_weight':
        default:
            // Ничего не изменяем
            break;
    }

    // Формируем сообщение с результатами расчета
    $resultMessage = "Дневная норма калорий для пользователя $username: $calories ккал";

    // Выводим результаты расчета в div.results-container
    echo '<div class="results-container">';
    echo '<label>' . $resultMessage . '</label>';
    

    // Распределяем калории на три приема пищи
    $mealCalories = $calories / 3;

    // Получаем список дней недели
    $daysOfWeek = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];

    // Создаем массив для хранения плана питания на неделю
    $weeklyDietPlan = [];

    // Цикл по дням недели
    foreach ($daysOfWeek as $index => $day) {
        // Выводим название дня
        echo "<label>$day</label>";

        // Переменная для хранения блюд текущего дня
        $dailyMeals = [];

        // Переменная для хранения имен уже выбранных блюд
        $selectedMealNames = [];

        // Повторяем запрос, пока не будет набрано 3 уникальных блюда без нежелательных ингредиентов
        while (count($dailyMeals) < 3) {
            // Получаем случайные блюда для данного дня
            $query = "SELECT name, composition, image_path, calories FROM products 
                      WHERE calories <= :mealCalories
                      ORDER BY RANDOM()
                      LIMIT 10";
            $statement = $pdo->prepare($query);
            $statement->bindParam(':mealCalories', $mealCalories, PDO::PARAM_INT);
            $statement->execute();
            $meals = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Фильтруем блюда, чтобы исключить те, которые содержат нежелательные ингредиенты или уже выбраны
            foreach ($meals as $meal) {
                $hasUnwantedIngredient = false;
                foreach ($unwanted_ingredients as $ingredient) {
                    if (stripos($meal['composition'], trim($ingredient)) !== false) {
                        $hasUnwantedIngredient = true;
                        break;
                    }
                }
                if (!$hasUnwantedIngredient && !in_array($meal['name'], $selectedMealNames)) {
                    $dailyMeals[] = $meal;
                    $selectedMealNames[] = $meal['name'];
                    if (count($dailyMeals) >= 3) {
                        break;
                    }
                }
            }
        }

        // Логируем выбранные блюда для отладки
        //echo "<pre>Выбранные блюда для $day: ";
        //foreach ($dailyMeals as $meal) {
        //    echo $meal['name'] . ", ";
        //}
        //echo "</pre>";

        // Проверяем, нет ли дублирования блюд
        $mealNames = array_column($dailyMeals, 'name');
        if (count($mealNames) !== count(array_unique($mealNames))) {
            echo "<pre>Обнаружено дублирование блюд в $day</pre>";
        }

        // Рассчитываем вес каждого блюда
        $updatedMeals = [];
        foreach ($dailyMeals as $meal) {
            $meal['weight'] = ($mealCalories / $meal['calories']) * 100; // Вес в граммах
            $updatedMeals[] = $meal;
        }

        // Формируем строку с названиями блюд для сохранения в базу данных
        $mealNamesString = "day" . ($index + 1) . ":," . implode(', ', array_column($updatedMeals, 'name'));
        $weeklyDietPlan[] = $mealNamesString;

        // Выводим список блюд для данного дня
        echo '<div class="meal-container">';
        foreach ($updatedMeals as $meal) {
            echo '<div class="meal">';
            echo '<img src="' . $meal['image_path'] . '" alt="' . $meal['name'] . '">';
            echo '<div class="text-container">';
            echo '<h3>' . $meal['name'] . '</h3>';
            echo '<p>' . $meal['composition'] . '</p>';
            echo '<p>Вес блюда: ' . round($meal['weight'], 2) . ' грамм</p>';
            echo '</div>'; // Закрываем div.text-container
            echo '</div>'; // Закрываем div.meal
        }
        echo '</div>'; // Закрываем div.meal-container
        
    }
    echo '</div>'; // Закрываем div.results-container

    // Сохраняем план питания в базу данных
    $dietPlanString = implode(', ', $weeklyDietPlan);
    $updateQuery = "UPDATE training_and_nutrition SET diet_plan = :dietPlan WHERE user_id = :userId";
    $updateStatement = $pdo->prepare($updateQuery);
    $updateStatement->bindParam(':dietPlan', $dietPlanString, PDO::PARAM_STR);
    $updateStatement->bindParam(':userId', $userId, PDO::PARAM_INT);
    $updateStatement->execute();

    echo "План питания успешно сохранен.";

} catch (PDOException $e) {
    echo "Ошибка выполнения запроса: " . $e->getMessage();
}

// Функция для расчета коэффициента активности
function getActivityMultiplier($activity)
{
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
