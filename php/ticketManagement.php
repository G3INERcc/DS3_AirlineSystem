<?php

session_start();
include("../php/connection.php");

// ========= CARGAR TIQUETE PARA EDITAR =========

$editMode = false;
$editTicket = null;

if (isset($_GET['edit'])) {

    $editID = $_GET['edit'];

    $editQuery = "
        SELECT *
        FROM ticket
        WHERE num_ticket='$editID'
    ";

    $editResult = $conn->query($editQuery);

    if ($editResult && $editResult->num_rows > 0) {
        $editMode = true;
        $editTicket = $editResult->fetch_assoc();
    }
}

// ========= FUNCIÓN PARA VALIDAR DISPONIBILIDAD =========

function validateAvailability(mysqli $conn, int $numFlight, string $class, $oldTicket = null)
{
    $capacityField = "";

    if ($class == "business") {
        $capacityField = "business_cant";
    } else if ($class == "turist") {
        $capacityField = "turist_cant";
    } else {
        $capacityField = "economy_cant";
    }

    $capacityQuery = "
        SELECT m.$capacityField AS capacity
        FROM flight f
        INNER JOIN airplane a ON f.registration_mark = a.registration_mark
        INNER JOIN model m ON a.cod_model = m.cod_model
        WHERE f.num_flight='$numFlight'
    ";

    $capacityResult = $conn->query($capacityQuery);
    $capacityRow = $capacityResult->fetch_assoc();
    $capacity = $capacityRow['capacity'];

    $soldQuery = "
        SELECT COUNT(*) AS sold
        FROM ticket
        WHERE num_flight='$numFlight'
        AND class='$class'
    ";

    if ($oldTicket != null) {
        $soldQuery .= " AND num_ticket<>'$oldTicket'";
    }

    $soldResult = $conn->query($soldQuery);
    $soldRow = $soldResult->fetch_assoc();
    $sold = $soldRow['sold'];

    return $sold < $capacity;
}

// ========= FUNCIÓN PARA OBTENER MONTO =========

function getAmount(mysqli $conn, int $numFlight, string $class)
{
    $priceField = "";

    if ($class == "business") {
        $priceField = "business_price";
    } else if ($class == "turist") {
        $priceField = "turist_price";
    } else {
        $priceField = "economy_price";
    }

    $query = "
        SELECT $priceField AS amount
        FROM flight
        WHERE num_flight='$numFlight'
    ";

    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    return $row['amount'];
}

// ========= CREAR TIQUETE =========

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createTicket'])) {

    $passport = $_POST['passport'];
    $numFlight = $_POST['num_flight'];
    $class = $_POST['class'];
    $purchaseDate = date("Y-m-d H:i:s");

    if (!validateAvailability($conn, $numFlight, $class)) {

        echo "
        <script>
            alert('No hay espacio disponible para esa clase en este vuelo');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";

        exit();
    }

    $amount = getAmount($conn, $numFlight, $class);

    $insert = "
        INSERT INTO ticket
        (
            passport,
            num_flight,
            class,
            amount,
            purchase_date
        )
        VALUES
        (
            '$passport',
            '$numFlight',
            '$class',
            '$amount',
            '$purchaseDate'
        )
    ";

    $conn->query($insert);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ========= ACTUALIZAR TIQUETE =========

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateTicket'])) {

    $oldTicket = $_POST['oldTicket'];
    $passport = $_POST['passport'];
    $numFlight = $_POST['num_flight'];
    $class = $_POST['class'];

    if (!validateAvailability($conn, $numFlight, $class, $oldTicket)) {

        echo "
        <script>
            alert('No hay espacio disponible para esa clase en este vuelo');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";

        exit();
    }

    $amount = getAmount($conn, $numFlight, $class);

    $update = "
        UPDATE ticket
        SET
            passport='$passport',
            num_flight='$numFlight',
            class='$class',
            amount='$amount'
        WHERE num_ticket='$oldTicket'
    ";

    $conn->query($update);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ========= ELIMINAR TIQUETE =========

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteTicket'])) {

    $numTicket = $_POST['ticketID'];

    $delete = "
        DELETE FROM ticket
        WHERE num_ticket='$numTicket'
    ";

    $conn->query($delete);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ========= BUSCAR =========

$search = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {

    $search = $_GET['search'];

    $query = "
        SELECT t.*, p.name, p.lastname, f.departure_city, f.arrival_city
        FROM ticket t
        INNER JOIN passenger p ON t.passport = p.passport
        INNER JOIN flight f ON t.num_flight = f.num_flight
        WHERE t.num_ticket LIKE '%$search%'
        OR t.passport LIKE '%$search%'
        OR p.name LIKE '%$search%'
        OR p.lastname LIKE '%$search%'
        OR t.num_flight LIKE '%$search%'
        ORDER BY t.num_ticket DESC
    ";

} else {

    $query = "
        SELECT t.*, p.name, p.lastname, f.departure_city, f.arrival_city
        FROM ticket t
        INNER JOIN passenger p ON t.passport = p.passport
        INNER JOIN flight f ON t.num_flight = f.num_flight
        ORDER BY t.num_ticket DESC
    ";
}

$result = $conn->query($query);

// ========= CARGAR PASAJEROS =========

$passengersQuery = "
    SELECT passport, name, lastname
    FROM passenger
    ORDER BY lastname
";

$passengersResult = $conn->query($passengersQuery);

$passengers = [];

while ($passenger = $passengersResult->fetch_assoc()) {
    $passengers[] = $passenger;
}

// ========= CARGAR VUELOS =========

$flightsQuery = "
    SELECT num_flight, departure_city, arrival_city
    FROM flight
    ORDER BY num_flight
