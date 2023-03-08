<link rel="stylesheet" href="src/styles/inside.css">

<div class="box">
  <div class="pages">
    <div class="info" style="color: #fff">
      <h2>Hola <b class="nombrePHP"></b>!</h2>
      <h3 class="menu">Menú:</h3>
        <ul style="margin: 0 40px auto;">
          <li class="menu-option" id="profile" style="border-radius:5px 5px 0 0;">Ver perfil</li>
          <li class="menu-option" id="chats">Tus chats</li>
          <li class="menu-option" id="buscar">Buscar personas</li>
          <li class="menu-option" id="change-password">Cambiar contraseña</li>
          <li class="menu-option" id="security-ask">Cambiar preguntas de seguridad</li>
          <li class="menu-option" id="remove-account" style="border-radius: 0 0 5px 5px;">Eliminar cuenta</li>
        </ul>
        <form action="./" method="post" class="back">
          <input type="submit" value="Cerrar sesión" name="volver" class="volver">
        </form>
    </div>
    <div class="chat-profile">
      <span class="profile-img"></span>
      <h2>Josafat</h2>
      <h4>Amigo</h4>
      <button>Eliminar de amigos</button>
    </div>
  </div>
  <div class="type">
    <div class="screen chat-screen" style="margin-left:-100%;padding:0;">
      <section>
      <span class="back-chat"><ion-icon name="arrow-back-sharp"></ion-icon></span>
        <span class="user">Josafat</span>
      </section>
      <div class="messages">
        <span class="msg emisor">
          <p>Estoy enviando un mensaje de prueba</p>
          <b>Hora: 12:45</b>
        </span>
        <span class="msg receptor">
          <p>Recibido</p>
          <b>Hora: 12:45</b>
        </span>
      </div>
      <input type="text">
      <span class="send"><ion-icon name="send-sharp"></ion-icon></span>
    </div>
    <div class="screen chats">
      <h2>Chats</h2>
      <input type="text" class="local_search">
      <div class="search">
        <div class="chat">
          <span></span>
          <div>
            <h2>Josafat</h2>
            <h3>Tu: Texto</h3>
          </div>
        </div>
        <div class="chat">
          <span></span>
          <div>
            <h2>Josafat</h2>
            <h3>Tu: Texto</h3>
          </div>
        </div>
        <div class="chat">
          <span></span>
          <div>
            <h2>Josafat</h2>
            <h3>Tu: Texto</h3>
          </div>
        </div>
        <div class="chat">
          <span></span>
          <div>
            <h2>Josafat</h2>
            <h3>Tu: Texto</h3>
          </div>
        </div>
        <div class="chat">
          <span></span>
          <div>
            <h2>Josafat</h2>
            <h3>Tu: Texto</h3>
          </div>
        </div>
        <div class="chat">
          <span></span>
          <div>
            <h2>Josafat</h2>
            <h3>Tu: Texto</h3>
          </div>
        </div>
      </div>
    </div>
    <div class="screen buscar">
      <h2>Buscar</h2>
      <input type="text" class="user_search">
      <div class="search">
        
      </div>
    </div>
    <div class="screen profile" style="padding: 20px 20px;">
      <h2>Tu perfil:</h2>
      <span class="profile-img"></span>
      <div class="chossen-img">
        <input type="file" accept="image/*">
      </div>
      <h1><b class="nombrePHP"></b></h1>
      <h3>Correo: &nbsp;&nbsp;&nbsp;<b class="emailPHP"></b></h3>
    </div>
    <div class="screen other-profile" style="padding: 20px 20px;">
      <span class="profile-img"></span>
      <h1>Josafat</h1>
      <button style="margin-top: 20px;" class="solicitud">Enviar solicitud</button>
    </div>
    <div class="screen security-ask">
      <h2 style="text-align:center;">Actualiza tus preguntas de seguridad</h2>
      <form class="form ask-form" method="post">
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
        <input type="submit" value="Actualizar" name="update-asks">
			</form>
    </div>
    <div class="screen change-password">
      <h2>Cambiar contraseña</h2>
      <form action="" method="post" class="form password-form">
        <h3>Contraseña antigua: </h3>
        <label for="old-password">
          <div class="passwords">
           <input type="password" name="old-password" class="password">
            <span class="visible">
							<ion-icon name="eye-outline"></ion-icon>
						</span>
						<span class="not-visible" style="display:none;">
							<ion-icon name="eye-off-outline"></ion-icon>
						</span>
          </div>
        </label>
        <h3>Contraseña nueva: </h3>
        <label for="new-password">
          <div class="passwords">
            <input type="password" name="new-password" class="password">
            <span class="visible">
							<ion-icon name="eye-outline"></ion-icon>
						</span>
						<span class="not-visible" style="display:none;">
							<ion-icon name="eye-off-outline"></ion-icon>
						</span>
          </div>
        </label>
        <h3>Confirma tu nueva contraseña: </h3>
        <label for="new-password-confirm">
          <div class="passwords">
            <input type="password" name="new-password-confirm" class="password">
            <span class="visible">
							<ion-icon name="eye-outline"></ion-icon>
						</span>
						<span class="not-visible" style="display:none;">
							<ion-icon name="eye-off-outline"></ion-icon>
						</span> 
          </div>
        </label>
        <input type="submit" value="Actualizar" name="update-password">
      </form>
    </div>
    <div class="screen remove-account">
      <h2>Eliminar cuenta</h2>
      <form action="" method="post" class="form remove-account-form">
        <h3>Escribe tu contraseña:</h3>
        <label for="password">
          <div class="passwords">
            <input type="password" name="password" class="password">
            <span class="visible">
							<ion-icon name="eye-outline"></ion-icon>
						</span>
						<span class="not-visible" style="display:none;">
							<ion-icon name="eye-off-outline"></ion-icon>
						</span>
          </div>
        </label>
        <h3>Confirma tu contraseña:</h3>
        <label for="confirm-password">
          <div class="passwords">
            <input type="password" name="confirm-password" class="password">
            <span class="visible">
							<ion-icon name="eye-outline"></ion-icon>
						</span>
						<span class="not-visible" style="display:none;">
							<ion-icon name="eye-off-outline"></ion-icon>
						</span>
          </div>
        </label>
        <input type="submit" value="Eliminar cuenta" name="remove">
      </form>
    </div>
  </div>
</div>

<script src="src/js/inside.js"></script>