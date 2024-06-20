<?php
include 'db_connect.php';

try {
    if (!isset($_POST['category'])) {
        throw new Exception("Категория не указана.");
    }

    $category = $_POST['category'];

    $stmt = $pdo->prepare("SELECT name FROM exercises WHERE category = ?");
    $stmt->execute([$category]);
    $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($exercises);

} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
