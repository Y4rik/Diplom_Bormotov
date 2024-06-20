<?php
session_start();

// Параметры подключения к базе данных
$host = "localhost";
$port = "5432";
$dbname = "sportbase";
$user = "postgres";
$password = "12345";

// Устанавливаем соединение с базой данных
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Проверяем наличие данных POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем введенные пользователем данные
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Подготавливаем SQL-запрос для проверки наличия администраторов с указанным именем пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND role like 'admin'");
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверяем, есть ли администратор с указанным именем пользователя
    if ($admin) {
        // Проверяем совпадение пароля
        if (sodium_crypto_pwhash_str_verify($admin['password'], $password)) {
            // Аутентификация прошла успешно
            // Создаем сессию
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = $admin['role'];

            // Перенаправляем администратора на страницу admin_profile.php
            header("Location: admin_profile.php");
            exit();
        } else {
            // Неправильный пароль
            echo "Неправильный пароль.";
            exit();
        }
    }

    // Подготавливаем SQL-запрос для проверки наличия пользователя с указанным именем пользователя (исключая администраторов)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND role not like 'admin'");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверяем, найден ли пользователь
    if ($user) {
        // Проверяем совпадение пароля
        if (sodium_crypto_pwhash_str_verify($user['password'], $password)) {
            // Аутентификация прошла успешно
            // Создаем сессию
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Перенаправляем пользователя на страницу user_profile.php
            header("Location: user_profile.php");
            exit();
        } else {
            // Неправильный пароль
            echo "Неправильный пароль.";
            exit();
        }
    } else {
        // Пользователь с указанным именем пользователя не найден
        echo "Пользователь с указанным именем пользователя не найден.";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="login_styles.css"> <!-- Подключаем файл со стилями -->
</head>
<body>
    
    <div class="login-container">
        <h2>Авторизация</h2>
        <form action="" method="post">
            <div class="input-group">
                <input type="text" placeholder="Username" id="username" name="username" required>
            </div>
            <div class="input-group">
                <input type="password" placeholder="Password" id="password" name="password" required>
            </div>
            <div class="btn-group">
                <button type="submit">Вход</button>
                <button type="button" onclick="window.location.href='register.php'">Регистрация</button>
            </div>
        </form>
    </div>
    
</body>
</html>
