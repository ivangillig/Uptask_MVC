<div class="contenedor restablecer">
    <?php include_once __DIR__ . '/../templates/sitio.php' ?>
    
    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu nuevo password</p>
        
        <?php include_once __DIR__ . '/../templates/alertas.php' ?>

        <?php if($mostrar) { ?>

        <form  class="formulario" method="POST">
            

            <div class="campo">
                <label for="password">Password</label>
                <input 
                type="password"
                id="password"
                placeholder="Tu Password"
                name="password"                
                />
            </div>

            <input type="submit" class="boton" value="Guardar Password">
        </form>

        <?php } ?>

            <div class="acciones">
                <a href="/crear">Aún no tienes una cuenta? obtener una</a>
                <a href="/">Ya tienes cuenta? Iniciar Sesión</a>
            </div>
    </div> <!--.Contenedor-SM -->

</div>