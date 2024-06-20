<?php
session_start();

// Параметры подключения к базе данных
$host = "localhost";
$port = "5432";
$dbname = "sportbase";
$user = "postgres";
$password = "12345";

try {
    // Установка соединения с базой данных с использованием PDO
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");

    // Установка режима обработки ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Проверяем, была ли отправлена форма
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Получаем данные из формы
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Проверяем, существует ли уже такое имя пользователя в базе данных
        $stmt_check = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt_check->execute(array(':username' => $username));
        $user_exists = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($user_exists) {
            // Если имя пользователя уже существует, выводим сообщение и просим пользователя выбрать другое имя
            echo "Пользователь с таким именем уже существует. Пожалуйста, выберите другое имя.";
        } else {
            // Хешируем пароль с использованием Sodium
            $hashed_password = sodium_crypto_pwhash_str(
                $password,
                SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
            );

            // Подготовка SQL-запроса для добавления нового пользователя
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')");
            
            // Выполнение запроса
            $stmt->execute(array(':username' => $username, ':password' => $hashed_password));

            // Редирект на главную страницу или другую страницу после успешной регистрации
            header("Location: index.php");
            exit();
        }
    }
} catch (PDOException $e) {
    // В случае ошибки выводим сообщение об ошибке
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="login_styles.css"> <!-- Подключаем файл со стилями -->
</head>
<body>
    
    <div class="login-container">
        <h2>Регистрация</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-group">
                <input type="text" placeholder="Username" id="username" name="username" required>
            </div>
            <div class="input-group">
                <input type="password" placeholder="Passsword" id="password" name="password" required>
            </div>
            <div class="btn-group">
                <button type="submit">Зарегистрироваться</button>
            </div>
        </form>
    </div>
    
</body>
</html>
