<?php
session_start();
include("../php/connection.php");
$detail = null;
if (isset($_GET['num_flight']) &&!empty($_GET['num_flight'])) {
    $id = $_GET['num_flight'];
    $query = "SELECT  flight.*, airline.name AS airline, airplane.registration_mark, model.business_cant, model.turist_cant, model.economy_cant
    FROM flight INNER JOIN airplane ON flight.registration_mark= airplane.registration_mark
    INNER JOIN airline ON airplane.cod_airline= airline.cod_airline
    INNER JOIN model ON airplane.cod_model= model.cod_model
    WHERE flight.num_flight='$id'";

    $result =$conn->query($query);
    $detail =$result->fetch_assoc();

    $businessSold =$conn->query("SELECT COUNT(*) qty
    FROM ticket WHERE num_flight='$id' AND class='ejecutiva'") ->fetch_assoc()['qty'];

    $turistSold =$conn->query(
                "SELECT COUNT(*) qty FROM ticket WHERE num_flight='$id' AND class='turista'")->fetch_assoc()['qty'];
    $economySold =$conn->query(
                "SELECT COUNT(*) qty FROM ticket WHERE num_flight='$id' AND class='economica'")->fetch_assoc()['qty'];
    $total =$conn->query(
                "SELECT IFNULL(SUM(amount),0) total FROM ticket WHERE num_flight='$id'")->fetch_assoc()['total'];

    $businessAvailable =
        $detail['business_cant'] - $businessSold;

    $turistAvailable =
        $detail['turist_cant'] - $turistSold;

    $economyAvailable =
         $detail['economy_cant'] - $economySold;
}

$flights =
    $conn->query("SELECT num_flight FROM flight");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>
        Consulta Vuelos Detallado
    </title>
    <link rel="stylesheet" href="../css/userManager.css">
</head>
<body>
    <header>
        <span>
            Galei Airlines
        </span>
    </header>
    <main class="container">
        <h1>
            Consulta vuelos detallado
        </h1>
        <form method="GET">
            <select name="num_flight">
                <option value="">
                    Seleccionar vuelo
                </option>
                <?php
                while ($f = $flights->fetch_assoc()) {
                    ?>
                    <option value="<?= $f['num_flight'] ?>"><?= $f['num_flight'] ?></option>
                    <?php
                }
                ?>
            </select>
            <button>Consultar</button>
        </form>

        <?php
        if ($detail) {
            ?>
            <table class="users-table">
                <tr>
                    <th>Campo</th>
                    <th>Información</th>
                </tr>
                <tr>
                    <td>Número vuelo</td>
                    <td><?= $detail['num_flight'] ?></td>
                </tr>
                <tr>
                    <td>Aerolínea</td><td>
                        <?= $detail['airline'] ?>
                    </td>
                </tr>

                <tr>
                    <td>Avión</td><td>
                        <?= $detail['registration_mark'] ?>
                    </td>
                </tr>

                <tr>
                    <td>Salida</td>
                    <td>
                        <?= $detail['departure_city'] ?>
                        <br>
                        <?= $detail['departure_date_time'] ?>
                    </td>
                </tr>

                <tr>
                    <td>Arribo</td>
                    <td>
                        <?= $detail['arrival_city'] ?>
                        <br>
                        <?= $detail['arrival_date_time'] ?>
                    </td>
                </tr>

                <tr>
                    <td>Ejecutiva vendidos</td>
                    <td><?= $businessSold ?></td>
                </tr>

                <tr>
                    <td>Ejecutiva disponibles</td>
                    <td><?= $businessAvailable ?></td>
                </tr>

                <tr>
                    <td>Turista vendidos</td>
                    <td><?= $turistSold ?></td>
                </tr>

                <tr>
                    <td>Turista disponibles</td>
                    <td><?= $turistAvailable ?></td>
                </tr>

                <tr>
                    <td>Económica vendidos</td>
                    <td><?= $economySold ?></td>
                </tr>

                <tr>
                    <td>Económica disponibles</td>
                    <td><?= $economyAvailable ?></td>
                </tr>

                <tr>
                    <td>Precio Ejecutiva</td>
                    <td>₡<?= $detail['business_price'] ?></td>
                </tr>

                <tr>
                    <td>Precio Turista</td>
                    <td>₡<?= $detail['turist_price'] ?></td>
                </tr>

                <tr>
                    <td>Precio Económica</td>
                    <td>₡<?= $detail['economy_price'] ?></td>
                </tr>

                <tr>
                    <td>Monto recaudado</td>
                    <td>₡<?= $total ?></td>
                </tr>

            </table>
            <?php
        }
        $conn->close();
        ?>
    </main>
</body>
</html>