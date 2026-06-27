<?php
session_start();
include("../php/conection.php"); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateRole'])) {
    $userOriginalName = $_POST['userOriginalName']; 
    $userNewName = $_POST['userNewName'];          
    $newPassword = $_POST['newPassword'];          
    $newRole = $_POST['newRole'];                

    $updateQuery = "UPDATE users SET user = '$userNewName', password = '$newPassword', role = '$newRole' WHERE user = '$userOriginalName'";
    $conn->query($updateQuery);
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUser'])) {
    $userToDelete = $_POST['userToDelete'];
    $deleteQuery = "DELETE FROM users WHERE user = '$userToDelete'";
    $conn->query($deleteQuery);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT user, password, role FROM users WHERE user LIKE '%$search%'";
} else {
    $query = "SELECT user, password, role FROM users";
}
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../css/userManagement.css"> 
</head>
<body>
    <header>
        <span>Galei Airlines</span>
    </header>
    
    <main class="container">
        <h1>Gestión de Usuarios</h1>

        <div class="search-container">
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                <button type="submit" class="btn-search">Buscar</button>
                <?php if (!empty($search)): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn-clear">Mostrar Todos</a>
                <?php endif; ?>
            </form>
        </div>

        <section class="userManagement">
            <div class="infoUsers">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Contraseña</th>
                            <th>Rol Actual</th>
                            <th>Modificar Rol</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $userVal = htmlspecialchars($row['user']);
                                $passVal = htmlspecialchars($row['password']);
                                $roleVal = htmlspecialchars($row['role']);

                                echo "<tr>";
                                
                                echo "<form method='POST' class='form-inline'>";
                                echo "<input type='hidden' name='userOriginalName' value='" . $userVal . "'>";
                                echo "<td><input type='text' name='userNewName' value='" . $userVal . "' class='input-table-user'></td>";
                                echo "<td><input type='text' name='newPassword' value='" . $passVal . "' class='input-table-pass'></td>"; 
                                echo "<td>" . $roleVal . "</td>";
                                
                                echo "<td>";
                                echo "<select name='newRole' class='select-role'>";
                                $selectedAdmin = ($row['role'] == 'Administrador') ? 'selected' : '';
                                $selectedColab = ($row['role'] == 'Colaborador') ? 'selected' : '';
                                echo "<option value='Administrador' $selectedAdmin>Administrador</option>";
                                echo "<option value='Colaborador' $selectedColab>Colaborador</option>";
                                echo "</select>";
                                echo "<button type='submit' name='updateRole' class='btn-save'>Guardar</button>";
                                echo "</td>";
                                
                                echo "</form>"; 
                                
                                echo "<td>";
                                echo "<form method='POST' onsubmit='return confirm(\"¿Seguro que deseas eliminar este usuario?\");'>";
                                echo "<input type='hidden' name='userToDelete' value='" . $userVal . "'>";
                                echo "<button type='submit' name='deleteUser' class='btn-delete'>Eliminar</button>";
                                echo "</form>";
                                echo "</td>";
                                
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='no-data'>No se encontraron usuarios</td></tr>";
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