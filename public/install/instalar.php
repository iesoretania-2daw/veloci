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

	$todocorrecto = false; // Variable que nos permitira continuar la instalación o no.

	//Busca si existe un fichero de configuración y si existe, detiene todo proceso de instalación.

	$ruta = $_SERVER["SERVER_NAME"];
	$subruta = $_SERVER["PHP_SELF"];

	$fin = strripos($subruta, '/');
	$subruta = substr($subruta, 0, $fin);
	$fin = strripos($subruta, '/');
	$subruta = substr($subruta, 0, $fin) . "/config/config.php";
	$ruta = $_SERVER["DOCUMENT_ROOT"] . $subruta;

	// Busca si existe un fichero de configuración y si existe, detiene todo proceso de instalación.
	
	// Ejecución tras presionar el botón instalar.
	if (isset($_POST['instalar'])) {
		$host = $_POST['inputHost'];
		$database = $_POST['inputBaseDatos'];
		$usuario = $_POST['inputUsuarioBase'];
		$password = $_POST['inputPassUsuarioBase'];
		$nombreApp = $_POST['inputNombreApp'];
		$usrADM = $_POST['inputUsuarioAdmin'];
		$passADM = $_POST['inputPassAdmin'];
		$passTestADM = $_POST['inputPassAdmin2'];
		$nombreADM = $_POST['inputNombrePublico'];
		$emailADM = $_POST['inputEmailAdmin'];

		$correcto = false;

		// Comprobación de que el usuario ha metido la misma contraseña en los 2 campos.
		if ($passADM != $passTestADM) {
			die (print_r("ERROR: La contraseña del administrador no coincide"));
		}

		// Encriptación de la contraseña
		$passADM = password_hash($passADM, PASSWORD_DEFAULT);

		// Comienzo de la instalación

		try {
			// Conexión, comienzo de transacción, creación de BBDD y entrada a la BBDD.
			$dbh = new PDO('mysql:host=' . $host . ';charset=utf8', $usuario, $password);
			$dbh->beginTransaction();
			$dbh->exec("CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;") or die(print_r("Error al crear la base de datos, 
				es posible que exista otra base de datos con ese nombre."));
			$dbh->exec("USE `$database`");

			// Creación de TABLAS:

			/* Creación de tabla Configuración */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `configuracion` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`nombre` VARCHAR(45) NOT NULL,
				`descripcion` VARCHAR(100) NOT NULL,
				`requiere_activacion` INT UNSIGNED NOT NULL,
				`requiere_normativa` INT UNSIGNED NOT NULL,
				`requiere_pago` INT UNSIGNED NOT NULL,
				`teamspeak` TEXT NOT NULL,
				`twitter` TEXT NOT NULL,
				`facebook` TEXT NOT NULL,
				`youtube` TEXT NOT NULL,
				`vimeo` TEXT NOT NULL,
				`permite_plantillas` INT UNSIGNED NOT NULL,
				`plantilla_defecto` VARCHAR(60) NOT NULL DEFAULT 'bootstrap.css',
				PRIMARY KEY (`id`))
			ENGINE = InnoDB;");

			/* Creación de tabla Notificación */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `notificacion` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`enlace` VARCHAR(70) NOT NULL,
				`nota` TEXT NOT NULL,
				`usuario` VARCHAR(60) NOT NULL,
				`fecha` DATETIME NOT NULL,
				`rango_requerido` INT UNSIGNED NOT NULL,
				`objetivo` INT UNSIGNED NOT NULL,
				`tipo` INT UNSIGNED NOT NULL,
				`leida` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`))
			ENGINE = InnoDB;");

			/* Creación de tabla piloto - usuario */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `piloto` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`nombre` VARCHAR(45) NOT NULL,
				`password` VARCHAR(255) NOT NULL,
				`avatar` VARCHAR(45) NOT NULL,
				`email` VARCHAR(50) NOT NULL,
				`token` VARCHAR(100) NOT NULL,
				`escuderia` VARCHAR(45) NOT NULL,
				`nombre_completo` VARCHAR(60) NOT NULL,
				`activo` INT UNSIGNED NOT NULL,
				`rol` INT UNSIGNED NOT NULL,
				`privacidad_perfil` INT UNSIGNED NOT NULL,
				`expulsado` INT UNSIGNED NOT NULL,
				`puede_reclamarse` INT UNSIGNED NOT NULL DEFAULT '1',
				PRIMARY KEY (`id`),
				UNIQUE INDEX `name_UNIQUE` (`nombre` ASC))
			ENGINE = InnoDB;");

			/* Creación de tabla categoría */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `categoria` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`nombre` varchar(45) NOT NULL,
				`pais` varchar(45) NOT NULL,
				`distancia` int(10) unsigned NOT NULL,
				`imagen` varchar(50) NOT NULL,
				`plazas` int(10) unsigned NOT NULL,
				`precio_inscripcion` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id`))
			ENGINE = InnoDB;");

			/* Creación de tabla circuito */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `circuito` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`nombre` VARCHAR(45) NOT NULL,
				`pais` VARCHAR(45) NOT NULL,
				`distancia` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`))
			ENGINE = InnoDB;");

			/* Creación de tabla carrera */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `carrera` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`nombre` VARCHAR(45) NOT NULL,
				`neumatico1` ENUM('Super blandos', 'Blandos', 'Medios', 'Duros', 'Normal') NOT NULL DEFAULT 'Normal',
				`neumatico2` ENUM('Super blandos', 'Blandos', 'Medios', 'Duros', 'Normal') NOT NULL DEFAULT 'Normal',
				`vueltas` VARCHAR(45) NOT NULL,
				`fecha` DATE NOT NULL,
				`hora` TIME NOT NULL,
				`fecha_limite` DATE NOT NULL,
				`hora_limite` TIME NOT NULL,
				`categoria_id` INT UNSIGNED NULL,
				`circuito_id` INT UNSIGNED NULL,
				PRIMARY KEY (`id`),
				INDEX `fk_carrera_categoria1_idx` (`categoria_id` ASC),
				INDEX `fk_carrera_circuito1_idx` (`circuito_id` ASC),
				CONSTRAINT `fk_carrera_categoria1`
				FOREIGN KEY (`categoria_id`)
				REFERENCES `$database`.`categoria` (`id`)
				ON DELETE SET NULL
				ON UPDATE CASCADE,
				CONSTRAINT `fk_carrera_circuito1`
				FOREIGN KEY (`circuito_id`)
				REFERENCES `$database`.`circuito` (`id`)
				ON DELETE SET NULL
				ON UPDATE CASCADE)
			ENGINE = InnoDB;");

			/* Creación de tabla incidente */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `incidente` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`vuelta` INT NOT NULL,
				`minuto` TIME NOT NULL,
				`abierto` INT UNSIGNED NOT NULL,
				`carrera_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `fk_incidente_carrera1_idx` (`carrera_id` ASC),
				CONSTRAINT `fk_incidente_carrera1`
				FOREIGN KEY (`carrera_id`)
				REFERENCES `$database`.`carrera` (`id`)
				ON DELETE CASCADE
				ON UPDATE NO ACTION)
			ENGINE = InnoDB;");

			/* Creación de tabla piloto - categoría */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `piloto_categoria` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`piloto_id` INT UNSIGNED NOT NULL,
				`categoria_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `fk_piloto_categoria_piloto1_idx` (`piloto_id` ASC),
				INDEX `fk_piloto_categoria_categoria1_idx` (`categoria_id` ASC),
				CONSTRAINT `fk_piloto_categoria_piloto1`
				FOREIGN KEY (`piloto_id`)
				REFERENCES `$database`.`piloto` (`id`)
				ON DELETE CASCADE
				ON UPDATE NO ACTION,
				CONSTRAINT `fk_piloto_categoria_categoria1`
				FOREIGN KEY (`categoria_id`)
				REFERENCES `$database`.`categoria` (`id`)
				ON DELETE CASCADE
				ON UPDATE NO ACTION)
			ENGINE = InnoDB;");

			/* Creación de tabla reclamación */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `reclamacion` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`titulo` VARCHAR(45) NOT NULL,
				`comentario` TEXT NOT NULL,
				`incidente_id` INT UNSIGNED NOT NULL,
				`piloto_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `fk_reclamacion_piloto1_idx` (`piloto_id` ASC),
				INDEX `fk_reclamacion_incidente1_idx` (`incidente_id` ASC),
				CONSTRAINT `fk_reclamacion_incidente1`
				FOREIGN KEY (`incidente_id`)
				REFERENCES `$database`.`incidente` (`id`)
				ON DELETE CASCADE
				ON UPDATE NO ACTION,
				CONSTRAINT `fk_reclamacion_piloto1`
				FOREIGN KEY (`piloto_id`)
				REFERENCES `$database`.`piloto` (`id`)
				ON DELETE CASCADE
				ON UPDATE NO ACTION)
			ENGINE = InnoDB;");

			/* Creación de tabla recurso */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `recurso` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`descripcion` VARCHAR(45) NOT NULL,
				`imagen` VARCHAR(60) NOT NULL,
				`reclamacion_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `fk_recurso_reclamacion1_idx` (`reclamacion_id` ASC),
				CONSTRAINT `fk_recurso_reclamacion1`
				FOREIGN KEY (`reclamacion_id`)
				REFERENCES `$database`.`reclamacion` (`id`)
				ON DELETE CASCADE
				ON UPDATE NO ACTION)
			ENGINE = InnoDB;");

			/* Creación de tabla piloto - carrera */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `piloto_carrera` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`piloto_id` INT UNSIGNED NOT NULL,
				`carrera_id` INT UNSIGNED NOT NULL,
				`estado` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `fk_piloto_carrera_carrera1_idx` (`carrera_id` ASC),
				INDEX `fk_piloto_carrera_piloto1_idx` (`piloto_id` ASC),
				CONSTRAINT `fk_piloto_carrera_carrera1`
				FOREIGN KEY (`carrera_id`)
				REFERENCES `$database`.`carrera` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE,
				CONSTRAINT `fk_piloto_carrera_piloto1`
				FOREIGN KEY (`piloto_id`)
				REFERENCES `$database`.`piloto` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE)
			ENGINE = InnoDB;");

			/* Creación de tabla piloto - incidente */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `piloto_incidente` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`piloto_id` INT UNSIGNED NOT NULL,
				`incidente_id` INT UNSIGNED NOT NULL,
				`reclama` INT UNSIGNED NOT NULL,
				`sancion` INT UNSIGNED NOT NULL,
				`nota` TEXT NULL,
				PRIMARY KEY (`id`),
				INDEX `fk_piloto_incidente_incidente1_idx` (`incidente_id` ASC),
				INDEX `fk_piloto_incidente_piloto1_idx` (`piloto_id` ASC),
				CONSTRAINT `fk_piloto_incidente_incidente1`
				FOREIGN KEY (`incidente_id`)
				REFERENCES `$database`.`incidente` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE,
				CONSTRAINT `fk_piloto_incidente_piloto1`
				FOREIGN KEY (`piloto_id`)
				REFERENCES `$database`.`piloto` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE)
			ENGINE = InnoDB;");

			/* Creación de tabla Noticias */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `noticia` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`titulo` VARCHAR(45) NOT NULL,
				`texto` TEXT NOT NULL,
				`fecha_publicacion` DATETIME NOT NULL,
				`rango_requerido` INT UNSIGNED NOT NULL,
				`n_comentarios` INT UNSIGNED NOT NULL,
				`estado` INT UNSIGNED NOT NULL,
				`piloto_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `fk_noticia_piloto1_idx` (`piloto_id` ASC),
				CONSTRAINT `fk_noticia_piloto1`
				FOREIGN KEY (`piloto_id`)
				REFERENCES `$database`.`piloto` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE)
			ENGINE = InnoDB;");

			/* Creación de tabla Comentario */
			$dbh->exec("CREATE TABLE IF NOT EXISTS `comentario` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`comentario` TEXT NOT NULL,
				`responde_a` INT UNSIGNED NOT NULL,
				`fecha` DATETIME NOT NULL,
				`piloto_id` INT UNSIGNED NOT NULL,
				`noticia_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `fk_comentario_piloto1_idx` (`piloto_id` ASC),
				INDEX `fk_comentario_noticia1_idx` (`noticia_id` ASC),
				CONSTRAINT `fk_comentario_piloto1`
				FOREIGN KEY (`piloto_id`)
				REFERENCES `$database`.`piloto` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE,
				CONSTRAINT `fk_comentario_noticia1`
				FOREIGN KEY (`noticia_id`)
				REFERENCES `$database`.`noticia` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE)
			ENGINE = InnoDB;");

			$correcto = true; // Si todo ha ido correcto permitiremos la ejecución.

		} catch (Exception $e) {
			// En casso de error, no hacemos ningún cambio, y detenemos la ejecución.
			//$dbh->rollBack();
			$correcto = false;
			die ($e->getMessage());
		}

		// Sí todo es correcto creamos el usuario de administración.
		if ($correcto) {
			try {
				$token = generarToken(100);

				$insertADM = $dbh->prepare("INSERT INTO `piloto` (`id`, `nombre`, `password`, `avatar`, `email`, `token`, `escuderia`, `nombre_completo`, `activo`, `rol`) VALUES (null,:usrADM,:passADM,'/images/default.png',:emailADM, :token, 'Ninguna', :nombreADM, '1', '5');");
				$insertADM->bindValue(':usrADM', strtolower($usrADM), PDO::PARAM_STR);
				$insertADM->bindValue(':passADM', $passADM, PDO::PARAM_STR);
				$insertADM->bindValue(':emailADM', $emailADM, PDO::PARAM_STR);
				$insertADM->bindValue(':token', $token, PDO::PARAM_STR);
				$insertADM->bindValue(':nombreADM', $nombreADM, PDO::PARAM_STR);
				$insertADM->execute() or die(print_r("Error en la creación del usuario administrador"));

				$configApp = $dbh->prepare("INSERT INTO `configuracion` (`id`, `nombre`, `descripcion`, `requiere_activacion`, `requiere_normativa`, `requiere_pago`, `teamspeak`, `twitter`, `facebook`, `youtube`, `vimeo`, `permite_plantillas`, `plantilla_defecto`) VALUES (null, :nombreApp, 'Aplicación web para la gestión de carreras', 0, 0, 0, '', '', '', '', '', 0, 'bootstrap.css');");
				$configApp->bindValue(':nombreApp', $nombreApp, PDO::PARAM_STR);
				$configApp->execute() or die(print_r("Error en la creación de la tabla configuración"));

				$todocorrecto = true;
			} catch (PDOException $e) {
				// Si falla algo, desechamos los cambios y paramos la ejecución.
				$dbh->rollBack();
				$todocorrecto = false;
				die ($e->getMessage());
			}

			// Sí todo es correcto vamos a escribir el fichero de configuración en config/config.php
			if ($todocorrecto) {
				$ruta = $_SERVER["DOCUMENT_ROOT"] . $_SERVER["PHP_SELF"];
				$ruta = dirname($ruta);

				$ruta = moveDir($ruta, 2, '/config/config.php');

				$fp = fopen($ruta, "w+") or die("error");
			    $cadena = "<?php ORM::configure('mysql:host=$host;dbname=$database'); ORM::configure('username', '$usuario'); ORM::configure('password', '$password'); ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));";
			    
			    fputs($fp,$cadena);
			    fclose($fp);
			}
		}
	}

function moveDir($ruta, $subir, $directorio = "") {
	$i = 0;
	while ($i < $subir) {
		$fin = strripos($ruta, '/');
		$ruta = substr($ruta, 0, $fin);
		$i++;
	}

	$ruta .= $directorio;

	return $ruta;
}

function generarToken($longitud) {
    $cadena = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $token = "";

    while($longitud > 0) {
        $num = mt_rand(1,62);
        $token .= $cadena[$num-1];
        $longitud--;
    }

    return $token;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Instalador</title>
	<meta charset="utf-8"/>
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="css/style.css" rel="stylesheet" type="text/css">
	<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700|Open+Sans:300italic,400,300,700" rel="stylesheet" type="text/css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/myscript.js"></script>
</head>
<body style="margin-top: 50px;">
	<div class="container">
	<div class="panel panel-default">
		<div class="panel-body">
			<?php 
			if ($todocorrecto) {
				echo "<h3 class='header'>Fin de la instalación</h3>";
				echo "<h2>¡Instalación completada con éxito!</h2>";
				echo "Ahora ya puede acceder a su web y panel de administración: " . $usrADM . "<br/>";
				echo "<a href='http://" . $_SERVER["SERVER_NAME"] . "' class='ui animated positive button floated left'>
				<div class='visible content'>Ir al sitio web</div>
				<div class='hidden content'>
				<i class='right arrow icon'></i>
				</div>
				</a>";
				echo "<a href='http://" . $_SERVER["SERVER_NAME"] . "/admin' class='ui animated positive button floated left'>
				<div class='visible content'>Ir al panel de administración</div>
				<div class='hidden content'>
				<i class='right arrow icon'></i>
				</div>
				</a>";

				if (!file_exists($ruta)) {
					echo "<br/><br/>Parece que hubo un problema en la creación del fichero de configuración, para que la aplicación pueda funcionar
					correctamente debe copiar el código a continuación en un fichero, guardarlo como 'config.php' y despúes copiarlo en la carpeta config
					de su servidor donde instalo la aplicación.<br/><br/>";
					echo "<textarea rows='2' class='form-control'>" . htmlspecialchars($cadena) . "</textarea>";
				}
			} else
				echo "Error, no se han recibido datos.";
			?>
		</div>
	</div>
	</div>
</body>
</html>