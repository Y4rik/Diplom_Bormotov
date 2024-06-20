<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Подключаемся к базе данных
    include 'db_connect.php';

    // Получаем username пользователя из сессии
    $username = $_SESSION['username'];

    try {
        // Получаем id пользователя по его username
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user_id_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_id_row) {
            $user_id = $user_id_row['id']; // Сохраняем id пользователя
        } else {
            // Обработка ситуации, когда id пользователя не найден
            // Можно вывести сообщение об ошибке или принять другие меры по вашему усмотрению
            echo "Ошибка: id пользователя не найден.";
            exit; // Прекращаем выполнение скрипта, если id пользователя не найден
        }

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
            switch ($training_days) {
                case 2:
                    $categories_day1 = ['Ноги','Спина','Грудь','Трицепс'];
                    $categories_day2 = ['Плечи','Спина','Бицепс'];
                    break;
                case 3:
                    $categories_day1 = ['Ноги','Спина','Плечи'];
                    $categories_day2 = ['Спина', 'Бицепс'];
                    $categories_day3 = ['Грудь','Трицепс'];
                    break;
                case 4:
                    $categories_day1 = ['Грудь', 'Трицепс'];
                    $categories_day2 = ['Ноги'];
                    $categories_day3 = ['Спина', 'Бицепс'];
                    $categories_day4 = ['Плечи', 'Грудь'];
                    break;
                case 5:
                    $categories_day1 = ['Ноги'];
                    $categories_day2 = ['Плечи'];
                    $categories_day3 = ['Спина'];
                    $categories_day4 = ['Бицепс', 'Спина'];
                    $categories_day5 = ['Грудь', 'Трицепс'];
                    break;
                default:
                    // Handle default case
                    break;
            }

            // Формируем запросы для каждого дня тренировки
            // Формируем запрос для первого дня
            $exercises_day1 = [];
            foreach ($categories_day1 as $category) {
                $stmt = $pdo->prepare("SELECT image_path, name, category, description FROM exercises WHERE category = ? AND location = ? ORDER BY RANDOM() LIMIT " . ($training_days == 2 ? rand(1, 2) : ($training_days == 3 ? rand(2, 3) : ($training_days == 4 ? rand(3, 4) : ($training_days == 5 ? rand(3, 4) : 0)))));
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
                $stmt = $pdo->prepare("SELECT image_path, name, category, description FROM exercises WHERE category = ? AND location = ? ORDER BY RANDOM() LIMIT " . ($training_days == 2 ? rand(1, 2) : ($training_days == 3 ? rand(2, 3) : ($training_days == 4 ? rand(3, 4) : ($training_days == 5 ? rand(3, 4) : 0)))));
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
            }
            echo '</div>'; // Закрываем div.results-container

            // Формируем запрос для третьего дня, если он существует
            if (isset($categories_day3)) {
                $exercises_day3 = [];
                foreach ($categories_day3 as $category) {
                    $stmt = $pdo->prepare("SELECT image_path, name, category, description FROM exercises WHERE category = ? AND location = ? ORDER BY RANDOM() LIMIT " . ($training_days == 3 ? rand(2, 3) : ($training_days == 4 ? rand(3, 4) : ($training_days == 5 ? rand(3, 4) : 0))));
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
                }
                echo '</div>'; // Закрываем div.results-container
            }

            // Формируем запрос для четвертого дня, если он существует
            if (isset($categories_day4)) {
                $exercises_day4 = [];
                foreach ($categories_day4 as $category) {
                    $stmt = $pdo->prepare("SELECT image_path, name, category, description FROM exercises WHERE category = ? AND location = ? ORDER BY RANDOM() LIMIT " . ($training_days == 4 ? rand(3, 4) : ($training_days == 5 ? rand(3, 4) : 0)));
                    $stmt->execute([$category, $loc_tion]);
                    $exercise = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if ($exercise) {
                        $exercises_day4 = array_merge($exercises_day4, $exercise);
                    }
                }

                // Выводим результаты поиска для четвертого дня
                echo '<div class="results-container">';
                echo '<label>Четвертый тренировочный день</label>';
                foreach ($exercises_day4 as $exercise) {
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
            }

            // Формируем запрос для пятого дня, если он существует
            if (isset($categories_day5)) {
                $exercises_day5 = [];
                foreach ($categories_day5 as $category) {
                    $stmt = $pdo->prepare("SELECT image_path, name, category, description FROM exercises WHERE category = ? AND location = ? ORDER BY RANDOM() LIMIT " . ($training_days == 5 ? rand(3, 4) : 0));
                    $stmt->execute([$category, $loc_tion]);
                    $exercise = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if ($exercise) {
                        $exercises_day5 = array_merge($exercises_day5, $exercise);
                    }
                }

                // Выводим результаты поиска для пятого дня
                echo '<div class="results-container">';
                echo '<label>Пятый тренировочный день</label>';
                foreach ($exercises_day5 as $exercise) {
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
            }

            // Формируем массив с тренировочными данными для всех дней
            $training_data = [
                'day1' => $exercises_day1,
                'day2' => $exercises_day2,
            ];

            // Добавляем данные для третьего дня, если он существует
            if (isset($exercises_day3)) {
                $training_data['day3'] = $exercises_day3;
            }

            // Добавляем данные для четвертого дня, если он существует
            if (isset($exercises_day4)) {
                $training_data['day4'] = $exercises_day4;
            }

            // Добавляем данные для пятого дня, если он существует
            if (isset($exercises_day5)) {
                $training_data['day5'] = $exercises_day5;
            }

            // Сформировать строку с названиями упражнений для всех дней
            
            $exercise_plan = "";
            foreach ($training_data as $day => $exercises) {
                $exercise_plan .= $day . ":,"; // Добавляем запятую после двоеточия перед первым упражнением каждого дня
                $exercise_names = array_column($exercises, 'name'); // Получаем массив названий упражнений
                $exercise_plan .= implode(",", $exercise_names) . "\n"; // Объединяем названия упражнений через запятую
            }
            // Проверяем, есть ли уже запись в таблице для этого пользователя
            $stmt = $pdo->prepare("SELECT * FROM training_and_nutrition WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $existing_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_data) {
                // Если запись уже существует, обновляем ее
                $stmt = $pdo->prepare("UPDATE training_and_nutrition SET exercise_plan = ? WHERE user_id = ?");
                $stmt->execute([$exercise_plan, $user_id]);
            } else {
                // Если записи нет, вставляем новую
                $stmt = $pdo->prepare("INSERT INTO training_and_nutrition (user_id, exercise_plan) VALUES (?, ?)");
                $stmt->execute([$user_id, $exercise_plan]);
            }

            // Закрываем соединение с базой данных
            $pdo = null;

            echo "Данные успешно сохранены.";
        } else {
            echo "Пользователь не найден.";
        }
    } catch (PDOException $e) {
        // В случае ошибки выводим сообщение об ошибке
        echo "Ошибка при выполнении запроса: " . $e->getMessage();
    }
}
?>
