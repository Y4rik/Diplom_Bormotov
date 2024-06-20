<?php
session_start();

// Подключение к базе данных
require_once 'db_connect.php';

// Проверяем, была ли установлена сессия с именем пользователя
if (isset($_SESSION['username'])) {
    // Получаем имя пользователя из сессии
    $username = $_SESSION['username'];

    // Проверяем, были ли переданы данные из формы
    if (isset($_POST['options'])) {
        // Получаем выбранные опции из формы
        $selectedOptions = $_POST['options'];

        try {
            // Получаем уже имеющиеся данные пользователя из базы данных
            $query = "SELECT other_info FROM users WHERE username = :username";
            $statement = $pdo->prepare($query);
            $statement->bindParam(':username', $username);
            $statement->execute();
            $existingData = $statement->fetchColumn();

            // Если нет существующих данных, устанавливаем пустую строку
            if ($existingData === null) {
                $existingData = '';
            }

            // Разделяем уже имеющиеся данные на массив
            $existingOptions = explode(",", $existingData);

            // Добавляем только новые опции к уже имеющимся данным
            foreach ($selectedOptions as $option) {
                $option = trim($option);
                if (!in_array($option, $existingOptions)) {
                    $existingOptions[] = $option;
                }
            }

            // Формируем новую строку с данными для обновления в базе данных
            $optionsTxt = implode(",", $existingOptions);

            // Обновляем запись пользователя в базе данных с новой информацией
            $updateQuery = "UPDATE users SET other_info = :other_info WHERE username = :username";
            $updateStatement = $pdo->prepare($updateQuery);
            $updateStatement->bindParam(':other_info', $optionsTxt);
            $updateStatement->bindParam(':username', $username);
            $updateStatement->execute();

            // Отправляем ответ клиенту об успешном обновлении данных
            echo "Данные успешно сохранены.";
        } catch (PDOException $e) {
            // В случае ошибки выводим сообщение об ошибке
            echo "Ошибка обновления данных: " . $e->getMessage();
        }
    } else {
        // Выводим сообщение об ошибке, если данные не были переданы из формы
        echo "Ошибка: данные не были переданы из формы.";
    }
} else {
    // Выводим сообщение об ошибке, если сессия с именем пользователя не была установлена
    echo "Ошибка: сессия с именем пользователя не была установлена.";
}
?>
