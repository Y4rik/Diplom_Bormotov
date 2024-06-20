<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="user_styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="main-content">
        <h1>Профиль пользователя</h1>
        <div class="profile-block">
            <img src="images/free-icon-sportsman-2906401.png" alt="User Avatar">
            <p><?php echo $_SESSION['username']; ?></p>
        </div>
        <div class="button-block">
            <button class="user-button" onclick="toggleForm()">Внести/изменить данные</button>
            <button class="user-button" onclick="toggleWorkoutForm()">Рассчитать тренировку</button>
            <button class="user-button" onclick="loadSavedWorkout()">Сохраненная тренировка</button>
            <button class="user-button" onclick="toggleDietForm()">Составление меню</button>
            <button class="user-button" onclick="loadSavedDiet()">Сохраненное питание</button> <!-- Новая кнопка -->
        </div>

        <div id="data-form" style="display: none;">
            <form action="save_data.php" method="post">
                <input type="hidden" name="username" value="<?php echo $_SESSION['username']; ?>">
                <div class="form-group">
                    <label for="user_height">Рост в сантиметрах:</label>
                    <input type="text" id="user_height" name="user_height" required>
                </div>

                <div class="form-group">
                    <label for="user_weight">Вес в килограммах:</label>
                    <input type="text" id="user_weight" name="user_weight" required>
                </div>

                <div class="form-group">
                    <label for="age">Возраст:</label>
                    <input type="text" id="age" name="age" required>
                </div>
                
                <div class="form-group">
                    <label for="gender">Пол:</label>
                    <select id="gender" name="gender" required>
                        <option value="male">Мужчина</option>
                        <option value="female">Женщина</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="user_aim">Цель:</label>
                    <select id="user_aim" name="user_aim">
                        <option value="build_muscle">Нарастить мышечную массу</option>
                        <option value="maintain_weight">Поддерживать вес</option>
                        <option value="lose_weight">Сбросить вес</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="activity">Уровень активности:</label>
                    <select id="activity" name="activity">
                        <option value="sedentary">Минимальная активность</option>
                        <option value="moderate">Умеренный уровень активности</option>
                        <option value="active">Тяжелая или трудоемкая активность</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="location">Место занятий:</label>
                    <select id="location" name="location">
                        <option value="Дом">Дом</option>
                        <option value="Зал">Зал</option>
                    </select>
                </div>

                <button type="submit">Сохранить</button>
            </form>
        </div>

        <div id="workout-form" style="display: none;">
            <form id="calculate-workout-form">
                <div class="form-group">
                    <label for="training_days">Количество дней тренировки в неделю:</label>
                    <select id="training_days" name="training_days">
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <button type="submit">Подтвердить</button>
            </form>
        </div>
        
        <div id="diet-form" style="display: none;">
            <form id="calculate-diet-form">
                <button type="button" onclick="configureMeals()">Настроить питание</button>
                <button type="button" onclick="calculateMeals()">Рассчитать</button>
            </form>
        </div>
        
        <div class="results-container">
            <!-- Результаты поиска и операций -->
        </div>
    </div>
    
    <footer class="footer">
        <p>© 2024 Спортивный сайт</p>
    </footer>

    <script>
        function toggleForm() {
            var form = document.getElementById('data-form');
            var workoutForm = document.getElementById('workout-form');
            var dietForm = document.getElementById('diet-form');
            hideResults();
            if (form.style.display === 'none') {
                form.style.display = 'block';
                workoutForm.style.display = 'none';
                dietForm.style.display = 'none';
            } else {
                form.style.display = 'none';
            }
        }

        function toggleWorkoutForm() {
            var form = document.getElementById('workout-form');
            var dataForm = document.getElementById('data-form');
            var dietForm = document.getElementById('diet-form');
            hideResults();
            if (form.style.display === 'none') {
                form.style.display = 'block';
                dataForm.style.display = 'none';
                dietForm.style.display = 'none';
            } else {
                form.style.display = 'none';
            }
        }

        function toggleDietForm() {
            var form = document.getElementById('diet-form');
            var dataForm = document.getElementById('data-form');
            var workoutForm = document.getElementById('workout-form');
            hideResults();
            if (form.style.display === 'none') {
                form.style.display = 'block';
                dataForm.style.display = 'none';
                workoutForm.style.display = 'none';
            } else {
                form.style.display = 'none';
            }
        }

        function loadSavedWorkout() {
            hideAllForms();
            $.ajax({
                type: 'POST',
                url: 'load_saved_workout.php',
                success: function(response) {
                    $('.results-container').html(response);
                }
            });
        }

        function loadSavedDiet() {
            hideAllForms();
            $.ajax({
                type: 'POST',
                url: 'load_saved_diet.php',
                success: function(response) {
                    $('.results-container').html(response);
                }
            });
        }

        function hideAllForms() {
            var formData = document.getElementById('data-form');
            var workoutForm = document.getElementById('workout-form');
            var dietForm = document.getElementById('diet-form');
            formData.style.display = 'none';
            workoutForm.style.display = 'none';
            dietForm.style.display = 'none';
        }

        function hideResults() {
            $('.results-container').html('');
        }

        function configureMeals() {
            $.ajax({
                type: 'POST',
                url: 'meals_op.php',
                success: function(response) {
                    $('.results-container').html(response);
                }
            });
        }

        function calculateMeals() {
            $.ajax({
                type: 'POST',
                url: 'calculate_meals.php',
                success: function(response) {
                    $('.results-container').html(response);
                }
            });
        }

        $(document).ready(function() {
            $('#calculate-workout-form').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'calculate_workout2.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('.results-container').html(response);
                    }
                });
            });
        });

        function deleteExercise(day, exercise) {
            $.ajax({
                type: 'POST',
                url: 'delete_exercise.php',
                data: { day: day, exercise: exercise },
                success: function(response) {
                    loadSavedWorkout();
                }
            });
        }

        function replaceExercise(day, oldExercise, newExercise) {
            if (!newExercise) return;
            $.ajax({
                type: 'POST',
                url: 'replace_exercise.php',
                data: { day: day, oldExercise: oldExercise, newExercise: newExercise },
                success: function(response) {
                    loadSavedWorkout();
                }
            });
        }

        function addExercise(day, newExercise) {
            if (!newExercise) return;
            $.ajax({
                type: 'POST',
                url: 'add_exercise.php',
                data: { day: day, newExercise: newExercise },
                success: function(response) {
                    loadSavedWorkout();
                }
            });
        }

        function deleteMeal(day, meal) {
            $.ajax({
                type: 'POST',
                url: 'delete_meal.php',
                data: { day: day, meal: meal },
                success: function(response) {
                    loadSavedDiet();
                }
            });
        }

        function replaceMeal(day, oldMeal, newMeal) {
            if (!newMeal) return;
            $.ajax({
                type: 'POST',
                url: 'replace_meal.php',
                data: { day: day, oldMeal: oldMeal, newMeal: newMeal },
                success: function(response) {
                    loadSavedDiet();
                }
            });
        }

        function addMeal(day, newMeal) {
            if (!newMeal) return;
            $.ajax({
                type: 'POST',
                url: 'add_meal.php',
                data: { day: day, newMeal: newMeal },
                success: function(response) {
                    loadSavedDiet();
                }
            });
        }
    </script>
</body>
</html>
