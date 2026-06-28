<?php

session_start();
include("../php/connection.php");

// Seguridad
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'administrador'
) {
    header("Location: ../index.html");
    exit();
}

// =======================
// ACTUALIZAR
// =======================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['updateRole'])
) {

    $userOriginalID = $_POST['userOriginalID'];
    $newID = $_POST['newID'];
    $userNewName = $_POST['userNewName'];
    $newPassword = $_POST['newPassword'];
    $newRole = $_POST['newRole'];

    // Validar ID repetido
    $checkID =
        "SELECT id FROM user
        WHERE id='$newID'
        AND id<>'$userOriginalID'";

    $resultID = $conn->query($checkID);

    // Validar username repetido
    $checkUser =
        "SELECT id FROM user
        WHERE username='$userNewName'
        AND id<>'$userOriginalID'";

    $resultUser = $conn->query($checkUser);

    if (
        $resultID->num_rows == 0 &&
        $resultUser->num_rows == 0
    ) {

        $update =
            "UPDATE user
            SET
                id='$newID',
                username='$userNewName',
                pass='$newPassword',
                user_type='$newRole'
            WHERE id='$userOriginalID'";

        $conn->query($update);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// =======================
// ELIMINAR
// =======================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['deleteUser'])
) {

    $id = $_POST['userID'];

    $delete =
        "DELETE FROM user
        WHERE id='$id'";

    $conn->query($delete);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// =======================
// BUSCAR
// =======================

$search = "";

if (
    isset($_GET['search']) &&
    !empty($_GET['search'])
) {

    $search = $_GET['search'];

    $query =
        "SELECT *
        FROM user
        WHERE username
        LIKE '%$search%'";

} else {

    $query =
        "SELECT *
        FROM user";
}

$result = $conn->query($query);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Usuarios</title>

    <link rel="stylesheet" href="../css/userManager.css">

</head>

<body>

    <header>

        <span>Galei Airlines</span>

        <a href="../php/menu.php">
            <img src="../assets/2.svg" alt="">
        </a>

    </header>

    <main class="container">

        <h1>Gestión de Usuarios</h1>

        <div class="search-container">

            <form method="GET" class="search-form">

                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="search-input">

                <button type="submit" class="btn-search">

                    Buscar

                </button>

                <button type="button" class="btn-search" onclick="window.location.href='../html/createUserAdm.html'">

                    Crear Usuario

                </button>

            </form>

        </div>

        <section class="userManagement">

            <div class="infoUsers">

                <table class="users-table">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Contraseña</th>
                            <th>Rol</th>
                            <th>Guardar</th>
                            <th>Eliminar</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        if ($result && $result->num_rows > 0) {

                            while ($row = $result->fetch_assoc()) {

                                ?>

                                <tr>

                                    <form method="POST" onsubmit="return confirm('¿Guardar cambios?');">

                                        <input type="hidden" name="userOriginalID" value="<?= $row['id'] ?>">

                                        <td>

                                            <input type="number" name="newID" value="<?= $row['id'] ?>" class="input-table-id">

                                        </td>

                                        <td>

                                            <input type="text" name="userNewName"
                                                value="<?= htmlspecialchars($row['username']) ?>" class="input-table-user">

                                        </td>

                                        <td>

                                            <input type="text" name="newPassword"
                                                value="<?= htmlspecialchars($row['pass']) ?>" class="input-table-pass">

                                        </td>

                                        <td>

                                            <select name="newRole" class="select-role">

                                                <option value="administrador" <?= $row['user_type'] == "administrador"
                                                    ? "selected"
                                                    : "" ?>>

                                                    Administrador

                                                </option>

                                                <option value="colaborador" <?= $row['user_type'] == "colaborador"
                                                    ? "selected"
                                                    : "" ?>>

                                                    Colaborador

                                                </option>

                                            </select>

                                        </td>

                                        <td>

                                            <button type="submit" name="updateRole" class="btn-save">

                                                Guardar

                                            </button>

                                        </td>

                                    </form>

                                    <td>

                                        <form method="POST" onsubmit="return confirm('¿Eliminar usuario?');">

                                            <input type="hidden" name="userID" value="<?= $row['id'] ?>">

                                            <button type="submit" name="deleteUser" class="btn-delete">

                                                Eliminar

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                                <?php

                            }

                        } else {

                            ?>

                            <tr>

                                <td colspan="6">

                                    No se encontraron usuarios

                                </td>

                            </tr>

                            <?php

                        }

                        $conn->close();

                        ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</body>

</html>
```