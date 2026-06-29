<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['user'])) {
    header("Location: ../index.html");
    exit();
}

$rol_actual = $_SESSION['user_type'] ?? 'colaborador';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="../css/menu.css">
</head>

<body>

    <nav class="menu">
        <section class="menu__container">

            <h1 class="menu__logo">
                Galei Airlines
                <img src="../assets/plane-alt.svg" alt="">
            </h1>

            <ul class="menu__links">

                <li class="menu__item">
                    <a href="#" class="menu__link">Home</a>
                </li>

                <li class="menu__item menu__item--show">

                    <a href="#" class="menu__link">About</a>

                    <ul class="menu__nesting">
                        <li class="menu__inside">
                            <a href="#" class="menu__link menu__link--inside">About 1</a>
                        </li>

                        <li class="menu__inside">
                            <a href="#" class="menu__link menu__link--inside">About 2</a>
                        </li>

                        <li class="menu__inside">
                            <a href="#" class="menu__link menu__link--inside">About 3</a>
                        </li>
                    </ul>

                </li>

                <li class="menu__item menu__item--show">

                    <a href="#" class="menu__link">Gestion</a>

                    <ul class="menu__nesting">

                        <li class="menu__inside">
                            <a href="#" class="menu__link menu__link--inside">
                                Gestion de Marcas
                            </a>
                        </li>

                        <li class="menu__inside">
                            <a href="#" class="menu__link menu__link--inside">
                                Gestion de Modelos
                            </a>
                        </li>

                        <li class="menu__inside">
                            <a href="#" class="menu__link menu__link--inside">
                                Gestion de Aviones
                            </a>
                        </li>

                        <li class="menu__inside">
                            <a href="../php/airlineManagement.php" class="menu__link menu__link--inside">
                                Gestion de Aereolíneas
                            </a>
                        </li>

                        <li class="menu__inside">
                            <a href="#" class="menu__link menu__link--inside">
                                Gestion de Vuelos
                            </a>
                        </li>

                        <li class="menu__inside">
                            <a href="#" class="menu__link menu__link--inside">
                                Gestion de Pasajeros
                            </a>
                        </li>

                        <li class="menu__inside">
                            <a href="#" class="menu__link menu__link--inside">
                                Gestion de Tiquetes
                            </a>
                        </li>
                    </ul>

                </li>

                <?php if ($rol_actual === 'administrador'): ?>

                    <li class="menu__item">
                        <a href="#" class="menu__link">
                            Usuario:
                            <?php echo $_SESSION['user']; ?>
                        </a>
                    </li>

                <?php endif; ?>

                <li class="menu__item menu__item--show">

                    <a href="#" class="menu__link">
                        <img src="../assets/menu-closer.svg" alt="" height="75">
                    </a>

                    <ul class="menu__nesting">

                        <?php if ($rol_actual === 'administrador'): ?>

                            <li class="menu__inside">
                                <a href="../php/userManagement.php" class="menu__link menu__link--inside">
                                    Gestion de Usuarios
                                </a>
                            </li>

                            <li class="menu__inside">
                                <a href="#" class="menu__link menu__link--inside">
                                    Historial de tickets
                                </a>
                            </li>

                            <li class="menu__inside">
                                <a href="#" class="menu__link menu__link--inside">
                                    Consultas de vuelo
                                </a>
                            </li>

                        <?php endif; ?>

                        <li class="menu__inside">
                            <a href="#" id="openManual" class="menu__link menu__link--inside">
                                Manual
                            </a>
                        </li>

                        <li class="menu__inside">
                            <a href="../php/logout.php" class="menu__link menu__link--inside">
                                Cerrar sesión
                            </a>
                        </li>

                    </ul>

                </li>

            </ul>

        </section>
    </nav>

    <section class="info">

        <div class="slide_img">
            <h1 id="title">
                Bienvenido
                <?php echo $_SESSION['user']; ?>
            </h1>
        </div>

    </section>

    <br><br><br><br><br><br><br><br><br><br>

    <footer class="footer">
        <p>
            © 2026 Mi sitio web. Todos los derechos reservados.
        </p>
    </footer>

    <script src="../js/app.js"></script>

</body>

</html>