<!-- db_connect.php -->
<?php
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
} catch (PDOException $e) {
    // В случае ошибки выводим сообщение об ошибке
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
}
?>