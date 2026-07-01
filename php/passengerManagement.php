<?php

session_start();
include("../php/connection.php");

// ========= CARGAR PASAJERO PARA EDITAR =========

$editMode = false;
$editPassenger = null;

if (isset($_GET['edit'])) {

    $editID = $_GET['edit'];

    $editQuery = "
        SELECT *
        FROM passenger
        WHERE passport='$editID'
    ";

    $editResult = $conn->query($editQuery);

    if ($editResult && $editResult->num_rows > 0) {
        $editMode = true;
        $editPassenger = $editResult->fetch_assoc();
    }
}

// ========= CREAR PASAJERO =========

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createPassenger'])) {

    $passport = $_POST['passport'];
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $birthdate = $_POST['birthdate'];
    $mail = $_POST['mail'];
    $phone = $_POST['phone_num'];

    $check = "
        SELECT *
        FROM passenger
        WHERE passport='$passport'
    ";

    $resultCheck = $conn->query($check);

    if ($resultCheck->num_rows > 0) {

        echo "
        <script>
            alert('Ya existe un pasajero con ese número de pasaporte');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";

        exit();
    }

    $insert = "
        INSERT INTO passenger
        (
            passport,
            name,
            lastname,
            birthdate,
            mail,
            phone_num
        )
        VALUES
        (
            '$passport',
            '$name',
            '$lastname',
            '$birthdate',
            '$mail',
            '$phone'
        )
    ";

    $conn->query($insert);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ========= ACTUALIZAR PASAJERO =========

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatePassenger'])) {

    $oldPassport = $_POST['oldPassport'];
    $passport = $_POST['passport'];
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $birthdate = $_POST['birthdate'];
    $mail = $_POST['mail'];
    $phone = $_POST['phone_num'];

    $check = "
        SELECT *
        FROM passenger
        WHERE passport='$passport'
        AND passport<>'$oldPassport'
    ";

    $resultCheck = $conn->query($check);

    if ($resultCheck->num_rows > 0) {

        echo "
        <script>
            alert('Ya existe un pasajero con ese número de pasaporte');
            window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";

        exit();
    }

    $update = "
        UPDATE passenger
        SET
            passport='$passport',
            name='$name',
            lastname='$lastname',
            birthdate='$birthdate',
            mail='$mail',
            phone_num='$phone'
        WHERE passport='$oldPassport'
    ";

    $conn->query($update);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ========= ELIMINAR PASAJERO =========

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletePassenger'])) {

    $passport = $_POST['passengerID'];

    $delete = "
        DELETE FROM passenger
        WHERE passport='$passport'
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
        SELECT *
        FROM passenger
        WHERE passport LIKE '%$search%'
        OR name LIKE '%$search%'
        OR lastname LIKE '%$search%'
        OR mail LIKE '%$search%'
        OR phone_num LIKE '%$search%'
        ORDER BY lastname
    ";

} else {

    $query = "
        SELECT *
        FROM passenger
        ORDER BY lastname
    ";
}

$result = $conn->query($query);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Gestión de Pasajeros</title>

    <link rel="stylesheet" href="../css/passengerManagement.css">

</head>

<body>

<header>

    <span>Galei Airlines</span>

    <a href="../php/menu.php">
        Regresar
    </a>

</header>

