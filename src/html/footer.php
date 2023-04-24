<?php
  $route = "";

  if (str_contains($dir,'mvc')){
    $route = '../../';
  }

?>

<footer class="footer">
  <span class="close">
    <img src="<?php echo $route?>img/close.svg" alt="close">
  </span>
  <a href="https://github.com/Josafast" target="_blank">
    <img src="<?php echo $route?>img/jfastSFX.svg" alt="Josafast logo">
  </a>
  <p>Todos los derechos reservados para "Ionic" y el uso de sus iconos "Ion-icons" &#169; <b class="year"></b></p>
  <br><br><br><br>
  <a href="https://github.com/Josafast/LoginAPIphp" target="_blank">
    <img src="<?php echo $route?>img/logo-github.svg" alt="logo github">
  </a>
</footer>