";

$flightsResult = $conn->query($flightsQuery);

$flights = [];

while ($flight = $flightsResult->fetch_assoc()) {
    $flights[] = $flight;
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Gestión de Tiquetes</title>

    <link rel="stylesheet" href="../css/ticketManagement.css">

</head>

<body>

<header>

    <span>Galei Airlines</span>

    <a href="../php/menu.php">
        Regresar
    </a>

</header>

<main class="container">

    <h1>Gestión de Tiquetes</h1>

    <section class="card">

        <h2>
            <?= $editMode ? "Editar Tiquete" : "Registrar Nuevo Tiquete" ?>
        </h2>

        <form method="POST" class="ticket-form" onsubmit="return confirm('¿Guardar tiquete?');">

            <?php if ($editMode) { ?>
                <input type="hidden" name="oldTicket" value="<?= $editTicket['num_ticket'] ?>">
            <?php } ?>

            <div class="form-group">

                <label>Pasajero</label>

                <select name="passport" required>

                    <option value="">-- Seleccione un pasajero --</option>

                    <?php foreach ($passengers as $passenger) { ?>

                        <option
                            value="<?= $passenger['passport'] ?>"
                            <?= $editMode && $editTicket['passport'] == $passenger['passport'] ? "selected" : "" ?>>

                            <?= $passenger['passport'] ?> -
                            <?= $passenger['name'] ?>
                            <?= $passenger['lastname'] ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <div class="form-group">

                <label>Vuelo</label>

                <select name="num_flight" required>

                    <option value="">-- Seleccione un vuelo --</option>

                    <?php foreach ($flights as $flight) { ?>

                        <option
                            value="<?= $flight['num_flight'] ?>"
                            <?= $editMode && $editTicket['num_flight'] == $flight['num_flight'] ? "selected" : "" ?>>

                            <?= str_pad($flight['num_flight'], 4, "0", STR_PAD_LEFT) ?>
                            -
                            <?= $flight['departure_city'] ?>
                            →
                            <?= $flight['arrival_city'] ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <div class="form-group">

                <label>Clase</label>

                <select name="class" required>

                    <option value="">-- Seleccione una clase --</option>

                    <option value="business" <?= $editMode && $editTicket['class'] == "business" ? "selected" : "" ?>>
                        Ejecutiva
                    </option>

                    <option value="turist" <?= $editMode && $editTicket['class'] == "turist" ? "selected" : "" ?>>
                        Turista
                    </option>

                    <option value="economy" <?= $editMode && $editTicket['class'] == "economy" ? "selected" : "" ?>>
                        Económica
                    </option>

                </select>

            </div>

            <div class="form-actions">

                <?php if ($editMode) { ?>

                    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-clean btn-link">
                        Cancelar
                    </a>

                    <button type="submit" name="updateTicket" class="btn-save">
                        Actualizar Tiquete
                    </button>

                <?php } else { ?>

                    <button type="reset" class="btn-clean">
                        Limpiar
                    </button>

                    <button type="submit" name="createTicket" class="btn-save">
                        Guardar Tiquete
                    </button>

                <?php } ?>

            </div>

        </form>

    </section>

    <section class="card">

        <div class="table-header">

            <h2>Tiquetes Registrados</h2>

            <form method="GET" class="search-form">

                <input
                    type="text"
                    name="search"
                    placeholder="Buscar tiquete..."
                    value="<?= htmlspecialchars($search) ?>">

            </form>

        </div>

        <table>

            <thead>

                <tr>

                    <th>N° Tiquete</th>
                    <th>Pasajero</th>
                    <th>Vuelo</th>
                    <th>Clase</th>
                    <th>Monto</th>
                    <th>Fecha Compra</th>
                    <th>Opciones</th>

                </tr>

            </thead>

            <tbody>

                <?php if ($result && $result->num_rows > 0) { ?>

                    <?php while ($row = $result->fetch_assoc()) { ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= str_pad($row['num_ticket'], 5, "0", STR_PAD_LEFT) ?>
                                </strong>
                            </td>

                            <td>

                                <ul class="info-list">

                                    <li>
                                        <?= htmlspecialchars($row['name']) ?>
                                        <?= htmlspecialchars($row['lastname']) ?>
                                    </li>

                                    <li>
                                        Pasaporte: <?= htmlspecialchars($row['passport']) ?>
                                    </li>

                                </ul>

                            </td>

                            <td>
                                <?= str_pad($row['num_flight'], 4, "0", STR_PAD_LEFT) ?>
                                -
                                <?= htmlspecialchars($row['departure_city']) ?>
                                →
                                <?= htmlspecialchars($row['arrival_city']) ?>
                            </td>

                            <td>
                                <?php
                                if ($row['class'] == "business") {
                                    echo "Ejecutiva";
                                } else if ($row['class'] == "turist") {
                                    echo "Turista";
                                } else {
                                    echo "Económica";
                                }
                                ?>
                            </td>

                            <td>
                                $<?= number_format($row['amount'], 2) ?>
                            </td>

                            <td>
                                <?= date('d/m/Y H:i', strtotime($row['purchase_date'])) ?>
                            </td>

                            <td class="actions">

                                <a
                                    href="<?= $_SERVER['PHP_SELF'] ?>?edit=<?= $row['num_ticket'] ?>"
                                    class="btn-edit">

                                    Editar

                                </a>

                                <form method="POST" onsubmit="return confirm('¿Eliminar tiquete?');">

                                    <input
                                        type="hidden"
                                        name="ticketID"
                                        value="<?= $row['num_ticket'] ?>">

                                    <button
                                        type="submit"
                                        name="deleteTicket"
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
                            No se encontraron tiquetes.
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