<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/icon" href="../../img/favicon.ico">
	<link rel="stylesheet" href="../../src/styles/normalize.css">
	<link rel="stylesheet" href="../../src/styles/style.css">
  <link rel="stylesheet" href="../../src/styles/recovery.css">
  <title>Recuperar contraseña</title>
  <?php $dir = __DIR__;?>
</head>
<body>
  <span class="back">
    <ion-icon name="arrow-back-outline"></ion-icon>
  </span>

  <span class="help">
		<img src="../../img/help.svg" alt="help">
	</span>
  <div class="message"></div>

    <h1>Recuperar contraseña</h1>
    <main>
      <form action="" class="formulario first-form">
        <h2 class="ask">Hola</h2>
        <label for="response1">
          <input type="text" name="response1">
        </label>
        <h2 class="ask">Hola</h2>
        <label for="response2">
          <input type="text" name="response2">
        </label>
        <h2 class="ask">Hola</h2>
        <label for="response3">
          <input type="text" name="response3">
        </label>
        <input type="submit" name="enviar" value="Recuperar">
      </form>
      <form action="" class="formulario second-form" style="display:none;">
        <h2 style="margin-left: 0;font-size: 1.3em">Contraseña nueva: </h2>
        <label for="new-password">
          <div class="passwords">
            <input type="password" name="new-password" class="password">
            <span class="visible" style="right: -50px;">
            <ion-icon name="eye-outline"></ion-icn>
            </span>
            <span class="not-visible" style="display:none;right: -50px">
              <ion-icon name="eye-off-outline"></ion-icon>
            </span>
          </div>
        </label>
        <h2 style="margin-left: 0;font-size: 1.3em">Confirma tu nueva contraseña: </h2>
        <label for="new-password-confirm">
          <div class="passwords">
            <input type="password" name="new-password-confirm" class="password">
            <span class="visible" style="right: -50px;">
              <ion-icon name="eye-outline"></ion-icon>
            </span>
            <span class="not-visible" style="display:none;right: -50px;">
              <ion-icon name="eye-off-outline"></ion-icon>
            </span>
          </div>
        </label>
        <input type="submit" value="Actualizar" name="update-password">
      </form>
    </main>

  <?php require_once '../../src/html/footer.php'?>

  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
	<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

	<script src="../../src/js/mensaje.js"></script>
	<script src="../../src/js/all.js"></script>
  <script src="../../src/js/recovery.js"></script>
</body>
</html>