<?php

session_start();
include("../php/connection.php");

// ========= CARGAR VUELO PARA EDITAR =========

$editMode = false;
$editFlight = null;

if (isset($_GET['edit'])) {

    $editID = $_GET['edit'];

    $editQuery = "
        SELECT *
        FROM flight
        WHERE num_flight='$editID'
    ";

    $editResult = $conn->query($editQuery);

    if ($editResult && $editResult->num_rows > 0) {
        $editMode = true;
        $editFlight = $editResult->fetch_assoc();
    }
}

// ========= CREAR VUELO =========

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['createFlight'])
) {

    $registrationMark = $_POST['registration_mark'];
    $departureCity = $_POST['departure_city'];
    $arrivalCity = $_POST['arrival_city'];
    $departureDateTime = $_POST['departure_date_time'];
    $arrivalDateTime = $_POST['arrival_date_time'];
    $businessPrice = $_POST['business_price'];
    $turistPrice = $_POST['turist_price'];
    $economyPrice = $_POST['economy_price'];

    if ($arrivalDateTime <= $departureDateTime) {

        echo "
        <script>
            alert('La fecha de llegada debe ser posterior a la fecha de salida');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";

        exit();
    }

    $check = "
        SELECT *
        FROM flight
        WHERE registration_mark='$registrationMark'
        AND ABS(TIMESTAMPDIFF(HOUR, departure_date_time, '$departureDateTime')) < 24
    ";

    $resultCheck = $conn->query($check);

    if ($resultCheck->num_rows > 0) {

        echo "
        <script>
            alert('Este avión ya tiene un vuelo registrado en un rango menor a 24 horas');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";

        exit();
    }

    $insert = "
        INSERT INTO flight
        (
            registration_mark,
            departure_city,
            arrival_city,
            departure_date_time,
            arrival_date_time,
            business_price,
            turist_price,
            economy_price
        )
        VALUES
        (
            '$registrationMark',
            '$departureCity',
            '$arrivalCity',
            '$departureDateTime',
            '$arrivalDateTime',
            '$businessPrice',
            '$turistPrice',
            '$economyPrice'
        )
    ";

    $conn->query($insert);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ========= ACTUALIZAR VUELO =========

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['updateFlight'])
) {

    $oldFlight = $_POST['oldFlight'];
    $registrationMark = $_POST['registration_mark'];
    $departureCity = $_POST['departure_city'];
    $arrivalCity = $_POST['arrival_city'];
    $departureDateTime = $_POST['departure_date_time'];
    $arrivalDateTime = $_POST['arrival_date_time'];
    $businessPrice = $_POST['business_price'];
    $turistPrice = $_POST['turist_price'];
    $economyPrice = $_POST['economy_price'];

    if ($arrivalDateTime <= $departureDateTime) {

        echo "
        <script>
            alert('La fecha de llegada debe ser posterior a la fecha de salida');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";

        exit();
    }

    $check = "
        SELECT *
        FROM flight
        WHERE registration_mark='$registrationMark'
        AND num_flight<>'$oldFlight'
        AND ABS(TIMESTAMPDIFF(HOUR, departure_date_time, '$departureDateTime')) < 24
    ";

    $resultCheck = $conn->query($check);

    if ($resultCheck->num_rows > 0) {

        echo "
        <script>
            alert('Este avión ya tiene otro vuelo registrado en un rango menor a 24 horas');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";

        exit();
    }

    $update = "
        UPDATE flight
        SET
            registration_mark='$registrationMark',
            departure_city='$departureCity',
            arrival_city='$arrivalCity',
            departure_date_time='$departureDateTime',
            arrival_date_time='$arrivalDateTime',
            business_price='$businessPrice',
            turist_price='$turistPrice',
            economy_price='$economyPrice'
        WHERE num_flight='$oldFlight'
    ";

    $conn->query($update);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ========= ELIMINAR VUELO =========

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['deleteFlight'])
) {

    $numFlight = $_POST['flightID'];

    $delete = "
        DELETE FROM flight
        WHERE num_flight='$numFlight'
    ";

    $conn->query($delete);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ========= BUSCAR =========

$search = "";

if (
    isset($_GET['search']) &&
    !empty($_GET['search'])
) {

    $search = $_GET['search'];

    $query = "
        SELECT *
        FROM flight
        WHERE num_flight LIKE '%$search%'
        OR departure_city LIKE '%$search%'
        OR arrival_city LIKE '%$search%'
        OR registration_mark LIKE '%$search%'
        ORDER BY departure_date_time
    ";
} else {

    $query = "
        SELECT *
        FROM flight
        ORDER BY departure_date_time
    ";
}

$result = $conn->query($query);

// ========= CARGAR AVIONES =========

$airplanesQuery = "
    SELECT registration_mark
    FROM airplane
    ORDER BY registration_mark
";

$airplanesResult = $conn->query($airplanesQuery);

$airplanes = [];

while ($airplane = $airplanesResult->fetch_assoc()) {
    $airplanes[] = $airplane['registration_mark'];
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Vuelos</title>
    <link rel="stylesheet" href="../css/flightManager.css">
</head>

<body>

    <header>
        <span>Galei Airlines</span>

        <a href="../php/menu.php">
            Regresar
        </a>
    </header>

    <main class="container">

        <h1>Gestión de Vuelos</h1>

        <section class="card">

            <h2>
                <?= $editMode ? "Editar Vuelo" : "Registrar Nuevo Vuelo" ?>
            </h2>

            <form method="POST" class="flight-form" onsubmit="return confirm('¿Guardar vuelo?');">

                <?php if ($editMode) { ?>
                    <input type="hidden" name="oldFlight" value="<?= $editFlight['num_flight'] ?>">
                <?php } ?>

                <div class="form-group">
                    <label>Avión</label>

                    <select name="registration_mark" required>
                        <option value="">-- Seleccione un avión --</option>

                        <?php foreach ($airplanes as $mark) { ?>
                            <option value="<?= $mark ?>"
                                <?= $editMode && $editFlight['registration_mark'] == $mark ? "selected" : "" ?>>
                                <?= $mark ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Ciudad de Salida</label>

                    <input
                        type="text"
                        name="departure_city"
                        placeholder="Ej. San José, CR"
                        value="<?= $editMode ? htmlspecialchars($editFlight['departure_city']) : '' ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Ciudad de Llegada</label>

                    <input
                        type="text"
                        name="arrival_city"
                        placeholder="Ej. Bogotá, CO"
                        value="<?= $editMode ? htmlspecialchars($editFlight['arrival_city']) : '' ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Fecha y Hora de Salida</label>

                    <input
                        type="datetime-local"
                        name="departure_date_time"
                        value="<?= $editMode ? date('Y-m-d\TH:i', strtotime($editFlight['departure_date_time'])) : '' ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Fecha y Hora de Llegada</label>

                    <input
                        type="datetime-local"
                        name="arrival_date_time"
                        value="<?= $editMode ? date('Y-m-d\TH:i', strtotime($editFlight['arrival_date_time'])) : '' ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Costo Clase Ejecutiva</label>

                    <input
                        type="number"
                        step="0.01"
                        name="business_price"
                        placeholder="Ej. 350.00"
                        value="<?= $editMode ? $editFlight['business_price'] : '' ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Costo Clase Turista</label>

                    <input
                        type="number"
                        step="0.01"
                        name="turist_price"
                        placeholder="Ej. 200.00"
                        value="<?= $editMode ? $editFlight['turist_price'] : '' ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Costo Clase Económica</label>

                    <input
                        type="number"
                        step="0.01"
                        name="economy_price"
                        placeholder="Ej. 120.00"
                        value="<?= $editMode ? $editFlight['economy_price'] : '' ?>"
                        required>
                </div>

                <div class="form-actions">

                    <?php if ($editMode) { ?>

                        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-clean btn-link">
                            Cancelar
                        </a>

                        <button type="submit" name="updateFlight" class="btn-save">
                            Actualizar Vuelo
                        </button>

                    <?php } else { ?>

                        <button type="reset" class="btn-clean">
                            Limpiar
                        </button>

                        <button type="submit" name="createFlight" class="btn-save">
                            Guardar Vuelo
                        </button>

                    <?php } ?>

                </div>

            </form>

        </section>

        <section class="card">

            <div class="table-header">

                <h2>Vuelos Programados</h2>

                <form method="GET" class="search-form">
                    <input
                        type="text"
                        name="search"
                        placeholder="Buscar vuelo..."
                        value="<?= htmlspecialchars($search) ?>">
                </form>

            </div>

            <table>

                <thead>
                    <tr>
                        <th>N° Vuelo</th>
                        <th>Avión</th>
                        <th>Ruta</th>
                        <th>Salida</th>
                        <th>Arribo</th>
                        <th>Costos</th>
                        <th>Opciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($result && $result->num_rows > 0) { ?>

                        <?php while ($row = $result->fetch_assoc()) { ?>

                            <tr>
                                <td>
                                    <strong>
                                        <?= str_pad($row['num_flight'], 4, "0", STR_PAD_LEFT) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['registration_mark']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['departure_city']) ?>
                                    <span class="arrow">→</span>
                                    <?= htmlspecialchars($row['arrival_city']) ?>
                                </td>

                                <td>
                                    <?= date('d/m/Y H:i', strtotime($row['departure_date_time'])) ?>
                                </td>

                                <td>
                                    <?= date('d/m/Y H:i', strtotime($row['arrival_date_time'])) ?>
                                </td>

                                <td>
                                    <ul class="prices">

                                        <li>Ejecutiva: $<?= $row['business_price'] ?></li>

                                        <li>Turista: $<?= $row['turist_price'] ?></li>

                                        <li>Económica: $<?= $row['economy_price'] ?></li>
                                    </ul>
                                </td>

                                <td class="actions">

                                    <a href="<?= $_SERVER['PHP_SELF'] ?>?edit=<?= $row['num_flight'] ?>" class="btn-edit">
                                        Editar
                                    </a>

                                    <form method="POST" onsubmit="return confirm('¿Eliminar vuelo?');">

                                        <input
                                            type="hidden"
                                            name="flightID"
                                            value="<?= $row['num_flight'] ?>">

                                        <button
                                            type="submit"
                                            name="deleteFlight"
                                            class="btn-delete">

                                            Eliminar

                                        </button>

                                    </form>

                                </td>
                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="7" class="empty">
                                No se encontraron vuelos.
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </section>

    </main>

</body>

</html>

<?php
$conn->close();
?>