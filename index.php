<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie-edge">
	<link rel="shortcut icon" type="image/icon" href="img/favicon.ico">
	<link rel="stylesheet" href="src/styles/normalize.css">
	<link rel="stylesheet" href="src/styles/style.css">
	<title>Sign up or Log in</title>
	<?php session_start(); ?>
</head>
<body class="body">

	<span class="help">
		<img src="img/help.svg" alt="help">
	</span>
	<div class="message"></div>
	<?php if(!isset($_SESSION['user'])):?>

	<div class="box">
		<div class="log">
			<h1>¿Ya tienes una cuenta?</h1>
			<button>Ingresa</button>
		</div>
		<div class="sign">
			<h1>¿No tienes una cuenta?</h1>
			<button>Regístrate</button>
		</div>
		<div class="type">
			<form class="loger formulario" id="loger">
				<h2>Ingresa</h2>
				<label for="login-email">
					<input type="email" placeholder="Correo electrónico" id="login-email" name="login-email">
				</label>
				<div class="passwords">
					<label for="login-password">
						<input type="password" placeholder="Contraseña" class="password" id="login-password" name="login-password">
						<span class="visible">
							<ion-icon name="eye-outline"></ion-icon>
						</span>
						<span class="not-visible" style="display:none;">
							<ion-icon name="eye-off-outline"></ion-icon>
						</span>
					</label>
				</div>
				<button id="login" type="submit">Ingresar</button>
				<a href="#" class="recuperar">¿Olvidaste tu contraseña?</a>
			</form>
			<form class="signer formulario" id="signer">
				<h2>Regístrate</h2>
				<label for="sign-user">
					<input type="text" placeholder="Nombre de usuario" id="sign-user" name="sign-user">
				</label>
				<label for="sign-email">
					<input type="email" placeholder="Correo electrónico" id="sign-email" name="sign-email">
				</label>
				<label for="sign-password">
					<div class="passwords">
						<input type="password" placeholder="Contraseña" class="password" id="sign-password" name="sign-password">
						<span class="visible">
							<ion-icon name="eye-outline"></ion-icon>
						</span>
						<span class="not-visible" style="display:none;">
							<ion-icon name="eye-off-outline"></ion-icon>
						</span>
					</div>
				</label>
				<label for="sign-password-confirm">
					<div class="passwords">
						<input type="password" placeholder="Confirmar contraseña" class="password" id="sign-password-confirm" name="sign-password-confirm">
						<span class="visible">
							<ion-icon name="eye-outline"></ion-icon>
						</span>
						<span class="not-visible" style="display:none;">
							<ion-icon name="eye-off-outline"></ion-icon>
						</span>
					</div>
				</label>
				<button id="signup">Registrarse</button>
			</form>
			<form class="pregunta formulario" id="asks">
				<span class="back-register"><ion-icon name="arrow-back-sharp"></ion-icon></span>
				<h2 style="margin-left: 20px">Define tus preguntas de seguridad</h2>
				<label for="ask1"></label>
        <select name="ask1" class="preguntas ask1">
          <option value="0" class="preguntas_value">Sin elegir</option>
					<option value="1" class="preguntas_value">Animal favorito</option>
          <option value="2" class="preguntas_value">País soñado</option>
          <option value="3" class="preguntas_value">Color favorito</option>
          <option value="4" class="preguntas_value">Asignatura favorita</option>
          <option value="5" class="preguntas_value">Comida favorita</option>
        </select>
        <label for="response1">
          <input type="text" name="response1" placeholder="Pregunta 1">
        </label>
				<label for="ask2"></label>
        <select name="ask2" class="preguntas ask2">
					<option value="0" class="preguntas_value">Sin elegir</option>
          <option value="1" class="preguntas_value">Animal favorito</option>
          <option value="2" class="preguntas_value">País soñado</option>
          <option value="3" class="preguntas_value">Color favorito</option>
          <option value="4" class="preguntas_value">Asignatura favorita</option>
          <option value="5" class="preguntas_value">Comida favorita</option>
        </select>
        <label for="response2">
          <input type="text" name="response2" placeholder="Pregunta 2">
        </label>
				<label for="ask3"></label>
        <select name="ask3" class="preguntas ask3">
					<option value="0" class="preguntas_value">Sin elegir</option>
          <option value="1" class="preguntas_value">Animal favorito</option>
          <option value="2" class="preguntas_value">País soñado</option>
          <option value="3" class="preguntas_value">Color favorito</option>
          <option value="4" class="preguntas_value">Asignatura favorita</option>
          <option value="5" class="preguntas_value">Comida favorita</option>
        </select>
        <label for="response3">
          <input type="text" name="response3" placeholder="Pregunta 3">
        </label>
        <input type="submit" value="Registrarse" name="register-ask" style="cursor: pointer;">
			</form>
		</div>
	</div>
	<script src="src/js/main.js"></script>
	<?php else: require_once('mvc/views/inside.php'); endif;?>
	<?php require_once('mvc/views/footer.html');?>

	<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
	<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

	<script src="src/js/mensaje.js"></script>
	<script src="src/js/all.js"></script>

</body>
</html>