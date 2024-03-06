    <?php


        //IMPORTAR LA CONEXION
        require '../includes/config/database.php';
        $db = conectarDB();


        //ESCRIBIR EL QUERY
        $query = "SELECT * FROM propiedades";


        //CONSULTAR LA BASE DE DATOS

        $resultadoConsulta = mysqli_query($db, $query);


        // Muestra mensaje condicional
        $resultado = isset($_GET['resultado']) ? $_GET['resultado'] : null;

        if($_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);

            var_dump($id);
        }


        //incluye un template
        require '../includes/funciones.php';
        incluirTemplate('header');
    ?>

    <main class="contenedor seccion">
        <h1>Administrador de Bienes Raices</h1>
        <?php if(intval($resultado) === 1): ?>
        <p class="alerta exito">Anuncio Creado Correctamente</p>
        <?php elseif( intval($resultado) === 2 ): ?>
        <p class="alerta exito">Anuncio Actualizado Correctamente</p>
        <?php endif; ?>

        <a href="http://localhost:8080/bienesraices/admin/propiedades/crear.php" class="boton boton-verde">Nueva
            Propiedad</a>
        <table class="propiedades">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Titulo</th>
                    <th>Imagen</th>
                    <th>precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <!-- MOSTRAR RESULTADOS -->
            <tbody>
                <?php while( $propiedad = mysqli_fetch_assoc($resultadoConsulta)): ?>
                <tr>
                    <td> <?php echo $propiedad['id']; ?> </td>
                    <td> <?php echo $propiedad['titulo']; ?> </td>
                    <td> <img src="http://localhost:8080/bienesraices/imagenes/<?php echo $propiedad['imagen']; ?>.jpg"
                            class="imagen-tabla"></td>
                    <td>$ <?php echo $propiedad['precio']; ?> </td>
                    <td>
                        <form method="POST" class="w100">

                        <input type="hidden" name="id" value="<?php echo $propiedad['id']; ?>">

                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>

                        <a href="http://localhost:8080/bienesraices/admin/propiedades/actualizar.php?id=<?php echo $propiedad['id']; ?>"
                            class="boton-amarillo-block">Actualizar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <?php


        // CERRAR LA CONEXION 

        mysqli_close($db);

        incluirTemplate('footer');
    ?>