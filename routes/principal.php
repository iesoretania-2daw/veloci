<?php
	/*  VELOCI - Web application for management races
  Copyright (C) 2014: Adrián Pérez López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/]. */

$app->get('/', function() use ($app) {
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$noticias = cargarNoticiasPaginacion(4,0);
	$nNoticias = cargarNnoticias(4);
	$categorias = cargarCategorias();
	$reclamaciones = cargarReclamacionesRecientes();

	if(!isset($_SESSION['id'])) {
		if (isset($_COOKIE['nombre'])) {
			$nombreGuardado = $_COOKIE['nombre'];
			$app->render('principal.html.twig', array('carrera' => $carrera, 'noticias' => $noticias, 'categorias' => $categorias, 'nNoticias' => $nNoticias, 'pagina' => 1, 'reclamaciones' => $reclamaciones, 'nombreGuardado' => $nombreGuardado, 'configuracion' => $configuracion));
		} else {
			$app->render('principal.html.twig', array('carrera' => $carrera, 'noticias' => $noticias, 'categorias' => $categorias, 'nNoticias' => $nNoticias, 'pagina' => 1, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
		}
	} else {
		$app->render('principal.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carrera' => $carrera, 'noticias' => $noticias, 'categorias' => $categorias, 'nNoticias' => $nNoticias, 'pagina' => 1, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	}
})->name('principal');

$app->get('/i:idPagina', function($idPagina) use ($app) {
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$noticias = cargarNoticiasPaginacion(4,$idPagina-1);
	$nNoticias = cargarNnoticias(4);
	$categorias = cargarCategorias();
	$reclamaciones = cargarReclamacionesRecientes();

	if(!isset($_SESSION['id'])) {
		$app->render('principal.html.twig', array('carrera' => $carrera, 'noticias' => $noticias, 'categorias' => $categorias, 'nNoticias' => $nNoticias, 'pagina' => $idPagina, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	} else {
		$app->render('principal.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carrera' => $carrera, 'noticias' => $noticias, 'categorias' => $categorias, 'nNoticias' => $nNoticias, 'pagina' => $idPagina, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	}
})->name('principalPaginada');

$app->post('/', function() use ($app) {
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$noticias = cargarNoticiasPaginacion(4,1);
	$nNoticias = cargarNnoticias(4);
	$reclamaciones = cargarReclamacionesRecientes();

	if (isset($_POST['login'])) {
		$acceso = testAccess($app, $_POST['inputUsuario'], $_POST['inputPassword']);
	}

	if (!$acceso) {
		$app->render('principal.html.twig', array('alertLogin' => 'Usuario o contraseña incorrectos', 'carrera' => $carrera, 'noticias' => $noticias, 'nNoticias' => $nNoticias, 'pagina' => 1, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
		echo "<script type='text/javascript'>alertify.error('Error: usuario o contraseña incorrectos');</script>";
	} else {
		if ($_POST['recordar']) {
			setcookie('nombre', $_POST['inputUsuario']);
		}
		$app->render('principal.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carrera' => $carrera, 'noticias' => $noticias, 'nNoticias' => $nNoticias, 'pagina' => 1, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
		echo "<script type='text/javascript'>alertify.success('Usuario identificado correctamente');</script>";
	}
})->name('accederPrincipal');

$app->get('/salir', function() use ($app) {
	$configuracion = datosApp();
	$reclamaciones = cargarReclamacionesRecientes();
	session_destroy();
	$app->redirect($app->urlFor('principal'));

	$app->render('principal.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'pagina' => 1, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
})->name('cerrarSesion');

$app->get('/pilotos', function() use ($app) {
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$usuarios = cargarUsuarios();
	$reclamaciones = cargarReclamacionesRecientes();

	if(!isset($_SESSION['id'])) {
		$app->render('pilotos.html.twig', array('carrera' => $carrera, 'usuarios' => $usuarios, 'configuracion' => $configuracion));
	} else {
		$app->render('pilotos.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'usuarios' => $usuarios, 'reclamaciones' => $reclamaciones, 'carrera' => $carrera, 'configuracion' => $configuracion));
	}
})->name('pilotos');

$app->get('/categorias', function() use ($app) {
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$categorias = cargarCategorias();
	$reclamaciones = cargarReclamacionesRecientes();

	if(!isset($_SESSION['id'])) {
		$app->render('categorias.html.twig', array('carrera' => $carrera, 'categorias' => $categorias, 'configuracion' => $configuracion));
	} else {
		$app->render('categorias.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'categorias' => $categorias, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	}
})->name('categorias');

$app->get('/carreras', function() use ($app) {
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$carreras = cargarCarreras();
	$reclamaciones = cargarReclamacionesRecientes();

	if(!isset($_SESSION['id'])) {
		$app->render('carreras.html.twig', array('carrera' => $carrera, 'carreras' => $carreras, 'configuracion' => $configuracion));
	} else {
		$app->render('carreras.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carreras' => $carreras, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	}
})->name('carreras');

$app->get('/reclamaciones', function() use ($app) {
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$carreras = cargarCarrerasReclamacion();
	$reclamaciones = cargarReclamacionesRecientes();

	if(!isset($_SESSION['id'])) {
		$app->render('reclamaciones.html.twig', array('carrera' => $carrera, 'carreras' => $carreras, 'configuracion' => $configuracion));
	} else {
		$app->render('reclamaciones.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carreras' => $carreras, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	}
})->name('reclamaciones');

$app->get('/reclamaciones/:idCarrera', function($idCarrera) use ($app) {
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$reclamaciones = cargarReclamaciones($idCarrera);

	if(isset($_SESSION['id']) && isset($_SESSION['rol'])) {
		if($_SESSION['rol'] >= 1) {
			$app->render('listaReclamaciones.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
		} else {
			$carreras = cargarCarrerasReclamacion();
			$app->render('reclamaciones.html.twig', array('carrera' => $carrera, 'alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras, 'configuracion' => $configuracion));
		}
	} else {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('carrera' => $carrera, 'alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras, 'configuracion' => $configuracion));
	}
})->name('listaReclamaciones');

$app->get('/reclamacion/nueva/:idCarrera', function($idCarrera) use ($app) {
	$carrera = cargarCarrera();
	$configuracion = datosApp();
	$datosCarrera = cargarDatosCarrera($idCarrera);
	$categoria = cargarCategorias($datosCarrera['categoria_id']);
	$pilotos = cargarUsuariosReclamar();
	$reclamaciones = cargarReclamacionesRecientes();

	if(isset($_SESSION['id']) && isset($_SESSION['rol'])) {
		if($_SESSION['rol'] >= 1) {
			$app->render('nuevaReclamacion.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carrera' => $carrera, 'categoria' => $categoria, 'pilotos' => $pilotos, 'idRace' => $idCarrera, 'reclamaciones' => $reclamaciones, 'datosCarrera' => $datosCarrera, 'configuracion' => $configuracion));
		} else {
			$carreras = cargarCarrerasReclamacion();
			$app->render('reclamaciones.html.twig', array('alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $datosCarrera, 'reclamaciones' => $reclamaciones, 'carrera' => $carrera, 'configuracion' => $configuracion));
		}
	} else {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	}
})->name('nuevaReclamacion');

$app->post('/reclamacion', function() use ($app) {
	$configuracion = datosApp();
	$nIncidente = crearReclamacion($app, $_POST['inputTitulo'], $_POST['inputAclaracion'], $_POST['inputVuelta'], $_POST['inputMinuto'], $_POST['inputIdCarrera'], $_POST['inputIdUsuario'], $_POST['inputPiloto']);
	$carrera = cargarDatosCarrera($_POST['inputIdCarrera']);
	$categoria = cargarCategorias(1);
	$pilotos = cargarUsuarios();
	$reclamaciones = cargarReclamacionesRecientes();

	if(isset($_SESSION['id']) || $_SESSION['rol'] <= 0) {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	} else {
		$app->redirect('/reclamacion/' . $nIncidente);
	}
})->name('crearReclamacion');

$app->get('/reclamacion/:idReclamacion', function($idReclamacion) use ($app) {
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$comentarios = cargarReclamacion($idReclamacion);
	$pilotosR = pilotosReclamados($idReclamacion);
	$reclamaciones = cargarReclamacionesRecientes();
	$sancionados = cargarSancionados($idReclamacion);
	$abierto = incidenteAbierto($idReclamacion);
	$recursos = cargarRecursos($idReclamacion);

	if(!isset($_SESSION['id']) || $_SESSION['rol'] <= 0) {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('carrera' => $carrera, 'alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	} else {
		$app->render('reclamacion.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'comentarios' => $comentarios, 'idReclamacion' => $idReclamacion, 'pilotosR' => $pilotosR, 'reclamaciones' => $reclamaciones, 'sancionados' => $sancionados, 'idReclamacion' => $idReclamacion, 'abierto' => $abierto['abierto'], 'recursos' => $recursos, 'configuracion' => $configuracion));
	}
})->name('reclamacion');

$app->post('/reclamacion/s:idReclamacion', function($idReclamacion) use ($app) {
	sancionarReclamacion($_POST['inputPiloto'], $_POST['cReclamacion'], $_POST['inputSancion']);
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$comentarios = cargarReclamacion($idReclamacion);
	$pilotosR = pilotosReclamados($idReclamacion);
	$reclamaciones = cargarReclamacionesRecientes();
	$sancionados = cargarSancionados($idReclamacion);
	$abierto = incidenteAbierto($idReclamacion);

	if(!isset($_SESSION['id']) || $_SESSION['rol'] <= 0) {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('carrera' => $carrera, 'alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	} else {
		$app->render('reclamacion.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'comentarios' => $comentarios, 'idReclamacion' => $idReclamacion, 'pilotosR' => $pilotosR, 'reclamaciones' => $reclamaciones, 'sancionados' => $sancionados, 'idReclamacion' => $idReclamacion, 'abierto' => $abierto['abierto'], 'configuracion' => $configuracion));
	}
})->name('sancionarReclamacion');

$app->post('/reclamacion/ns:idReclamacion', function($idReclamacion) use ($app) {
	noSancionarReclamacion($_POST['inputPiloto'], $_POST['cReclamacion']);
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$comentarios = cargarReclamacion($idReclamacion);
	$pilotosR = pilotosReclamados($idReclamacion);
	$reclamaciones = cargarReclamacionesRecientes();
	$sancionados = cargarSancionados($idReclamacion);
	$abierto = incidenteAbierto($idReclamacion);

	if(!isset($_SESSION['id']) || $_SESSION['rol'] <= 0) {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('carrera' => $carrera, 'alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	} else {
		$app->render('reclamacion.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'comentarios' => $comentarios, 'idReclamacion' => $idReclamacion, 'pilotosR' => $pilotosR, 'reclamaciones' => $reclamaciones, 'sancionados' => $sancionados, 'idReclamacion' => $idReclamacion, 'abierto' => $abierto['abierto'], 'configuracion' => $configuracion));
	}
})->name('noSancionarReclamacion');

$app->post('/reclamacion/c:idReclamacion', function($idReclamacion) use ($app) {
	cerrarReclamacion($_POST['cReclamacion']);
	$configuracion = datosApp();
	$carrera = cargarCarrera();
	$comentarios = cargarReclamacion($idReclamacion);
	$pilotosR = pilotosReclamados($idReclamacion);
	$reclamaciones = cargarReclamacionesRecientes();
	$sancionados = cargarSancionados($idReclamacion);
	$abierto = incidenteAbierto($idReclamacion);

	if(!isset($_SESSION['id']) || $_SESSION['rol'] <= 0) {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('carrera' => $carrera, 'alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	} else {
		$app->render('reclamacion.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'comentarios' => $comentarios, 'idReclamacion' => $idReclamacion, 'pilotosR' => $pilotosR, 'reclamaciones' => $reclamaciones, 'sancionados' => $sancionados, 'idReclamacion' => $idReclamacion, 'configuracion' => $configuracion));
	}
})->name('cerrarReclamacion');

$app->post('/reclamacion/:idReclamacion', function($idReclamacion) use ($app) {
	$configuracion = datosApp();
	crearComentario($app, $_POST['inputTitulo'], $_POST['inputComentario'], $_POST['inputID']);
	agregarRecurso($_FILES['inputRecurso'], $idReclamacion);
	$carrera = cargarCarrera();
	$comentarios = cargarReclamacion($idReclamacion);
	$pilotosR = pilotosReclamados($idReclamacion);
	$reclamaciones = cargarReclamacionesRecientes();

	if(isset($_SESSION['id']) || $_SESSION['rol'] <= 0) {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('carrera' => $carrera, 'alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	} else {
		$app->render('reclamacion.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'comentarios' => $comentarios, 'idReclamacion' => $idReclamacion, 'pilotosR' => $pilotosR, 'reclamaciones' => $reclamaciones, 'configuracion' => $configuracion));
	}
})->name('nuevoComentario');

$app->get('/noticia/:idNoticia', function($idNoticia) use ($app) {
	$configuracion = datosApp();
	$noticias = cargarNoticias();
	$noticia = cargarNoticia($idNoticia);
	$carrera = cargarCarrera();
	$comentarios = cargarComentarios($idNoticia);
	$nComentarios = numeroComentarios($idNoticia);

	if(!isset($_SESSION['id'])) {
		$app->render('noticia.html.twig', array('carrera' => $carrera, 'noticias' => $noticias, 'noticia' => $noticia, 'comentarios' => $comentarios, 'nComentarios' => $nComentarios, 'configuracion' => $configuracion));
	} else {
		$app->render('noticia.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'noticias' => $noticias, 'noticia' => $noticia, 'comentarios' => $comentarios, 'nComentarios' => $nComentarios, 'configuracion' => $configuracion));
	}
})->name('noticia');

$app->post('/noticia/:idNoticia', function($idNoticia) use ($app) {
	$configuracion = datosApp();
	$noticias = cargarNoticias();
	$noticia = cargarNoticia($idNoticia);
	$carrera = cargarCarrera();

	if(isset($_SESSION['id'])) {
		if(isset($_POST['nuevoComentario'])) {
			enviarComentario($_POST['inputComentario'], $_POST['inputResponde'], $_SESSION['id'], $_POST['inputNoticia']);
			$comentarios = cargarComentarios($idNoticia);
			$nComentarios = numeroComentarios($idNoticia);
			contarComentarios($idNoticia);
		} else {
			$comentarios = cargarComentarios($idNoticia);
			$nComentarios = numeroComentarios($idNoticia);
		}

		$app->render('noticia.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'noticias' => $noticias, 'noticia' => $noticia, 'comentarios' => $comentarios, 'nComentarios' => $nComentarios, 'configuracion' => $configuracion));
		echo "<script>alertify.alert('No estoy seguro, pero creo que estas intentado hacer cosas malas...');</script>";
	} else {
		$comentarios = cargarComentarios($idNoticia);
		$nComentarios = numeroComentarios($idNoticia);
		$app->render('noticia.html.twig', array('carrera' => $carrera, 'noticias' => $noticias, 'noticia' => $noticia, 'comentarios' => $comentarios, 'nComentarios' => $nComentarios, 'configuracion' => $configuracion));
		echo "<script>alertify.alert('No estoy seguro, pero creo que estas intentado hacer cosas malas...');</script>";
	}
})->name('comentar');

$app->post('/noticia/comentario/:idNoticia', function($idNoticia) use ($app) {
	$configuracion = datosApp();
	$noticias = cargarNoticias();
	$noticia = cargarNoticia($idNoticia);
	$carrera = cargarCarrera();
	$error = eliminarComentario($_POST['no'], $_POST['co']);
	contarComentarios($idNoticia);
	$comentarios = cargarComentarios($idNoticia);
	$nComentarios = numeroComentarios($idNoticia);

	if(!isset($_SESSION['id'])) {
		$app->render('noticia.html.twig', array('carrera' => $carrera, 'noticias' => $noticias, 'noticia' => $noticia, 'comentarios' => $comentarios, 'nComentarios' => $nComentarios, 'configuracion' => $configuracion));
	} else {
		$app->render('noticia.html.twig', array('carrera' => $carrera, 'id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'noticias' => $noticias, 'noticia' => $noticia, 'comentarios' => $comentarios, 'nComentarios' => $nComentarios, 'configuracion' => $configuracion));
	}

	if ($error) {
		echo "<script>alertify.alert('No puede borrar un comentario que no es suyo.');</script>";
	}
})->name('borrarComentario');


$app->get('/asistencias', function() use ($app) {
	$carrera = cargarCarrera();
	$configuracion = datosApp();
    $carreras = cargarCarreras();
    $pilotos = cargarUsuarios();
    $estados = carreraControlAsistencia();
    
    if(!isset($_SESSION['id'])) {
		$app->render('asistencias.html.twig', array('carreras' => $carreras, 'pilotos' => $pilotos, 'estados' => $estados, 'carrera' => $carrera, 'configuracion' => $configuracion));
	} else {
		$app->render('asistencias.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carreras' => $carreras, 'pilotos' => $pilotos, 'estados' => $estados, 'carrera' => $carrera, 'configuracion' => $configuracion));
	}   
})->name('asistencias');

function testAccess($app, $usuario, $pass) {
	$usuario = strtolower($usuario);
	$user = ORM::for_table('piloto')->where('nombre', $usuario)->find_one();
	if ($user['nombre'] == $usuario && password_verify($pass, $user['password'])) {
		$_SESSION['id'] = $user['id'];
		$_SESSION['nombre_completo'] = $user['nombre_completo'];
		$_SESSION['rol'] = $user['rol'];
		$_SESSION['avatar'] = $user['avatar'];
		return true;
	} else {
		return false;
	}
}

/**
* Carga la próxima carrera
* 
* Recoge todas las carreras y las ordena por fechas más próximas, aplica un limit 1
* y devuelve la próxima carrera
*
* @category Carreras
* @example principal.php cargarCarrera();
*
* @return object
*/
function cargarCarrera() {
	return ORM::for_table('carrera')->where_gt('fecha', date("Y-m-d"))->order_by_asc('fecha')->limit(1)->find_many();
}

/**
* Carga todos los datos para una carrera específicada
*
* @category Carreras
* @example principal.php cargarDatosCarrera(9)
* @param integer $idCarrera ID de la carrera
*
* @return object
*/
function cargarDatosCarrera($idCarrera) {
	return ORM::for_table('carrera')->where('id', $idCarrera)->find_one();
}

/**
* Carga todos los datos de la categorías específicada
*
* @category Categorías
* @example principal.php cargarDatosCategoria(8);
* @param integer $idCategoria ID de la categoría
*
* @return object
*/
function cargarDatosCategoria($idCategoria) {
	return ORM::for_table('categoria')->where('id', $idCategoria)->find_one();
}

/**
* Devuelve la lista de carreras que tienen el plazo de reclamación abierto
*
* @category Carreras
* @example principal.php cargarCarrerasReclamacion();
*
* @return object
*/
function cargarCarrerasReclamacion() {
    return ORM::for_table('carrera')->
    where_lte('fecha', date("Y-m-d"))->
    where_gte('fecha_limite', date("Y-m-d"))->
    where_lt('hora', date("H:M:s"))->
    where_gt('hora_limite', date("H:m:s"))->
    order_by_asc('fecha')->find_many();
}

/**
* Devuelve las 3 últimas reclamaciones abiertas para cualquier carrera y categoría
*
* @category Reclamaciones
* @example principal.php cargarReclamacionesRecientes();
*
* @return object
*/
function cargarReclamacionesRecientes() {
	return ORM::for_table('incidente')->
	join('carrera', array('carrera_id', '=', 'carrera.id'))->
	join('reclamacion', array('incidente.id', '=', 'reclamacion.incidente_id'))->
	join('piloto_incidente', array('incidente.id', '=', 'piloto_incidente.incidente_id'))->
	join('piloto', array('piloto.id', '=', 'piloto_incidente.piloto_id'))->
	where('piloto_incidente.reclama', 1)->
	where_gt('carrera.fecha_limite', date("Y-m-d"))->
	order_by_asc('incidente.id')->
	group_by('piloto_incidente.id')->
	limit(3)->
	select_many('reclamacion.incidente_id', 'vuelta', 'minuto', 'nombre_completo', 'reclama', 'titulo', 'sancion')->find_many();
}

/**
* Devuelve las sanciones de los pilotos sancionados.
* 
* Consulta si el piloto ha sido sancionado y en caso de serlo devuelve el comentario
* con su sanción
*
* @category Reclamaciones
* @example principal.php cargarSancionados();
*
* @return object
*/
function cargarSancionados($idReclamacion) {
	return ORM::for_table('piloto')->
	join('piloto_incidente', array('piloto.id', '=', 'piloto_incidente.piloto_id'))->
	where('piloto_incidente.incidente_id', $idReclamacion)->where('piloto_incidente.sancion', '1')->
	where('piloto_incidente.reclama', '0')->
	find_many();
}

/**
* Carga los datos de las reclamaciones para la carrera específicada
*
* @category Reclamaciones
* @example principal.php cargarReclamaciones(23);
* @param integer $race ID de la carrera
*
* @return object
*/
function cargarReclamaciones($race) {
	return ORM::for_table('incidente')->
	join('reclamacion', array('incidente.id', '=', 'reclamacion.incidente_id'))->
	join('piloto_incidente', array('incidente.id', '=', 'piloto_incidente.incidente_id'))->
	join('piloto', array('piloto.id', '=', 'piloto_incidente.piloto_id'))->
	where('carrera_id', $race)->where('piloto_incidente.reclama', 1)->order_by_asc('vuelta')->
	group_by('piloto_incidente.id')->
	select_many('reclamacion.incidente_id', 'vuelta', 'minuto', 'nombre_completo', 'reclama', 'titulo')->find_many();
}

/**
* Carga la reclamación específicada
*
* @category Reclamaciones
* @example principal.php cargarReclamacion(102);
* @param integer $idReclamacion ID de la reclamación
*
* @return object
*/
function cargarReclamacion($idReclamacion) {
	return ORM::for_table('reclamacion')->
	join('piloto', array('piloto.id', '=', 'reclamacion.piloto_id'))->
	join('piloto_incidente', array('piloto_incidente.incidente_id', '=', 'reclamacion.incidente_id'))->group_by('reclamacion.id')->
	where('reclamacion.incidente_id', $idReclamacion)->
	find_many();
}

/**
* Crea una nueva reclamación
*
* Inserta los registros con el usuario que reclama y los reclamados
*
* @category Reclamaciones
* @example principal.php crearReclamacion('Me golpea y me rompe el coche', 'En la cuarta curva me rompe el aleron', 2, 6, 14, Array(6, 7, 5));
* @param string $titulo Título para la reclamación
* @param string $comentario Descripción del incidente
* @param integer $vuelta Vuelta en la que se produce el incidente
* @param integer $minuto Minuto de la vuelta en la que ocurre el incidente
* @param integer $reclama ID del usuario que crea la reclamación
* @param Array $sereclama Usuarios que son reportados en la reclamación
*
* @return integer
*/
function crearReclamacion($app, $titulo, $comentario, $vuelta, $minuto, $carrera, $reclama, $sereclama) {
	$i = 0;

	$nincidentes = ORM::for_table('incidente')->max('id');
	$nincidentes++;

	$incidente = ORM::for_table('incidente')->create();
	$incidente->id = null;
	$incidente->vuelta = $vuelta;
	$incidente->minuto = $minuto;
	$incidente->carrera_id = $carrera;

	$reclamacion = ORM::for_table('reclamacion')->create();
	$reclamacion->titulo = $titulo;
	$reclamacion->comentario = $comentario;
	$reclamacion->incidente_id = $nincidentes;
	$reclamacion->piloto_id = $reclama;

	$piloto_incidente = ORM::for_table('piloto_incidente')->create();
	$piloto_incidente->piloto_id = $reclama;
	$piloto_incidente->incidente_id = $nincidentes;
	$piloto_incidente->reclama = 1;
	$piloto_incidente->sancion = 0;

	$incidente->save();
	$reclamacion->save();
	$piloto_incidente->save();

	while ($i < count($sereclama)) {
		if ($sereclama[$i] != 0) {

			$piloto_incidente = ORM::for_table('piloto_incidente')->create();
			$piloto_incidente->piloto_id = $sereclama[$i];
			$piloto_incidente->incidente_id = $nincidentes;
			$piloto_incidente->reclama = 0;
			$piloto_incidente->sancion = 0;

			$piloto_incidente->save();
			$i++;
		}
	}

	return $nincidentes;
}

/**
* Añade un comentario a una reclamación
*
* @category Reclamaciones
* @example principal.php crearComentario('No estoy de acuerdo', 'Yo no tengo la culpa', 5)
* @param string $titulo Título para el comentario
* @param string $comentario Texto del comentario (mensaje)
* @param integer $id ID del incidente para el que se pone el comentario
*
* @return void
*/
function crearComentario($app, $titulo, $comentario, $id) {
	$comment = ORM::for_table('reclamacion')->create();
	$comment->titulo = $titulo;
	$comment->comentario = $comentario;
	$comment->incidente_id = $id;
	$comment->piloto_id = $_SESSION['id'];

	$comment->save();
}

/**
* Devuelve los pilotos que están reclamados para un determinado incidente
*
* @category Reclamaciones
* @example principal.php pilotosReclamados(6)
* @param integer $idReclamacion ID de la reclamación de la que queremos obtener los pilotos reclamados
*
* @return object
*/
function pilotosReclamados($idReclamacion) {
	return ORM::for_table('piloto')->
	join('piloto_incidente', array('piloto.id', '=', 'piloto_incidente.piloto_id'))->
	where('piloto_incidente.reclama', '0')->
	where('piloto_incidente.incidente_id', $idReclamacion)->
	select_many('piloto.id', 'piloto.avatar', 'piloto.nombre_completo', 'piloto_incidente.reclama', 'piloto_incidente.sancion', 'piloto_incidente.nota')->
	find_many();
}

/**
* Crea un comentario o responde a otro usuario en una noticia
*
* @category Noticias
* @example principal.php enviarComentario('comentario', Array(1,3,5), 5, 24)
* @param string $comentario Texto del comentario
* @param Array $responde_a Usuario al que se responde en el comentario, en caso de ser un array vacío tendra valor 0
* @param integer $usuario ID del usuario que publica el comentario
* @param integer $noticia ID de la noticia para la que se crea el comentario
*
* @return void
*/
function enviarComentario($comentario, $responde_a, $usuario, $noticia) {
	$comment = ORM::for_table('comentario')->create();
	$comment->id = null;
	$comment->comentario = $comentario;
	$comment->responde_a = $responde_a;
	$comment->fecha = date("Y-n-d H:i:s");
	$comment->usuario_id = $usuario;
	$comment->noticia_id = $noticia;
	$comment->save();
}

/**
* Carga los comentarios para una determinada noticia
*
* @category Noticias
* @example principal.php cargarComentarios(27)
* @param integer $noticia ID de la noticia de la que cargar los comentarios
*
* @return object
*/
function cargarComentarios($noticia) {
	return ORM::for_table('comentario')->
	join('piloto', array('comentario.usuario_id', '=', 'piloto.id'))->
	where('noticia_id', $noticia)->
	select_many('piloto.id', 'comentario.id', 'nombre_completo', 'avatar', 'noticia_id', 'comentario', 'fecha', 'usuario_id')->find_many();
}

/**
* Cuenta el número de comentarios para una noticia específicada
*
* @category Noticias
* @example principal.php numeroComentarios(34)
* @param integer $noticia ID de la noticia de la que cargar los comentarios
*
* @return object
*/
function numeroComentarios($idNoticia) {
	return ORM::for_table('comentario')->where('noticia_id', $idNoticia)->count();
}

/**
* Devuelve los pilotos que pueden ser reclamados
*
* Esto evita que pueda haber reclamaciones en contra de cuentas de administracción como la FIA.
*
* @category Noticias
* @example principal.php cargarUsuariosReclamar()
*
* @return object
*/
function cargarUsuariosReclamar() {
	return ORM::for_table('piloto')->where('puede_reclamarse', '1')->find_many();
}

function cerrarReclamacion($idReclamacion) {
	$cerrar = ORM::for_table('incidente')->find_one($idReclamacion);
	$cerrar->abierto = 0;
	$cerrar->save();
}

function sancionarReclamacion($idPiloto, $idReclamacion, $comentario) {
	$s = ORM::for_table('piloto_incidente')->
	where('piloto_id', $idPiloto)->
	where('incidente_id', $idReclamacion)->
	find_one();

	$s->sancion = 1;
	$s->nota = $comentario;
	$s->save();
}

function noSancionarReclamacion($idPiloto, $idReclamacion) {
	$sancion = ORM::for_table('piloto_incidente')->
	where('piloto_id', $idPiloto)->
	where('incidente_id', $idReclamacion)->
	find_one();

	$sancion->sancion = 2;
	$sancion->save();
}

function incidenteAbierto($idReclamacion) {
	return ORM::for_table('incidente')->select('abierto')->find_one($idReclamacion);
}

function agregarRecurso($imagen, $idReclamacion) {
	$maximo = ORM::for_table('recurso')->MAX('id');
	$maximo++;
    if ($_FILES['inputRecurso']['error'] > 0) {
        //echo "error";
    } else {
        $ok = array("image/jpg", "image/jpeg", "image/gif", "image/png");
        $limite_kb = 100;
        
        $ext = imgExt($_FILES['inputRecurso']['name']);

        if (in_array($_FILES['inputRecurso']['type'], $ok) && $_FILES['inputRecurso']['size'] <= $limite_kb * 1024) {
            $ruta = "images/recursos/" . $maximo . $ext;
            
                $resultado = @move_uploaded_file($_FILES['inputRecurso']['tmp_name'], $ruta);
                if ($resultado) {
                    $recurso = ORM::for_table('recurso')->create();
                    $recurso->id = null;
                    $recurso->descripcion = "texto";
                    $recurso->imagen = $ruta;
                    $recurso->reclamacion_id = $idReclamacion;
                    $recurso->save();
                } else {
                    //echo "ERROR";
                }
        } else {
            //echo "Archivo no permitido";
        }
    }
}

function imgExt($cadena) {
    $pos = stripos($cadena, '.');
    return substr($_FILES['inputRecurso']['name'], $pos);
}

function cargarRecursos($idReclamacion) {
	return ORM::for_table('recurso')->where('reclamacion_id', $idReclamacion)->find_many();
}

function contarComentarios($idNoticia) {
	$nComentarios = ORM::for_table('comentario')->where('noticia_id', $idNoticia)->count();
	$noticia = ORM::for_table('noticia')->find_one($idNoticia);
	$noticia->n_comentarios = $nComentarios;
	$noticia->save();
}

function eliminarComentario($idNoticia, $idComentario) {
	$comentario = ORM::for_table('comentario')->where('noticia_id', $idNoticia)->find_one($idComentario);
	if ($_SESSION['id'] == $comentario['usuario_id']) {
		$comentario->delete();
		$error = false;
	} else {
		$error = true;
	}
	return $error;
}