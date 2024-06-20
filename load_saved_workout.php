<?php
session_start();

include 'db_connect.php';

try {
    if (!isset($_SESSION['username'])) {
        throw new Exception("Пользователь не авторизован.");
    }

    $username = $_SESSION['username'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user_id_row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user_id_row) {
        $user_id = $user_id_row['id'];
    } else {
        throw new Exception("Ошибка: id пользователя не найден.");
    }

    $stmt = $pdo->prepare("SELECT exercise_plan FROM training_and_nutrition WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $exercise_plan_txt = $stmt->fetchColumn();

    if (!$exercise_plan_txt) {
        throw new Exception("Сохраненная тренировка не найдена.");
    }

    $days_exercises_txt = explode('day', $exercise_plan_txt);

    for ($i = 1; $i < count($days_exercises_txt); $i++) {
        $day_exercises_txt = trim($days_exercises_txt[$i]);
        $day_exercises_arr = explode(',', $day_exercises_txt);
        $categories = [];

        echo "<div class='results-container'>";
        echo "<label>Тренировочный день $i</label>";

        // Собираем все категории упражнений для данного дня
        foreach ($day_exercises_arr as $exercise_name) {
            $exercise_name = trim($exercise_name);

            $stmt = $pdo->prepare("SELECT category FROM exercises WHERE name LIKE ?");
            $stmt->execute(["%$exercise_name%"]);
            $exercise = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($exercise) {
                $categories[] = $exercise['category'];
            }
        }

        // Удаляем дублирующиеся категории
        $categories = array_unique($categories);

        echo "<select onchange=\"addExercise($i, this.value)\">";
        echo "<option value=''>Добавить упражнение</option>";

        foreach ($categories as $category) {
            $stmt = $pdo->prepare("SELECT name FROM exercises WHERE category = ?");
            $stmt->execute([$category]);
            while ($new_exercise = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . $new_exercise['name'] . "'>" . $new_exercise['name'] . " (" . $category . ")</option>";
            }
        }

        echo "</select>";

        foreach ($day_exercises_arr as $exercise_name) {
            $exercise_name = trim($exercise_name);

            $stmt = $pdo->prepare("SELECT image_path, name, category, description FROM exercises WHERE name LIKE ?");
            $stmt->execute(["%$exercise_name%"]);
            $exercise = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($exercise) {
                echo "<div class='exercise'>";
                echo "<div class='image-container'>";
                echo "<img src='" . $exercise['image_path'] . "' alt='" . $exercise['name'] . "'>";
                echo "</div>";
                echo "<div class='text-container'>";
                echo "<h3>" . $exercise['name'] . "</h3>";
                echo "<h2>" . $exercise['category'] . "</h2>";
                echo "<p>" . $exercise['description'] . "</p>";
                echo "<div class='actions'>";
                echo "<button onclick=\"deleteExercise($i, '$exercise_name')\">Удалить</button>";
                echo "<select onchange=\"replaceExercise($i, '$exercise_name', this.value)\">";
                echo "<option value=''>Выберите новое упражнение</option>";

                $stmt = $pdo->prepare("SELECT name FROM exercises WHERE category = ? AND name != ?");
                $stmt->execute([$exercise['category'], $exercise['name']]);
                while ($new_exercise = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $new_exercise['name'] . "'>" . $new_exercise['name'] . "</option>";
                }

                echo "</select>";
                echo "</div>"; // .actions
                echo "</div>"; // .text-container
                echo "</div>"; // .exercise
            }
        }

        echo "</div>"; // .results-container
    }

} catch (Exception $e) {
    echo "Ошибка выполнения запроса: " . $e->getMessage();
}
?>
