<?php
    require "../modelo/conexion.php"; 

    //Creamos un objeto de base de datos
    $objBD = new BBDD();
    //Conectamos a la Base de datos
    $objBD->conectarBD();
 
	//Campos de la consulta
	$unionTablas = [
		["tabla1" => "muebles","campoTabla1" => "id", "tabla2" => "almacen_mueble","campoTabla2" => "id_mueble"],
		["tabla1" => "almacen","campoTabla1" => "id", "tabla2" => "almacen_mueble","campoTabla2" => "id_almacen"]
	];
	//$campos = ["muebles.nombre","muebles.precio","almacen.nombre"];
	$campos = ["id","nombre","precio"];
	$muebles = $objBD->consultarTabla("muebles", $campos, null, null, null, null);
	//Bucle que muestra los resultados de la consulta
	while($row = mysqli_fetch_array($muebles)){
		//echo "".$row['nombre']." ".$row['precio']."<br>";
		echo "<h1>".$row['id']."</h1>".$row['precio']."€ ".$row['nombre']."<br>";
	}

	$objBD->desconectarBD();
?>