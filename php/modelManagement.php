<?php

session_start();
include("../php/connection.php");


// ========= CREAR MODELO =========

if (
$_SERVER['REQUEST_METHOD']=='POST'
&&
isset($_POST['createModel'])
){

$code=$_POST['cod_model'];

$name=$_POST['name'];

$brand=$_POST['cod_brand'];

$business=$_POST['business_cant'];

$turist=$_POST['turist_cant'];

$economy=$_POST['economy_cant'];

$check=
"SELECT *
FROM model
WHERE
name='$name'";

$result=
$conn->query(
$check
);

if(
$result->num_rows>0
){

echo "

<script>

alert(
'Ya existe un modelo con ese nombre'
);

window.location='".$_SERVER['PHP_SELF']."';

</script>";

exit();

}

$insert=
"INSERT INTO model
VALUES
(
'$code',
'$name',
'$brand',
'$business',
'$turist',
'$economy'
)";

$conn->query(
$insert
);

header(
"Location: ".$_SERVER['PHP_SELF']
);

exit();

}



// ========= ACTUALIZAR =========

if(
$_SERVER['REQUEST_METHOD']=='POST'
&&
isset($_POST['updateModel'])
){

$old=
$_POST['oldModel'];

$code=
$_POST['cod_model'];

$name=
$_POST['name'];

$brand=
$_POST['cod_brand'];

$business=
$_POST['business_cant'];

$turist=
$_POST['turist_cant'];

$economy=
$_POST['economy_cant'];

$check=
"SELECT *
FROM model
WHERE
name='$name'
AND
cod_model<>'$old'";

$result=
$conn->query(
$check
);

if(
$result->num_rows>0
){

echo "

<script>

alert(
'Nombre repetido'
);

window.location='".$_SERVER['PHP_SELF']."';

</script>";

exit();

}

$update=
"UPDATE model
SET

cod_model='$code',

name='$name',

cod_brand='$brand',

business_cant='$business',

turist_cant='$turist',

economy_cant='$economy'

WHERE

cod_model='$old'";

$conn->query(
$update
);

header(
"Location: ".$_SERVER['PHP_SELF']
);

exit();

}



// ========= ELIMINAR =========

if(
$_SERVER['REQUEST_METHOD']=='POST'
&&
isset($_POST['deleteModel'])
){

$id=
$_POST['modelID'];

$check=
"SELECT *
FROM airplane
WHERE
cod_model='$id'";

$result=
$conn->query(
$check
);

if(
$result->num_rows>0
){

echo "

<script>

alert(
'No puede eliminarse porque existe un avión asociado'
);

window.location='".$_SERVER['PHP_SELF']."';

</script>";

exit();

}

$delete=
"DELETE
FROM model
WHERE
cod_model='$id'";

$conn->query(
$delete
);

header(
"Location: ".$_SERVER['PHP_SELF']
);

exit();

}



// ========= BUSCAR =========

$search="";

if(
isset($_GET['search'])
&&
!empty($_GET['search'])
){

$search=
$_GET['search'];

$query=
"SELECT
model.*,
brand.name
AS
brand_name

FROM model

INNER JOIN brand

ON

model.cod_brand=
brand.cod_brand

WHERE

model.name

LIKE

'%$search%'";

}

else{

$query=
"SELECT
model.*,
brand.name
AS
brand_name

FROM model

INNER JOIN brand

ON

model.cod_brand=
brand.cod_brand";

}

$result=
$conn->query(
$query
);

$brands=
$conn->query(
"SELECT *
FROM brand"
);

?>


<!DOCTYPE html>

<html>

<head>

<title>

Gestión Modelos

</title>

<link
rel="stylesheet"
href="../css/modelManagement.css">

</head>

<body>

<h1>

Gestión de Modelos

</h1>



<form method="GET">

<input
type="text"
name="search">

<button>

Buscar

</button>

</form>



<table>

<tr>

<th>Código</th>

<th>Nombre</th>

<th>Marca</th>

<th>Ejecutiva</th>

<th>Turista</th>

<th>Económica</th>

<th>Guardar</th>

<th>Eliminar</th>

</tr>



<tr>

<form
method="POST"
onsubmit="
return
confirm(
'¿Crear modelo?'
)
">

<td>

<input
name="cod_model"
required>

</td>

<td>

<input
name="name"
required>

</td>


<td>

<select
name="cod_brand">

<?php

while(
$b=
$brands->fetch_assoc()
){

?>

<option
value="<?= $b['cod_brand']?>">

<?= $b['name']?>

</option>

<?php

}

?>

</select>

</td>


<td>

<input
type="number"
name="business_cant"
required>

</td>


<td>

<input
type="number"
name="turist_cant"
required>

</td>


<td>

<input
type="number"
name="economy_cant"
required>

</td>


<td>

<button
name="createModel">

Crear

</button>

</td>


<td>

<button
type="reset">

Limpiar

</button>

</td>

</form>

</tr>



<?php

while(
$row=
$result->fetch_assoc()
){

?>

<tr>

<form
method="POST"
onsubmit="
return
confirm(
'¿Guardar cambios?'
)
">

<input
type="hidden"
name="oldModel"
value="<?= $row['cod_model']?>">


<td>

<input
name="cod_model"
value="<?= $row['cod_model']?>">

</td>


<td>

<input
name="name"
value="<?= $row['name']?>">

</td>


<td>

<select
name="cod_brand">

<?php

$list=
$conn->query(
"SELECT *
FROM brand"
);

while(
$b=
$list->fetch_assoc()
){

?>

<option

value="<?= $b['cod_brand']?>"

<?=

$b['cod_brand']==$row['cod_brand']

?

"selected"

:

""

?>

>

<?= $b['name']?>

</option>

<?php

}

?>

</select>

</td>


<td>

<input
name="business_cant"
value="<?= $row['business_cant']?>">

</td>


<td>

<input
name="turist_cant"
value="<?= $row['turist_cant']?>">

</td>


<td>

<input
name="economy_cant"
value="<?= $row['economy_cant']?>">

</td>


<td>

<button
name="updateModel">

Guardar

</button>

</td>

</form>



<td>

<form
method="POST"
onsubmit="
return
confirm(
'¿Eliminar modelo?'
)
">

<input
type="hidden"
name="modelID"
value="<?= $row['cod_model']?>">

<button
name="deleteModel">

Eliminar

</button>

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