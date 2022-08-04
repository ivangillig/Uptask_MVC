<?php include_once __DIR__ . '/header-dashboard.php' ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php' ?>

    <a href="/perfil" class="enlace">Volver a mi perfil</a>

    <form action="/cambiar-password" method="POST" class="formulario">
        <div class="campo">
            <label for="nombre">Password Actual</label>
            <input 
                type="password"
                name="password_actual"
                placeholder="Ingresa tu password"
            />
        </div>
        <div class="campo">
            <label for="email">Nuevo Password</label>
            <input 
                type="password"
                name="password_nuevo"
                placeholder="Nuevo Password"
            />
        </div>

        <input type="submit" value="Guardar Cambios">
    </form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php' ?>