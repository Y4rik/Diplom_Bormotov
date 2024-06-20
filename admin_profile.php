<?php include 'db_connect.php'; ?>
<?php
session_start();

// Обработка добавления новой записи
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_record'])) {
    $table = $_POST["table"];
    $columns = array();
    $values = array();
    foreach ($_POST as $key => $value) {
        if ($key != "table" && $key != "add_record") {
            $value = (!empty($value)) ? $pdo->quote($value) : 'NULL';
            $columns[] = $key;
            $values[] = $value;
        }
    }
    $column_list = implode(", ", $columns);
    $value_list = implode(", ", $values);
    $insert_query = "INSERT INTO $table ($column_list) VALUES ($value_list)";
    $pdo->query($insert_query);
    // Перезагружаем страницу для отображения изменений
    echo "<meta http-equiv='refresh' content='0'>";
}

// Обработка действий: редактирование, удаление, добавление записей
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["edit"])) {
        $table = $_POST["table"];
        $id = $_POST["id"];
        $column = $_POST["column"];
        $value = $_POST["value"];
        $update_query = "UPDATE $table SET $column='$value' WHERE id=$id";
        $pdo->query($update_query);
        echo "<meta http-equiv='refresh' content='0'>";
    } elseif (isset($_POST["delete"])) {
        $table = $_POST["table"];
        $id = $_POST["id"];

        // Удаляем связанные записи
        if ($table == 'users') {
            $delete_related_query = "DELETE FROM training_and_nutrition WHERE user_id=$id";
            $pdo->query($delete_related_query);
        }

        // Удаляем запись из основной таблицы
        $delete_query = "DELETE FROM $table WHERE id=$id";
        $pdo->query($delete_query);
        echo "<meta http-equiv='refresh' content='0'>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль администратора</title>
    <link rel="stylesheet" href="user_styles.css"> <!-- Подключаем файл со стилями -->

    <style>
        .table-container {
            max-height: 600px;
            overflow-y: auto;
        }
        .form-container {
            display: none;
            margin-top: 20px;
        }
        .table-container input[type="text"] {
            transition: all 0.3s ease-in-out;
            width: 100%;
            box-sizing: border-box; /* This ensures padding and borders are included in the width */
        }
        .table-container input[type="text"]:focus {
            transform: scale(1.1);
            width: 120%;
            z-index: 1;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?> <!-- Подключаем шапку сайта -->

    <div class="main-content">
        <h1>Профиль администратора</h1>

        <?php
        // Получаем список таблиц в базе данных
        $tables_query = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        $tables = [];
        while ($row = $tables_query->fetch(PDO::FETCH_ASSOC)) {
            $tables[] = $row['table_name'];
        }
        ?>

        <form method="get" action="">
            <label for="table_select">Выберите таблицу:</label>
            <select id="table_select" name="table">
                <?php foreach ($tables as $table): ?>
                    <option value="<?php echo $table; ?>" <?php echo (isset($_GET['table']) && $_GET['table'] == $table) ? 'selected' : ''; ?>><?php echo $table; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Показать">
        </form>

        <?php
        if (isset($_GET['table'])) {
            $selected_table = $_GET['table'];
            echo "<h2>$selected_table</h2>";

            // Получаем список столбцов выбранной таблицы
            $columns_query = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = '$selected_table'");
            $columns = [];
            $data_types = [];
            while ($row = $columns_query->fetch(PDO::FETCH_ASSOC)) {
                $columns[] = $row['column_name'];
                $data_types[$row['column_name']] = $row['data_type'];
            }

            echo "<form method='get' action=''>";
            echo "<input type='hidden' name='table' value='$selected_table'>";
            echo "<label for='column_select'>Выберите столбец:</label>";
            echo "<select id='column_select' name='column'>";
            foreach ($columns as $column) {
                echo "<option value='$column'>" . $column . "</option>";
            }
            echo "</select>";
            echo "<label for='search_value'>Введите значение для поиска:</label>";
            echo "<input type='text' id='search_value' name='search_value'>";
            echo "<input type='submit' value='Поиск'>";
            echo "</form>";

            echo "<button onclick='toggleForm()'>Добавить новую запись</button>";
            echo "<div id='form-container' class='form-container'>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='table' value='$selected_table'>";
            foreach ($columns as $column) {
                echo "<input type='text' name='{$column}' placeholder='{$column}'>";
            }
            echo "<input type='submit' name='add_record' value='Добавить запись'>";
            echo "</form>";
            echo "</div>";

            // Проверяем, есть ли параметры поиска
            $where_clause = '';
            if (isset($_GET['column']) && isset($_GET['search_value'])) {
                $search_column = $_GET['column'];
                $search_value = $_GET['search_value'];
                $data_type = $data_types[$search_column];
                if ($data_type == 'character varying' || $data_type == 'text') {
                    $where_clause = "WHERE $search_column ILIKE '%$search_value%'";
                } else {
                    $where_clause = "WHERE CAST($search_column AS TEXT) ILIKE '%$search_value%'";
                }
            }

            echo "<div class='table-container'>";
            $stmt = $pdo->query("SELECT * FROM $selected_table $where_clause ORDER BY 1");
            echo "<table>";
            echo "<tr>";
            for ($i = 0; $stmt && $i < $stmt->columnCount(); $i++) {
                $column = $stmt->getColumnMeta($i);
                echo "<th>{$column['name']}</th>";
            }
            echo "<th>Действия</th>";
            echo "</tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<td>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='table' value='$selected_table'>";
                    echo "<input type='hidden' name='id' value='{$row['id']}'>";
                    echo "<input type='hidden' name='column' value='$key'>";
                    echo "<input type='text' name='value' value='$value'>";
                    echo "<input type='submit' name='edit' value='Сохранить'>";
                    echo "</form>";
                    echo "</td>";
                }
                echo "<td>";
                echo "<form method='post'>";
                echo "<input type='hidden' name='table' value='$selected_table'>";
                echo "<input type='hidden' name='id' value='{$row['id']}'>";
                echo "<input type='submit' name='delete' value='Удалить'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        }
        ?>

    </div>

    <footer class="footer">
        <p>© 2024 Спортивный сайт</p>
    </footer>

    <script>
        function toggleForm() {
            var formContainer = document.getElementById('form-container');
            if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                formContainer.style.display = 'block';
            } else {
                formContainer.style.display = 'none';
            }
        }
    </script>
</body>
</html>
