<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Спортивный сайт</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключаем файл со стилями -->
    <!-- Подключаем библиотеку jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Подключаем библиотеку для слайдера -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css"/>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?> <!-- Подключаем шапку сайта -->
    <div class="header_img">
    </div>
    <div class="main-content">
        <div class="title-container">
            <h1>Наши возможности</h1>
        </div>
        <!-- Блок-контейнер для слайдера -->
        <div class="block-container">
            <div class="block">
                <h2>Индивидуальный план</h2>
                <img src="images/free-icon-weightlifting-2738793.png" alt="Картинка 1">
                <p>Создание уникального плана тренировок для пользователей</p>
            </div>
            <div class="block">
                <h2>Библиотека упражнений</h2>
                <img src="images/free-icon-open-book-4797975.png" alt="Картинка 2">
                <p>Доступный поиск любого упражнения из встроенной библиотеки</p>
            </div>
            <div class="block">
                <h2>Библиотека продуктов питания</h2>
                <img src="images/apple_food_fruit_icon_182561.png" alt="Картинка 3">
                <p>Информация о продуктах питания и их калорийности</p>
            </div>
            <div class="block">
                <h2>Составление плана тренировок</h2>
                <img src="images/free-icon-calendar-1827274.png" alt="Картинка 4">
                <p>Составление плана для тренировок каждого пользователя</p>
            </div>
            <div class="block">
                <h2>Составление индивидуального плана питания</h2>
                <img src="images/-meal_89750.png" alt="Картинка 5">
                <p>Составление плана питания для каждого пользователя</p>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>© 2024 Спортивный сайт</p>
    </footer>

    <!-- Скрипт для инициализации слайдера -->
    <script>
        $(document).ready(function(){
            $('.block-container').slick({
                slidesToShow: 3, // Показывать 3 слайда одновременно
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000, // Интервал автопрокрутки в миллисекундах
                arrows: false, // Отображение стрелок для переключения
                dots: false, // Отключение точек для навигации
                centerMode: true, // Включение центрального режима
                focusOnSelect: true, // При выборе слайда фокус будет на нем
                variableWidth: true // Позволяет использовать переменную ширину слайдов
            });
        });
    </script>
</body>
</html>
