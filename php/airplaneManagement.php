  <?php
session_start();
include("../php/connection.php");

// ===== CREAR =====
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['createAirplane'])) {
    $plate = $_POST['registration_mark'];
    $model = $_POST['cod_model'];
    $airline = $_POST['cod_airline'];
    $year = $_POST['yom'];

    $check = "SELECT * FROM airplane WHERE registration_mark='$plate'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        echo "
            <script>alert('Ya existe esa matrícula'); 
            window.location='" . $_SERVER['PHP_SELF'] . "'; 
            </script>";
        exit();
    }
    $insert ="INSERT INTO airplane VALUES('$plate','$model','$airline','$year')";
    $conn->query( $insert);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// ===== ACTUALIZAR =====

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateAirplane'])) {
    $old = $_POST['oldPlate'];
    $new = $_POST['registration_mark'];
    $model = $_POST['cod_model'];
    $airline = $_POST['cod_airline'];
    $year = $_POST['yom'];

    $check ="SELECT * FROM airplane WHERE registration_mark='$new' AND registration_mark<>'$old'";

    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        echo "
        <script>alert('Matrícula repetida');
        window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";
        exit();
    }
    $update = "UPDATE airplane SET registration_mark='$new',cod_model='$model',cod_airline='$airline',yom='$year' WHERE registration_mark='$old'";

    $conn->query($update);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ===== ELIMINAR =====
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteAirplane'])) {
    $id = $_POST['airplaneID'];
    $check = "SELECT * FROM flight WHERE registration_mark='$id'";

    $result =$conn->query($check);
    if ($result->num_rows > 0) {
        echo "
        <script>alert('No puede eliminarse porque tiene vuelos');
        window.location='" . $_SERVER['PHP_SELF'] . "'; 
        </script>";
        exit();
    }
    $delete ="DELETE FROM airplane WHERE registration_mark='$id'";
    $conn->query($delete);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// ===== BUSCAR =====
$search = "";
if (isset($_GET['search'])&&!empty($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM airplane WHERE registration_mark LIKE '%$search%'";
} else {
    $query ="SELECT * FROM airplane";
}
$result = $conn->query($query);
$models = $conn->query("SELECT * FROM model");

$airlines = $conn->query("SELECT * FROM airline");
?>
<html>
<head>
    <title>Gestión Aviones</title>
    <link rel="stylesheet" href="../css/airplaneManagement.css">
</head>
<body>
    <h1>Gestión de Aviones</h1>
    <form method="GET">
        <input name="search">
        <button>Buscar</button>
    </form>

    <table>
        <tr>
            <th>Matrícula</th>
            <th>Modelo</th>
            <th>Aerolínea</th>
            <th>Año</th>
            <th>Guardar</th>
            <th>Eliminar</th>
        </tr>

        <tr>
            <form method="POST" onsubmit="return confirm('¿Crear avión?')">
                <td><input name="registration_mark" required></td>
                <td><select name="cod_model">
                        <?php
                        while ($m =$models->fetch_assoc()) {
                            ?>
                            <option value="<?= $m['cod_model'] ?>"> <?= $m['name'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>

                <td>
                    <select name="cod_airline">
                        <?php

                        while ($a = $airlines->fetch_assoc()) {
                            ?>
                            <option value="<?= $a['cod_airline'] ?>"> <?= $a['name'] ?> </option>
                            <?php
                        }
                        ?>
                    </select>
                </td>

                <td><input type="number" name="yom" required></td>
                <td><button name="createAirplane"> Crear </button> </td>
                <td><button type="reset"> Limpiar </button>

                </td>
            </form>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            ?>
            <tr>
                <form method="POST" onsubmit="return confirm('¿Guardar cambios?')">
                    <input type="hidden" name="oldPlate" value="<?= $row['registration_mark'] ?>">
                    <td><input name="registration_mark" value="<?= $row['registration_mark'] ?>"></td>
                    <td><input name="cod_model" value="<?= $row['cod_model'] ?>"> </td>
                    <td><input name="cod_airline" value="<?= $row['cod_airline'] ?>"></td>
                    <td><input name="yom" value="<?= $row['yom'] ?>"></td>
                    <td><button name="updateAirplane">Guardar</button></td>
                </form>

                <td>
                    <form method="POST" onsubmit="return confirm('¿Eliminar avión?')">
                        <input type="hidden" name="airplaneID" value="<?= $row['registration_mark'] ?>">
                        <button name="deleteAirplane"> Eliminar </button>
                    </form>
                </td>
            </tr>
            <?php
        }
        $conn->close();
        ?>
    </table>
</body>
</html>