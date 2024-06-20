<!-- header.php -->
<?php
session_start();

// Проверяем, установлен ли сеанс с именем пользователя
if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $role = $_SESSION['role'];
    if ($role == 'admin') {
        $profile_link = 'admin_profile.php';
    } elseif ($role == 'user') {
        $profile_link = 'user_profile.php';
    }
    $logout_link = 'logout.php';
} else {
    // Если сеанс не установлен, пользователь не авторизован
    $username = 'Войти';
    $profile_link = 'login.php';
    $logout_link = '';
}
?>

<header>
    <nav>
        <ul>
            <li><a href="index.php">Главная</a></li>
            <li><a href="exercises.php">Упражнения</a></li>
            <li><a href="products.php">Питание</a></li>
            <?php if (isset($_SESSION['username']) && !empty($_SESSION['username'])): ?>
                <li><a href="<?php echo $profile_link; ?>"><?php echo $username; ?></a></li>
                <li><a href="<?php echo $logout_link; ?>">Выйти</a></li>
            <?php else: ?>
                <li><a href="login.php"><?php echo $username; ?></a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
