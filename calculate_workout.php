
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Подключаемся к базе данных
    include 'db_connect.php';

    // Получаем username пользователя из сессии
    $username = $_SESSION['username'];

    try {
        // Получаем информацию о пользователе из таблицы users
        $stmt = $pdo->prepare("SELECT location, user_aim FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $loc_tion = $user['location'];
            $user_aim = $user['user_aim'];

            // Получаем количество дней тренировки в неделю из формы
            $training_days = $_POST['training_days'];
            // Выводим сообщения в зависимости от цели пользователя
            if ($user_aim == 'lose_weight') {
                echo '<p class="advice2">Информация по количеству подходов и повторений</p>';
                echo '<p class="advice">
                В процессе похудения акцент делается на высоких объемах тренировок с меньшими весами. Цель состоит в том, чтобы сжигать больше калорий и стимулировать обмен веществ, что способствует снижению веса. Для этого рекомендуется выбирать веса, которые позволяют выполнять большее количество повторений (обычно от 12 до 15 и более) в каждом подходе. Это помогает поддерживать высокий темп работы сердца и увеличивать калорийный дефицит, что в свою очередь способствует снижению жировой массы</p>';
            } elseif ($user_aim == 'build_muscle') {
                echo '<p class="advice2">Информация по количеству подходов и повторений</p>';
                echo '<p class="advice">
                При стремлении к набору мышечной массы ключевую роль играют высокие веса и низкое количество повторений. Основная идея заключается в том, чтобы максимально нагрузить мышцы и стимулировать их рост. Для этого рекомендуется выбирать веса, которые позволяют выполнить лишь 1-2 повторения в пределах каждого подхода. Такой подход способствует активации большего количества мышечных волокон, что способствует их росту и увеличению объема.</p>';
            }

            // Определяем категории упражнений для каждого дня тренировки
            if ($training_days == 2) {
                $categories_day1 = ['Ноги', 'Спина', 'Грудь', 'Трицепс'];
                $categories_day2 = ['Плечи', 'Спина', 'Бицепс'];
            } elseif ($training_days == 3) {
                $categories_day1 = ['Ноги', 'Спина', 'Плечи'];
                $categories_day2 = ['Спина', 'Бицепс'];
                $categories_day3 = ['Грудь', 'Трицепс'];
            }

            // Формируем запрос для первого дня
            $exercises_day1 = [];
            foreach ($categories_day1 as $category) {
                $stmt = $pdo->prepare("SELECT image_path, name, category, description FROM exercises WHERE category = ? AND location = ? ORDER BY RANDOM() LIMIT " . ($training_days == 3 ? 2 : 1));
                $stmt->execute([$category, $loc_tion]);
                $exercise = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($exercise) {
                    $exercises_day1 = array_merge($exercises_day1, $exercise);
                }
            }

            // Выводим результаты поиска для первого дня
            echo '<div class="results-container">';
            echo '<label>Первый тренировочный день</label>';
            foreach ($exercises_day1 as $exercise) {
                echo '<div class="exercise">';
                echo '<div class="image-container">';
                echo '<img src="' . $exercise['image_path'] . '" alt="' . $exercise['name'] . '">';
                echo '</div>'; // Закрываем div.image-container
                echo '<div class="text-container">';
                echo '<h3>' . $exercise['name'] . '</h3>';
                echo '<h2>' . $exercise['category'] . '</h2>';
                echo '<p>' . $exercise['description'] . '</p>';
                echo '</div>'; // Закрываем div.text-container
                echo '</div>'; // Закрываем div.exercise
            }
            echo '</div>'; // Закрываем div.results-container

            // Формируем запрос для второго дня
            $exercises_day2 = [];
            foreach ($categories_day2 as $category) {
                $stmt = $pdo->prepare("SELECT image_path, name, category, description FROM exercises WHERE category = ? AND location = ? ORDER BY RANDOM() LIMIT " . ($training_days == 3 ? 2 : 1));
                $stmt->execute([$category, $loc_tion]);
                $exercise = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($exercise) {
                    $exercises_day2 = array_merge($exercises_day2, $exercise);
                }
            }

            // Выводим результаты поиска для второго дня
            echo '<div class="results-container">';
            echo '<label>Второй тренировочный день</label>';
            foreach ($exercises_day2 as $exercise) {
                echo '<div class="exercise">';
                echo '<div class="image-container">';
                echo '<img src="' . $exercise['image_path'] . '" alt="' . $exercise['name'] . '">';
                echo '</div>'; // Закрываем div.image-container
                echo '<div class="text-container">';
                echo '<h3>' . $exercise['name'] . '</h3>';
                echo '<h2>' . $exercise['category'] . '</h2>';
                echo '<p>' . $exercise['description'] . '</p>';
                echo '</div>'; // Закрываем div.text-container
                echo '</div>'; // Закрываем div.exercise
                // Вывод упражнений
            }
            echo '</div>'; // Закрываем div.results-container

            // Если тренировок 3, формируем запрос для третьего дня
            if ($training_days == 3) {
                $exercises_day3 = [];
                foreach ($categories_day3 as $category) {
                    $stmt = $pdo->prepare("SELECT image_path, name, category, description FROM exercises WHERE category = ? AND location = ? ORDER BY RANDOM() LIMIT 2");
                    $stmt->execute([$category, $loc_tion]);
                    $exercise = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if ($exercise) {
                        $exercises_day3 = array_merge($exercises_day3, $exercise);
                    }
                }

                // Выводим результаты поиска для третьего дня
                echo '<div class="results-container">';
                echo '<label>Третий тренировочный день</label>';
                foreach ($exercises_day3 as $exercise) {
                    echo '<div class="exercise">';
                    echo '<div class="image-container">';
                    echo '<img src="' . $exercise['image_path'] . '" alt="' . $exercise['name'] . '">';
                    echo '</div>'; // Закрываем div.image-container
                    echo '<div class="text-container">';
                    echo '<h3>' . $exercise['name'] . '</h3>';
                    echo '<h2>' . $exercise['category'] . '</h2>';
                    echo '<p>' . $exercise['description'] . '</p>';
                    echo '</div>'; // Закрываем div.text-container
                    echo '</div>'; // Закрываем div.exercise
                    
                    // Вывод упражнений
                }
                echo '</div>'; // Закрываем div.results-container
            }

            

        } else {
            echo "Пользователь не найден.";
        }
        // Формируем массив с тренировочными данными для всех дней
        $training_data = [];

        // Добавляем данные для первого дня
        $training_data['day1'] = $exercises_day1;

        // Добавляем данные для второго дня
        $training_data['day2'] = $exercises_day2;

        // Если тренировок 3, добавляем данные для третьего дня
        if ($training_days == 3) {
            $training_data['day3'] = $exercises_day3;
        }

        // Преобразуем массив в JSON
        $training_data_json = json_encode($training_data);

        // Обновляем данные пользователя в таблице users
        $stmt = $pdo->prepare("UPDATE users SET other_info = ? WHERE username = ?");
        $stmt->execute([$training_data_json, $username]);
        // Закрываем соединение с базой данных
        $pdo = null;
    } catch (PDOException $e) {
        // В случае ошибки выводим сообщение об ошибке
        echo "Ошибка при выполнении запроса: " . $e->getMessage();
    }
}
?>
