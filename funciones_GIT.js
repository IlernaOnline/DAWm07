///////////////////////////////////////////////////////
////CAMBIO DE OPCION DEL MENU GENERAL DE LA WEB////////
///////////////////////////////////////////////////////
function menu(opcion,button){
	if (opcion != null) {
		button.className = "active";
		postSincAjax("controlador/menu.php","op="+opcion, "cuerpo");
		switch(opcion){
			case 1:
				postAsincAjax("controlador/consulta_portada.php", "zona="+opcion,'portada');	//datos de la portada
			break;
			case 2:
				//Opcion 2
			break;
			default:
				postAsincAjax("controlador/consulta_portada.php", "zona=1",'portada');	//datos de la portada
			break;
		}
	}
}

///////////////////////////////////////////////////////
/////METODO POST AJAX ASÍNCRONO////////////////////////
///////////////////////////////////////////////////////
function postAsincAjax(url, data, capa){
	// Creamos el objeto XMLHttpRequest 
	var xhr= new XMLHttpRequest();
	//Ejecutamos el método open() de XMLHttpReques con el método Post (en vez del GET,el post lleva la info oculta, me gusta mas),el path del php y decimos si es sincrona o asincrona....
	//Al poner true estamos haciendo que el js no se pare a esperar la respuesta del server, sino que continua ejecutando las lineas mientras se realiza la consulta con la bbdd en segundo plano
    xhr.open("POST", url, true);
	//Creamos la cabecera para poder mandar información (clave:valor)
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//Ejecutamos el método que esta continuamente escuchando asignándole una función que sera la que nos devuelva el contenido del php
    xhr.onreadystatechange = function() {
		//El readystate hace referencia al estado de la conexión (0 = sin inicializar, 1 = abierto, 2 = cabeceras recibidas, 3 = cargando y 4 = completado.)
		// el status devuelve el estado como un número (p. ej. 404 para "Not Found" y 200 para "OK").
		if(xhr.readyState == 4 && xhr.status == 200) {
			var respuesta = xhr.responseText;//Obtenemos la respuesta del php
			//alert(respuesta);
			document.getElementById(capa).innerHTML = respuesta; 
		}
	}
    xhr.send(data);
}
///////////////////////////////////////////////////////
/////METODO POST AJAX SÍNCRONO/////////////////////////
///////////////////////////////////////////////////////
function postSincAjax(url, data, capa){
	// Creamos el objeto XMLHttpRequest 
	var xhr= new XMLHttpRequest();
	//Ejecutamos el método open() de XMLHttpReques con el método Post (en vez del GET,el post lleva la info oculta, me gusta mas),el path del php y decimos si es sincrona o asincrona....
	//Al poner true estamos haciendo que el js no se pare a esperar la respuesta del server, sino que continua ejecutando las lineas mientras se realiza la consulta con la bbdd en segundo plano
    xhr.open("POST", url, false);
	//Creamos la cabecera para poder mandar información (clave:valor)
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//Ejecutamos el método que esta continuamente escuchando asignándole una función que sera la que nos devuelva el contenido del php
    xhr.onreadystatechange = function() {
		//El readystate hace referencia al estado de la conexión (0 = sin inicializar, 1 = abierto, 2 = cabeceras recibidas, 3 = cargando y 4 = completado.)
		// el status devuelve el estado como un número (p. ej. 404 para "Not Found" y 200 para "OK").
		if(xhr.readyState == 4 && xhr.status == 200) {
			var respuesta = xhr.responseText;//Obtenemos la respuesta del php
			//alert(respuesta);
			document.getElementById(capa).innerHTML = respuesta; 
		}
	}
    xhr.send(data);
}

