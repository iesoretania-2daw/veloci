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
	$carrera = cargarCarrera();

	if(!isset($_SESSION['id'])) {
		$app->render('principal.html.twig');
	} else {
		$app->render('principal.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carrera' => $carrera));
	}
})->name('principal');

$app->post('/', function() use ($app) {
	if (isset($_POST['login'])) {
		$acceso = testAccess($app, $_POST['inputUsuario'], $_POST['inputPassword']);
	}

	if (!$acceso) {
		$app->render('principal.html.twig', array('alertLogin' => 'Usuario o contraseña incorrectos'));
		echo "<script type='text/javascript'>alertify.error('Error: usuario o contraseña incorrectos');</script>";
	} else {
		$app->render('principal.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol']));
		echo "<script type='text/javascript'>alertify.success('Usuario identificado correctamente');</script>";
	}
})->name('accederPrincipal');

$app->get('/salir', function() use ($app) {
	session_destroy();
	$app->redirect($app->urlFor('principal'));

	$app->render('principal.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol']));
})->name('cerrarSesion');

$app->get('/pilotos', function() use ($app) {
	$usuarios = cargarUsuarios();

	if(!isset($_SESSION['id'])) {
		$app->render('pilotos.html.twig', array('usuarios' => $usuarios));
	} else {
		$app->render('pilotos.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'usuarios' => $usuarios));
	}
})->name('pilotos');

$app->get('/categorias', function() use ($app) {
	$categorias = cargarCategorias();

	if(!isset($_SESSION['id'])) {
		$app->render('categorias.html.twig', array('categorias' => $categorias));
	} else {
		$app->render('categorias.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'categorias' => $categorias));
	}
})->name('categorias');

$app->get('/carreras', function() use ($app) {
	$carreras = cargarCarreras();

	if(!isset($_SESSION['id'])) {
		$app->render('carreras.html.twig', array('carreras' => $carreras));
	} else {
		$app->render('carreras.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carreras' => $carreras));
	}
})->name('carreras');

$app->get('/reclamaciones', function() use ($app) {
	$carreras = cargarCarrerasReclamacion();

	if(!isset($_SESSION['id'])) {
		$app->render('reclamaciones.html.twig', array('carreras' => $carreras));
	} else {
		$app->render('reclamaciones.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carreras' => $carreras));
	}
})->name('reclamaciones');

$app->get('/listaReclamaciones/:idCarrera', function($idCarrera) use ($app) {
	$reclamaciones = cargarReclamaciones($idCarrera);

	if(!isset($_SESSION['id'])) {
		$app->render('reclamaciones.html.twig', array('carreras' => $reclamaciones));
	} else {
		$app->render('listaReclamaciones.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'reclamaciones' => $reclamaciones));
	}
})->name('listaReclamaciones');

$app->get('/nuevaReclamacion/:idCarrera', function($idCarrera) use ($app) {
	$carrera = cargarDatosCarrera($idCarrera);
	$categoria = cargarCategorias($carrera['categoria_id']);
	$pilotos = cargarUsuarios();

	if(!isset($_SESSION['id']) || $_SESSION['rol'] <= 1) {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras));
	} else {
		$app->render('nuevaReclamacion.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'carrera' => $carrera, 'categoria' => $categoria, 'pilotos' => $pilotos, 'idRace' => $idCarrera));
	}
})->name('nuevaReclamacion');

$app->post('/reclamacion', function() use ($app) {
	$nIncidente = crearReclamacion($app, $_POST['inputTitulo'], $_POST['inputAclaracion'], $_POST['inputVuelta'], $_POST['inputMinuto'], $_POST['inputIdCarrera'], $_POST['inputIdUsuario'], $_POST['inputPiloto']);
	$carrera = cargarDatosCarrera($_POST['inputIdCarrera']);
	$categoria = cargarCategorias(1);
	$pilotos = cargarUsuarios();

	if(!isset($_SESSION['id']) || $_SESSION['rol'] <= 0) {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras));
	} else {
		$app->redirect('/reclamacion/' . $nIncidente);
	}
})->name('crearReclamacion');

$app->get('/reclamacion/:idReclamacion', function($idReclamacion) use ($app) {
	$comentarios = cargarReclamacion($idReclamacion);
	$pilotosR = pilotosReclamados($idReclamacion);

	if(!isset($_SESSION['id']) || $_SESSION['rol'] <= 0) {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras));
	} else {
		$app->render('reclamacion.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'comentarios' => $comentarios, 'idReclamacion' => $idReclamacion, 'pilotosR' => $pilotosR));
	}
})->name('reclamacion');

