<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

//BASE DE DATOS

require '../../includes/config/database.php';
$db = conectarDB();

//Consultar para optener los vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);


// Arreglo con mensajes de errores
$errores = [];  
$titulo = '';
$precio = '';
$descripcion = '';
$habitaciones = '';
$wc = '';
$estacionamiento = '';
$vendedoresId = '';


// ejecutar el codigo despes que el usuario envie el formulario
if($_SERVER['REQUEST_METHOD']  === 'POST') {

    //echo"<pre>";
    //var_dump($_POST);
    //echo"</pre>";

    //echo"<pre>";
    //var_dump($_FILES);
    //echo"</pre>";

    

    $titulo =  mysqli_real_escape_string( $db, $_POST['titulo'] );
    $precio = mysqli_real_escape_string( $db, $_POST['precio'] );
    $descripcion = mysqli_real_escape_string( $db, $_POST['descripcion'] );
    $habitaciones = mysqli_real_escape_string( $db, $_POST['habitaciones'] );
    $wc = mysqli_real_escape_string( $db, $_POST['wc'] );
    $estacionamiento = mysqli_real_escape_string( $db, $_POST['estacionamiento'] );
    $vendedoresId = mysqli_real_escape_string( $db, $_POST['vendedor'] );
    $creado = date('Y/m/d');

    //ASIGNAR FILES HACIA UNA VARIABLE

    $imagen = $_FILES['imagen'];


    if(!$titulo) {
        $errores[] = "Debes añadir un titulo";
    }

    if(!$precio) {
        $errores[] = "Debes añadir el valor de la propiedad";
    }

    if(!$descripcion) {
        $errores[] = "Debes añadir una descripcion de la propiedad ";
    }
    if(!$habitaciones) {
        $errores[] = "Debes añadir el numero de habitaciones";
    }
    if(!$wc) {
        $errores[] = "Debes añadir el numero de baños";
    }
    if(!$estacionamiento) {
        $errores[] = "Debes añadir la cantidad de estacionamientos";
    }
    if(!$vendedoresId) {
        $errores[] = "Debes añadir el vendedor";
    }

    if(!$imagen['name']) {
        $errores[] = "La imagen es obligatoria";
    }


    // CALIRDADOR POR TAMAÑO (100 KB MAXIMO)
    
    $medida = 1000* 2000;

    if($imagen['size'] > $medida) {
        $errores [] = "La imagen es muy pesada";
    }


    //echo "<pre>";
    //var_dump($errores);
    //echo "<pre>";

    // Revisar que el array de errores este vacio

    if(empty($errores)) {

        //SUBIDA DE ARCHIVOS

        //CREAR CARPETA
        $carpetaImagenes = '../../imagenes/';

        if(!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        //Generar Nombre unico para imagenes

        $nombreImagen = md5( uniqid(rand(), true ) );

        //SUBIR LA IMAGEN
        var_dump($imagen['tmp_name']);
        var_dump($carpetaImagenes . $nombreImagen . ".jpg");
        if (!move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen . ".jpg")) {
            $errores[] = "Hubo un error al subir el archivo";
        } else {
            $errores[] = "Archivo subido con éxito";
        }
        
        
        //echo "<pre>";
        //var_dump($errores);
        //echo "<pre>";


        // Insertar en la Base de datos
        $query = "INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedores_id) VALUES ('$titulo', '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedoresId')";


        //echo $query;

        $resultado = mysqli_query($db, $query);

        if($resultado) {
            //Redireccionar al usuario

            header('Location: http://localhost:8080/bienesraices/admin/index.php?resultado=1');

        }

    }  else {
        // Mostrar error si la inserción falla
        echo "Error al insertar: " . mysqli_error($db);
    }
}


    require '../../includes/funciones.php';
  incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Crear Propiedad</h1>

    <?php foreach($errores as $error):  ?>
    <div class="alerta error">
        <?php echo $error; ?>
    </div>

    <?php endforeach; ?>


    <form class="formulario" method="POST" action="http://localhost:8080/bienesraices/admin/propiedades/crear.php" enctype="multipart/form-data">
        <fieldset>
            <legend>Informacion General</legend>

            <label for="titulo">Titulo</label>
            <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" placeholder="Titulo Propiedad"
                value="<?php echo $precio; ?>">

            <label for="imagen">Imagen</label>
            <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

            <label for="descripcion">Descripcion</label>
            <textarea id="descripcion" name="descripcion"><?php echo $descripcion; ?></textarea>

        </fieldset>

        <fieldset>
            <legend>Informacion Propiedad</legend>
            <label for="habitaciones">Habitaciones</label>
            <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="20"
                value="<?php echo $habitaciones; ?>">

            <label for="wc">Baños</label>
            <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="20" value="<?php echo $wc; ?>">

            <label for="estacionamiento">Estacionamiento</label>
            <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="20"
                value="<?php echo $estacionamiento; ?>">
        </fieldset>

        <fieldset>
            <legend>Vendedor</legend>
            <select name="vendedor" id="">
                <option value="">-- Seleccione --</option>
                <?php while($vendedor = mysqli_fetch_assoc($resultado)) : ?>
                <option <?php echo $vendedoresId === $vendedor['id'] ? 'selected' : '' ; ?> value="<?php echo $vendedor['id']; ?>">
                    <?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?> </option>
                <?php endwhile; ?>
            </select>
        </fieldset>
        <input type="submit" value="Crear Propiedad" class="boton boton-verde">
    </form>
    <a href=/bienesraices/admin/index.php class="boton boton-verde">Volver</a>
</main>

<?php
  incluirTemplate('footer');
?>