<main class="container">

    <h1>Gestión de Pasajeros</h1>

    <section class="card">

        <h2>
            <?= $editMode ? "Editar Pasajero" : "Registrar Nuevo Pasajero" ?>
        </h2>

        <form method="POST" class="passenger-form" onsubmit="return confirm('¿Guardar pasajero?');">

            <?php if ($editMode) { ?>
                <input type="hidden" name="oldPassport" value="<?= $editPassenger['passport'] ?>">
            <?php } ?>

            <div class="form-group">

                <label>N° Pasaporte</label>

                <input
                    type="text"
                    name="passport"
                    placeholder="Ej. C1234567"
                    value="<?= $editMode ? htmlspecialchars($editPassenger['passport']) : '' ?>"
                    required>

            </div>

            <div class="form-group">

                <label>Nombre</label>

                <input
                    type="text"
                    name="name"
                    placeholder="Ej. Galilea"
                    value="<?= $editMode ? htmlspecialchars($editPassenger['name']) : '' ?>"
                    required>

            </div>

            <div class="form-group">

                <label>Apellidos</label>

                <input
                    type="text"
                    name="lastname"
                    placeholder="Ej. Blandón Segura"
                    value="<?= $editMode ? htmlspecialchars($editPassenger['lastname']) : '' ?>"
                    required>

            </div>

            <div class="form-group">

                <label>Fecha de nacimiento</label>

                <input
                    type="date"
                    name="birthdate"
                    value="<?= $editMode ? $editPassenger['birthdate'] : '' ?>"
                    required>

            </div>

            <div class="form-group">

                <label>Correo electrónico</label>

                <input
                    type="email"
                    name="mail"
                    placeholder="Ej. pasajero@email.com"
                    value="<?= $editMode ? htmlspecialchars($editPassenger['mail']) : '' ?>">

            </div>

            <div class="form-group">

                <label>Teléfono</label>

                <input
                    type="text"
                    name="phone_num"
                    placeholder="Ej. 8888-8888"
                    value="<?= $editMode ? htmlspecialchars($editPassenger['phone_num']) : '' ?>">

            </div>

            <div class="form-actions">

                <?php if ($editMode) { ?>

                    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-clean btn-link">
                        Cancelar
                    </a>

                    <button type="submit" name="updatePassenger" class="btn-save">
                        Actualizar Pasajero
                    </button>

                <?php } else { ?>

                    <button type="reset" class="btn-clean">
                        Limpiar
                    </button>

                    <button type="submit" name="createPassenger" class="btn-save">
                        Guardar Pasajero
                    </button>

                <?php } ?>

            </div>

        </form>

    </section>

    <section class="card">

        <div class="table-header">

            <h2>Pasajeros Registrados</h2>

            <form method="GET" class="search-form">

                <input
                    type="text"
                    name="search"
                    placeholder="Buscar pasajero..."
                    value="<?= htmlspecialchars($search) ?>">

            </form>

        </div>

        <table>

            <thead>

                <tr>

                    <th>Pasaporte</th>
                    <th>Nombre completo</th>
                    <th>Nacimiento</th>
                    <th>Contacto</th>
                    <th>Opciones</th>

                </tr>

            </thead>

            <tbody>

            <?php if ($result && $result->num_rows > 0) { ?>

                <?php while ($row = $result->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <strong><?= htmlspecialchars($row['passport']) ?></strong>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['name']) ?>
                            <?= htmlspecialchars($row['lastname']) ?>
                        </td>

                        <td>
                            <?= date('d/m/Y', strtotime($row['birthdate'])) ?>
                        </td>

                        <td>

                            <ul class="contact-list">

                                <li>
                                    Correo: <?= htmlspecialchars($row['mail']) ?>
                                </li>

                                <li>
                                    Teléfono: <?= htmlspecialchars($row['phone_num']) ?>
                                </li>

                            </ul>

                        </td>

                        <td class="actions">

                            <a
                                href="<?= $_SERVER['PHP_SELF'] ?>?edit=<?= $row['passport'] ?>"
                                class="btn-edit">

                                Editar

                            </a>

                            <form method="POST" onsubmit="return confirm('¿Eliminar pasajero?');">

                                <input
                                    type="hidden"
                                    name="passengerID"
                                    value="<?= $row['passport'] ?>">

                                <button
                                    type="submit"
                                    name="deletePassenger"
                                    class="btn-delete">

                                    Eliminar

                                </button>

                            </form>

                        </td>

                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>

                    <td colspan="5" class="empty">
                        No se encontraron pasajeros.
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