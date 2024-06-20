<?php
// Подключение к базе данных
include 'db_connect.php';

try {
    // Получение списка всех таблиц в базе данных
    $tables_query = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    
    // Перебор результатов запроса для каждой таблицы
    while ($table_row = $tables_query->fetch(PDO::FETCH_ASSOC)) {
        $table_name = $table_row['table_name'];
        
        // Вывод названия таблицы
        echo "<h2>Таблица: $table_name</h2>";
        
        // Выполнение запроса SELECT для каждой таблицы, отсортированного по id
        $select_query = $pdo->query("SELECT * FROM $table_name ORDER BY id");
        
        // Вывод данных таблицы
        echo "<table border='1'><tr>";
        
        // Вывод заголовков столбцов
        for ($i = 0; $i < $select_query->columnCount(); $i++) {
            $column_meta = $select_query->getColumnMeta($i);
            echo "<th>{$column_meta['name']}</th>";
        }
        echo "</tr>";
        
        // Вывод данных
        while ($row = $select_query->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table><br>";
    }
} catch (PDOException $e) {
    // Вывод сообщения об ошибке
    echo "Ошибка: " . $e->getMessage();
}
?>