$app->post('/reclamacion/:idReclamacion', function($idReclamacion) use ($app) {
	crearComentario($app, $_POST['inputTitulo'], $_POST['inputComentario'], $_POST['inputID']);
	$comentarios = cargarReclamacion($idReclamacion);
	$pilotosR = pilotosReclamados($idReclamacion);

	if(!isset($_SESSION['id']) || $_SESSION['rol'] <= 0) {
		$carreras = cargarCarrerasReclamacion();
		$app->render('reclamaciones.html.twig', array('alert' => "Error: No tiene permiso para acceder a esta zona, debe ser piloto oficial de la categoría", 'carreras' => $carreras));
	} else {
		$app->render('reclamacion.html.twig', array('id' => $_SESSION['id'], 'usuario' => $_SESSION['nombre_completo'], 'avatar' => $_SESSION['avatar'], 'rol' => $_SESSION['rol'], 'comentarios' => $comentarios, 'idReclamacion' => $idReclamacion, 'pilotosR' => $pilotosR));
	}
})->name('nuevoComentario');

function testAccess($app, $usuario, $pass) {
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

function cargarCarrera() {
	return ORM::for_table('carrera')->where_gt('fecha', date("Y-m-d"))->order_by_asc('fecha')->limit(1)->find_many();	
}

function cargarDatosCarrera($idCarrera) {
	return ORM::for_table('carrera')->where('id', $idCarrera)->find_one();
}

function cargarDatosCategoria($idCategoria) {
	return ORM::for_table('categoria')->where('id', $idCategoria)->find_one();
}

function cargarCarrerasReclamacion() {
    return ORM::for_table('carrera')->
    where_lt('fecha', calcularFecha('days', 4, date("Y-m-d")))->
    order_by_asc('fecha')->find_many();
}

function cargarReclamaciones($race) {
	return ORM::for_table('incidente')->
	join('reclamacion', array('incidente.id', '=', 'reclamacion.incidente_id'))->
	join('piloto_incidente', array('incidente.id', '=', 'piloto_incidente.incidente_id'))->
	join('piloto', array('piloto.id', '=', 'piloto_incidente.piloto_id'))->
	where('carrera_id', $race)->where('piloto_incidente.reclama', 1)->order_by_asc('vuelta')->
	select_many('reclamacion.incidente_id', 'vuelta', 'minuto', 'reclama', 'nombre_completo', 'titulo')->find_many();
}

function cargarReclamacion($idReclamacion) {
	return ORM::for_table('reclamacion')->
	//join('incidente', array('incidente.id', '=', 'reclamacion.incidente_id'))->
	//join('piloto_incidente', array('piloto_incidente.incidente_id', '=', 'incidente.id'))->
	where('reclamacion.incidente_id', $idReclamacion)->
	find_many();
}

function crearReclamacion($app, $titulo, $comentario, $vuelta, $minuto, $carrera, $reclama, $sereclama) {
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

	$piloto_incidente = ORM::for_table('piloto_incidente')->create();
	$piloto_incidente->piloto_id = $sereclama;
	$piloto_incidente->incidente_id = $nincidentes;
	$piloto_incidente->reclama = 0;
	$piloto_incidente->sancion = 0;

	$piloto_incidente->save();

	return $nincidentes;
}

function crearComentario($app, $titulo, $comentario, $id) {
	$comment = ORM::for_table('reclamacion')->create();
	$comment->titulo = $titulo;
	$comment->comentario = $comentario;
	$comment->incidente_id = $id;
	$comment->piloto_id = $_SESSION['id'];

	$comment->save();
}

function pilotosReclamados($idReclamacion) {
	return ORM::for_table('piloto')->
	join('piloto_incidente', array('piloto.id', '=', 'piloto_incidente.piloto_id'))->
	where('piloto_incidente.reclama', '0')->
	where('piloto_incidente.incidente_id', $idReclamacion)->
	select_many('piloto.nombre_completo')->
	find_many();
}

function calcularFecha($modo, $valor, $fecha_inicio = false) {
 
   if ($fecha_inicio != false) {
          $fecha_base = strtotime($fecha_inicio);
   } else {
          $time = time();
          $fecha_actual = date("Y-m-d",$time);
          $fecha_base = strtotime($fecha_actual);
   }
 
   $calculo = strtotime("$valor $modo","$fecha_base");
 
   return date("Y-m-d", $calculo);
}