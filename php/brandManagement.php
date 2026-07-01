<?php

session_start();
include("../php/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createBrand'])) {
    $code = $_POST['cod_brand'];
    $name = $_POST['name'];
    $check ="SELECT * FROM brand WHERE name='$name'";

    $resultCheck = $conn->query($check);
    if ($resultCheck->num_rows > 0) {
        echo "
        <script>
        alert('La marca ya existe');
        window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";
        exit();
    }
    $insert ="INSERT INTO brand VALUES('$code','$name')";
    $conn->query($insert);
    header(
        "Location: " . $_SERVER['PHP_SELF']
    );
    exit();
}
// ========= ACTUALIZAR =========
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateBrand'])) {
    $old = $_POST['oldCode'];
    $newCode = $_POST['cod_brand'];
    $name = $_POST['name'];
    $check ="SELECT * FROM brand WHERE name='$name'AND cod_brand<>'$old'";

    $result =$conn->query($check);
    if ($result->num_rows > 0) {
        echo "
        <script>alert('Nombre repetido');
        window.location='" . $_SERVER['PHP_SELF'] . "'; 
        </script>";
        exit();
    }
    $update ="UPDATE brand SET cod_brand='$newCode', name='$name' WHERE cod_brand='$old'";
    $conn->query($update);
    header("Location:" . $_SERVER['PHP_SELF']);
    exit();
}
// ========= ELIMINAR =========

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteBrand'])) {
    $id = $_POST['brandID'];
    $check ="SELECT * FROM model WHERE cod_brand='$id'";

    $result =$conn->query($check);
    if ($result->num_rows > 0) {
        echo "
        <script>alert('No se puede eliminar porque tiene modelos asociados');
        window.location='" . $_SERVER['PHP_SELF'] . "';
        </script>";
        exit();
    }

    $delete ="DELETE FROM brand WHERE cod_brand='$id'";
    $conn->query($delete);
    header("Location:" . $_SERVER['PHP_SELF']);
    exit();
}
// ========= BUSCAR =========
$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $query ="SELECT * FROM brand WHERE name LIKE '%$search%'";
} else {
    $query ="SELECT *FROM brand";
}
$result =
    $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión Marcas</title>
    <link rel="stylesheet" href="../css/brandManagements.css">
</head>
<body>
    <h1>Gestión de Marcas</h1>
    <form method="GET">
        <input type="text" name="search" placeholder="Buscar">
        <button>Buscar</button>
    </form>
    <table>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Guardar</th>
            <th>Eliminar</th>
        </tr>

        <tr> 
            <form method="POST" onsubmit="return confirm('¿Crear marca?')">
                <td><input name="cod_brand" required></td>
                <td><input name="name" required></td>
                <td><button name="createBrand">Crear</button></td>
                <td><button type="reset">Limpiar</button></td>
            </form>
        </tr>
        <?php
        while ($row =$result->fetch_assoc()) {
            ?>
            <tr><form method="POST" onsubmit="return confirm('¿Guardar cambios?')">
                    <input type="hidden" name="oldCode" value="<?= $row['cod_brand'] ?>">
                    <td><input name="cod_brand" value="<?= $row['cod_brand'] ?>"></td>
                    <td><input name="name" value="<?= $row['name'] ?>"></td>
                    <td><button name="updateBrand">Guardar</button></td>
                </form>
                <td>
                <form method="POST" onsubmit="return confirm('¿Eliminar marca?')">
                        <input type="hidden" name="brandID" value="<?= $row['cod_brand'] ?>">
                        <button name="deleteBrand">Eliminar</button>
                </form>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
</body>
</html>