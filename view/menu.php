<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
  $( function() {
    $( "#tabs" ).tabs();
  } );
</script>
 <div id="tabs">
  <ul>
    <li><a href="#tabs-1">Añadir registro</a></li>
    <li><a href="#tabs-2">Eliminar registro</a></li>
    <li><a href="#tabs-3">Reemplazar</a></li>
  </ul>
  <div id="tabs-1">
    <form method="post" action="" onsubmit="window.location.reload()">
        <fieldset>
            <h1><legend>Crea un nuevo reemplazo</legend></h1>
            <label for="og_post" accesskey="s">Mensaje a editar: <input type='text' id='og_post' name='og_post' /></label><br>
            <label for="replace_post" accesskey="s">Mensaje editado: <input type='text' id='replace_post' name='replace_post' /></label><br>
            <input id='insert' type='submit' name='insert' value='Insertar'/>
        </fieldset>
    </form>
  </div>
  <div id="tabs-2">
    <form method="post" action="" onsubmit="location.refresh(true);">
          <fieldset>
              <h1><legend>Elimina un reemplazo existente</legend></h1>
              <?php $wordinfo = replaceIt_GetList();?>
              <select id="options" name="options" onchange="update(this, 'tf')">
                <option value="">Selecciona una opcion...</option>
                <?php foreach($wordinfo as $k => $v): ?>
                <option value="<?php echo $v?>" id="opt<?php echo $k?>"><?php echo $v?></option>
                <?php endforeach; ?>
                </select>
              <input id='delete' type='submit' name='delete' value='Eliminar' onclick="window.location.reload()"/>
          </fieldset>
      </form>
  </div>
  <div id="tabs-3">
    <h1>Busca y reemplaza todos los registros</h1>
    <form method="post" action="">
      <fieldset>
        <input id='replace' type='submit' name='replace' value='Reemplazar' />
      </fieldset>
    </form>
  </div>
</div>
