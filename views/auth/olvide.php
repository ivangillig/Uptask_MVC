<div class="contenedor olvide">
<?php include_once __DIR__ . '/../templates/sitio.php' ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recupera tu acceso UpTask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php' ?>

        <form action="/olvide" class="formulario" method="POST" novalidate>
            

            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="email"
                    id="email"
                    placeholder="Tu Email"
                    name="email"                
                />
            </div>


            <input type="submit" class="boton" value="Reestablecer Contraseña" >
        </form>
            <div class="acciones">
                <a href="/">Ya tienes cuenta? Iniciar Sesión</a>
                <a href="/crear">Aún no tienes una cuenta? obtener una</a>
            </div>
    </div> <!--.Contenedor-SM -->

</div>