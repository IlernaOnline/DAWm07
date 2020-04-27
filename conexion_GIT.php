<?php
/* 
	AUTOR: ILERNA ONLINE
	------>FUNCIONES<----------
		CONSULTAR()
		
						     							 	Solo campos o valores en String o Array sin sentencias SQL 
							String        String o Array    		para omitir enviar NULL 
		consultarTabla(     $tabla    ,      $campos   ,    [$condicion, $agrupacion, $ordenacion, $limit])

						     	
		Estas funciones lanzaran SELECT sobre las tablas que elgimamos de nuestra conexión
		Para ser utilizadas tienen que cumplir los siguientes requisistos:
		*********************consultarTabla()*****************************
		$tabla  	==> La tabla tiene que ser un String con el nombre de esta exclusivamente.
		$campos 	==> Los campos pueden ser un solo campo (nombre) todos los campos (*)
						Si utilizamos la opcion de array hay que informar el nombre de los 
						campos en cada posicion del array ["campo1", "campo2", ... "campoN"]
		$condicion 	==> La condicion solo debe contener el campo y la condicion ("precio > 50")
						todo en un string.
						Para poner más condiciones se tienen que separar con un array de strings.
						["precio > 50", "stock > 5", "etc..."]
		$agrupacion ==> La agrupación solo debe contener el campo "tipo_mueble".
						En caso de querer agrupar por varios campos estos se tienen que informar
						dentro de un array de strings
						["tipo_mueble", "color", "etc..."]
				**HAVING**
						Si se pretende utilizar la clausula HAVING junto con la agrupación esta debe 
						ser infromada en una posicion de ARRAY y en la siguiente posicion la condicion.
						["tipo_mueble", "HAVING", "color = 'red'"]
		$ordenacion ==> La ordenación solo debe contener en campo y la forma "nombre ASC".
						En caso de querer ordenar por varios campos estos se tienen que informar
						dentro de un array de strings
						["nombre ASC", "apellido DESC", "etc..."]
						La forma es opcional, aunque si no se indica esta siempre sera ASC.
		*********************consultarTablas()****************************
		
		*********************actualizarTabla()****************************
			
*/
class BBDD{
	var $servidor; //Nombre de la maquina donde se encuentra la BD generalmente es localhost
	var $nombreBD; //Nombre de la Base de Datos
	var $nombreDeUsuario; //Nombre del usuario autorizado para entrar a la Base de Datos
	var $contrasena; //Contraseña del Usuario

	/////////////////////////////////////////////////////////////
	////////////Constructor de la Clase/////////////////////////
	////////////////////////////////////////////////////////////
    function BBDD($servidor='localhost', $nameBD='mueblesilerna'){
	    $this->servidor=$servidor;
	    $this->nombreBD=$nameBD;
	    $this->nombreDeUsuario='root';
	    $this->contrasena='';
	    $this->consulta='';
    }

