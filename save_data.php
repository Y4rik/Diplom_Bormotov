<!-- save_data.php -->
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Подключаемся к базе данных
    include 'db_connect.php';

    // Получаем данные из формы
    $username = $_POST['username'];
    $user_height = $_POST['user_height'];
    $user_weight = $_POST['user_weight'];
    $user_aim = $_POST['user_aim'];
    $location = $_POST['location'];
    $age = $_POST['age'];
    $activity = $_POST['activity'];
    $gender = $_POST['gender'];

    try {
        // Проверяем существует ли пользователь с таким именем
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Если пользователь существует, обновляем его данные
            $stmt = $pdo->prepare("UPDATE users SET user_height = ?, user_weight = ?, user_aim = ?, location = ?, activity = ?, age = ?, gender = ? WHERE username = ?");
            $stmt->execute([$user_height, $user_weight, $user_aim, $location, $activity, $age, $gender, $username]);
        } else {
            // Если пользователь не существует, создаем новую запись
            $stmt = $pdo->prepare("INSERT INTO users (username, user_height, user_weight, user_aim, location, activity, age, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $user_height, $user_weight, $user_aim, $location, $activity, $age, $gender]);
        }

        // Закрываем соединение с базой данных
        $pdo = null;

        // Перенаправляем пользователя обратно на страницу профиля
        header("Location: user_profile.php");
        exit();
    } catch (PDOException $e) {
        // В случае ошибки выводим сообщение об ошибке
        echo "Ошибка при сохранении данных: " . $e->getMessage();
    }
}
?>
