<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/icon" href="../../img/favicon.ico">
	<link rel="stylesheet" href="../../src/styles/normalize.css">
	<link rel="stylesheet" href="../../src/styles/style.css">
  <style>
    body {
      background: #fff;
      flex-direction: column;
      justify-content: space-around;
      padding-bottom: 120px;
    }

    .help {
      filter: invert(63%) sepia(76%) saturate(5533%) hue-rotate(178deg) brightness(105%) contrast(102%);
    }

    .footer p {
      color: var(--clr);
    }

    .footer img {
      filter: invert(42%) sepia(59%) saturate(4864%) hue-rotate(5deg) brightness(107%) contrast(104%);
    }

    .formulario {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .formulario h2 {
      align-self: start;
      margin: 10px 0 10px -40px;
    }

    .formulario input[type="text"], input[type="password"]{
      position: relative;
      padding: 10px 13px;
      border: 2px solid #bfbfbf;
      color: gray;
      margin: 7px 0;
      outline: none;
    }

    .formulario input[type="submit"]{
      margin-top: 20px;
      color: #fff;
      outline: none;
      border: none;
      width: 120px;
      height: 41px;
      background-color: var(--clr);
      cursor: pointer;
    }

    .message {
      background-color: var(--clr);
      color: #fff;
    }

    .back {
      font-size: 1.6em;
      cursor: pointer;
      position: absolute;
      top: 20px;
      left: 20px;
      color: var(--clr);
    }
  </style>
  <title>Recuperar contraseña</title>
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
        <h2>Contraseña nueva: </h2>
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
        <h2>Confirma tu nueva contraseña: </h2>
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

  <footer class="footer">
    <span class="close">
      <img src="../../img/close.svg" alt="close">
    </span>
    <a href="https://github.com/Josafast" target="_blank">
      <img src="../../img/jfastSFX.svg" alt="Josafast logo">
    </a>
    <p>Todos los derechos reservados para "Ionic" y el uso de sus iconos "Ion-icons" &#169; <b class="year"></b></p>
    <br><br><br><br>
    <a href="https://github.com/Josafast?tab=repositories" target="_blank">
      <img src="../../img/logo-github.svg" alt="logo github">
    </a>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
	<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

	<script src="../../src/js/mensaje.js"></script>
	<script src="../../src/js/all.js"></script>

  <script>
    window.addEventListener('load',()=>{
      document.querySelector('.back').addEventListener('click',()=>{
        history.back();
        location.reload();
      });

      if (history.state != null){
        let forme = new FormData();
        forme.append('recuperar',history.state.email);
        fetch("../controllers/login_controller.php",{
          method: "POST",
          body: forme
        }).then(res=>res.json()).then(res=>{
          let datos = JSON.parse(res.mensaje);
          let i=0;
          for (let arr in datos){
            document.querySelectorAll('.ask')[i].textContent = arr;
            i++;
          }
        });
      } else {
        history.back();
      }

      document.querySelectorAll('.formulario').forEach(formu=>{
        formu.addEventListener('submit',(e)=>{
          e.preventDefault();
          let forme = new FormData(e.target);
          forme.append("consultar-recuperacion",history.state.email);

          let message = e.target.classList.contains('first-form') ? (
            forme.get('response1') == "" || forme.get('response2') == "" || forme.get('response3') == "" ? "Debes rellenar todos los campos" : ""
          ) : (
            forme.get("new-password") == "" ?
              "Debes rellenar los campos de contraseña" :
            forme.get("new-password-confirm") == "" ? 
              "Debes confirmar la contraseña" :
            forme.get("new-password") != forme.get("new-password-confirm") ?
              "Las contraseñas no coinciden entre si" : "" 
          );

          if (message) return mensaje("no","Debe rellenar todos los campos");

          fetch(`../controllers/${e.target.classList.contains("first-form") ? "login_controller.php" : "loged_controller.php"}`,{method:"POST",body:forme})
            .then(res=>res.json())
            .then(res=>{
              if (res.mode == "ok-reccovery"){
                document.querySelector('.second-form').style.display = "flex";
                document.querySelector('.first-form').style.display = "none";
              } else if (res.mode == "ok-changed") {
                history.replaceState({mode:"edit",mensaje:res.mensaje},'','index.php');
                location.reload();
              } else {
                return mensaje(res.mode,res.mensaje);
              } 
          });
        });  
      });
    });
  </script>
</body>
</html>