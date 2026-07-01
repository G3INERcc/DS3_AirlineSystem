<?php

session_start();
include("../php/connection.php");

// Seguridad
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'administrador') {
    header("Location: ../index.html");
    exit();
}
// ACTUALIZAR

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateAirline'])) {
    $originalCode = $_POST['originalCode'];
    $newCode = $_POST['newCode'];
    $newName = trim($_POST['newName']);
    $newCountry = trim($_POST['newCountry']);

    $checkCode = "SELECT cod_airline FROM airline WHERE cod_airline='$newCode' AND cod_airline<>'$originalCode'";

    $resultCode = $conn->query($checkCode);

    $checkName = "SELECT cod_airline FROM airline WHERE name='$newName' AND cod_airline<>'$originalCode'";

    $resultName = $conn->query($checkName);

    if ($resultCode->num_rows > 0) {
        echo "<script>
            alert('El código de la aerolínea ya existe.');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";
        exit();
    }

    if ($resultName->num_rows > 0) {
        echo "<script>
            alert('Ya existe una aerolínea con ese nombre.');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";
        exit();
    }

    $update = "UPDATE airline SET cod_airline='$newCode', name='$newName', country='$newCountry' WHERE cod_airline='$originalCode'";

    $conn->query($update);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// ELIMINAR

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&isset($_POST['deleteAirline'])) {
    $code = $_POST['airlineCode'];
    $checkAirplanes = "SELECT registration_mark FROM airplane WHERE cod_airline='$code'";

    $resultAirplanes = $conn->query($checkAirplanes);

    if ($resultAirplanes->num_rows > 0) {
        echo "<script>
            alert('No se puede eliminar la aerolínea porque tiene aviones registrados.');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";
        exit();
    }
    $delete = "DELETE FROM airline WHERE cod_airline='$code'";

    $conn->query($delete);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// BUSCAR

$search = "";

if (isset($_GET['search']) &&!empty($_GET['search'])) {
    $search = $_GET['search'];

    $query = "SELECT * FROM airline WHERE name LIKE '%$search%' ORDER BY cod_airline";
} else {

    $query = "SELECT * FROM airline ORDER BY cod_airline";
}
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Aerolíneas</title>
    <link rel="stylesheet" href="../css/airlineManager.css">
</head>
<body>
    <header>
        <span>Galei Airlines</span>
        <a href="../php/menu.php"> <img src="../assets/2.svg" alt=""> </a>
    </header>

    <main class="container">
        <h1>Gestión de Aerolíneas</h1>

        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="search-input">
                <button type="submit" class="btn-search"> Buscar </button>
                <button type="button" class="btn-search" onclick="window.location.href='../html/createAirline.html'"> Crear Aerolínea</button>
            </form>
        </div>

        <section class="airlineManagement">
            <div class="infoAirlines">
                <table class="users-table">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>País</th>
                        <th>Guardar</th>
                        <th>Eliminar</th>
                    </tr>

                        <?php

                        if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { 
                            ?>
                            <tr>
                                <form method="POST" onsubmit="return confirm('¿Guardar cambios?');">
                                    <input type="hidden" name="originalCode" value="<?= $row['cod_airline'] ?>">
                                        <td> <input type="number" name="newCode" value="<?= $row['cod_airline'] ?>" class="input-table-id"></td>
                                        <td> <input type="text" name="newName" value="<?= htmlspecialchars($row['name']) ?>" class="input-table-user"> </td>
                                        <td> <input type="text" name="newCountry" value="<?= htmlspecialchars($row['country']) ?>" class="input-table-user"> </td>
                                        <td> <button type="submit" name="updateAirline" class="btn-save"> Guardar </button></td>
                                </form>

                                <td>
                                    <form method="POST" onsubmit="return confirm('¿Eliminar aerolínea?');">
                                        <input type="hidden" name="airlineCode" value="<?= $row['cod_airline'] ?>">
                                        <button type="submit" name="deleteAirline" class="btn-delete"> Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr> <td colspan="5"> No se encontraron aerolíneas. </td> </tr>
                        <?php
                        }
                        $conn->close();
                        ?>
                </table>
            </div>
        </section>
    </main>
</body>
</html>