    ////////////////////////////////////////////////////////////
	////////////Conexion de la BASE DE DATOS////////////////////
	////////////////////////////////////////////////////////////
    function conectarBD(){

    	$this->consulta = new mysqli($this->servidor,$this->nombreDeUsuario, $this->contrasena, $this->nombreBD);
		
		if ($this->consulta->connect_error) {
	    	printf('Connect failed: %s\n', $consulta->connect_error);
	    	exit();
		}

		$this->consulta->set_charset('ISO-8859-1');
    }
    ////////////////////////////////////////////////////////////
	//////////Desconexion de la BASE DE DATOS///////////////////
	////////////////////////////////////////////////////////////
    function desconectarBD(){
 		$this->consulta->close();
 	}
    ////////////////////////////////////////////////////////////
	/////SELECCIÓN SIMPLE (SOLO 1 TABLA)////////////////////////
	////////////////////////////////////////////////////////////
    function consultarTabla($tabla, $campos, $condicion, $agrupacion, $ordenacion, $limite){
    	//COMPROBAR DATOS MINIMOS RECIBIDOS
    	if(is_array($campos)){
    		//Union de campos
    		$valCampos = self::unirCampos($campos,'C');
    	}
    	else if(is_string($campos) && is_string($tabla)){
    		$valCampos = $campos;
    	}
    	else{
    		//Error doc
    		$resultado = 'Los datos introducidos en la funcion no cumplen los requisitos minimos establecidos en la documentacion';
    		//exit();
    	}
    	
    	//Sentencia a la BBDD
    	$sentenciaSQL = 'SELECT '.$valCampos.' FROM '.$tabla;
		//echo $sentenciaSQL;

    	//COMPROBAR CONDICION
    	if ($condicion != null) {
    		$restriccion = self::whereConuslta($condicion);
	    	$sentenciaSQL = $sentenciaSQL." ".$restriccion;
	    }
    	
    	//COMPROBAR AGRUPACION
    	if ($agrupacion != null) {    		
    		$grupo = self::groupConuslta($agrupacion);
	    	$sentenciaSQL = $sentenciaSQL.' '.$grupo;
	    }

    	//COMPROBAR ORDEN
	    if ($ordenacion != null) {    		
			$orden = self::orderConuslta($ordenacion);
	    	$sentenciaSQL = $sentenciaSQL." ".$orden;
	    }

        //COMPROBAR LIMITE
        if ($limite != null) {          
            $orden = self::limitarConuslta($limite);
            $sentenciaSQL = $sentenciaSQL." ".$orden;
        }

		//echo $sentenciaSQL;
		//Ejecución en BBDD
		$this->resultado = $this->consulta->query($sentenciaSQL);  	
    	
		return $this->resultado;
	}
	//Unir campos para consulta
	function unirCampos($campos,$accion){
		$valCampos = ""; //Variable para unir los campos
		//Recorremos el array para filtrar cada campo	
		for ($i=0; $i < count($campos); $i++) {
			//Ultimo Campo
            if($i==count($campos)-1){
				switch ($accion) {
                    case 'C':
                        $valCampos = $valCampos.$campos[$i];
                    break;
                    case 'I':
                        $valCampos = $valCampos.$campos[$i]['valor'].');';
                    break;
                    case 'A':
                        $valCampos = $valCampos.$campos[$i]['campo'].'='.$campos[$i]['valor'];;
                    break;
                    default:
                        $valCampos = $valCampos.$campos[$i];
                    break;
                }
			}else{
				switch ($accion) {
                    case 'C':
                        $valCampos = $valCampos.$campos[$i].', ';
                    break;
                    case 'I':
                        $valCampos = $valCampos.$campos[$i]['valor'].');';
                    break;
                    case 'A':
                        $valCampos = $valCampos.$campos[$i]['campo'].'='.$campos[$i]['valor'].', ';
                    break;
                    default:
                        $valCampos = $valCampos.$campos[$i].', ';
                    break;
                }	
			}
		}
		return $valCampos;
	}
	//Añadir condicion en la consulta (WHERE)
	function whereConuslta($condicion){
		if (is_string($condicion)) {
    		$restriccion = 'WHERE '.$condicion;
    	}
    	else if (is_array($condicion)) {
    		for ($i=0; $i < count($condicion); $i++) { 
    			if ($i == 0) {
    				$restriccion = 'WHERE '.$condicion[$i];
    			}
    			else{
    				$restriccion = $restriccion.' AND '.$condicion[$i];
    			}
    		}
    	}
    	else{
    		//Error doc
    		$restriccion = false;
    		$resultado = 'La condición no se ha introducido de forma correcta';
    		//exit();
    	}
    	return $restriccion;
	}
 	//Añadir agrupación en la consulta (GROUP BY)
	function groupConuslta($agrupacion){
		if (is_string($agrupacion)) {
    		$grupo = 'GROUP BY '.$agrupacion;
    	}
    	else if (is_array($agrupacion)) {
    		for ($i=0; $i < count($agrupacion); $i++) { 
    			if ($i == 0 && $agrupacion[$i] != 'HAVING') {
    				$grupo = 'GROUP BY '.$agrupacion[$i];
    			}
    			else if($agrupacion[$i] == 'HAVING'){
    				$grupo = $grupo.' '.$agrupacion[$i];
    				$i++;
    				$grupo = $grupo.' '.$agrupacion[$i];
    			}
    			else{
    				$grupo = $grupo.', '.$agrupacion[$i];
    			}
    		}
    	}
    	else{
    		//Error doc
    		$grupo = false;
    		$resultado = 'La agrupación de campos no se ha introducido de forma correcta';
    		//exit();
    	}
    	return $grupo;
	}
	//Añadir orden en la consulta (ORDER BY)
	function orderConuslta($ordenacion){
		if (is_string($ordenacion)) {
    		$orden = 'ORDER BY '.$ordenacion;
    	}
    	else if (is_array($ordenacion)) {
    		for ($i=0; $i < count($ordenacion); $i++) { 
    			if ($i == 0) {
    				$orden = 'ORDER BY '.$ordenacion[$i];
    			}
    			else{
    				$orden = $orden.', '.$ordenacion[$i];
    			}
    		}
    	}
    	else{
    		//Error doc
    		$ordenacion = false;
    		$resultado = 'El orden de los campos no se ha introducido de forma correcta';
    		//exit();
    	}
    	return $orden;
	}
    //Añadir limite en la consulta (LIMIT)
    function limitarConuslta($limit){
        if (is_string($limit)) {
            $limite = 'LIMIT '.$limit;
        }
        else if (is_array($limit)) {
            for ($i=0; $i < count($limit); $i++) { 
                if ($i == 0) {
                    $limite = 'LIMIT '.$limit[$i];
                }
                else{
                    $limite = $limite.' OFFSET '.$limit[$i];
                }
            }
        }
        else{
            //Error doc
            $limite = false;
            $resultado = 'El orden de los campos no se ha introducido de forma correcta';
            //exit();
        }
        return $limite;
    }
    
}//Fin de la Clase

?>