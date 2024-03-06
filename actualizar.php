<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


// VALIDAR LA URL CON UN ID VALIDO
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id) {
  header('location: http://localhost:8080/bienesraices/admin/index.php');
}

//BASE DE DATOS

require '../../includes/config/database.php';
$db = conectarDB();

//OBTENER LOS DATOS DE LA PROPIEDAD

$consulta = "SELECT * FROM propiedades WHERE id = ${id}";
$resultado = mysqli_query($db, $consulta);
$propiedad = mysqli_fetch_assoc($resultado);




//Consultar para optener los vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);


// Arreglo con mensajes de errores
$errores = [];  


//VARIABLES PARA QUE NO MARQUE ERRORES Y LAS TENGA PREDETERMINADAS PARA QUE EL USUARIO PUEDA EDITAR LO QUE QUIERA SION NECESIDAD DE RELLENAR TODOS LOS CAMPOS.

$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion = $propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$wc = $propiedad['wc'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedoresId = $propiedad['vendedores_id'];
$imagenPropiedad = $propiedad['imagen'];


// ejecutar el codigo despes que el usuario envie el formulario
if($_SERVER['REQUEST_METHOD']  === 'POST') {


    //ESTE PRE SIRVE PARA VALIDAR QUE EL ARRAY SE ESTE CREANDO CORRECTAMENTE
//    echo"<pre>";
//    var_dump($_POST);
//    echo"</pre>";

    //echo"<pre>";
    //var_dump($_FILES);
    //echo"</pre>";

    
// ACA EN ESTA OPCION SE VAN A REASIGNAR LASS VARIABLES CON LO QUE EL USUARIO ESCRIBIÓ

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

        //CREAR CARPETA
        $carpetaImagenes = '../../imagenes/';

        if(!is_dir($carpetaImagenes)) {
              mkdir($carpetaImagenes);
        }

        $nombreImagen = '';

        //SUBIDA DE ARCHIVOS

        if($imagen['name']) {

            unlink($carpetaImagenes . $propiedad['imagen'] );

        //ACTUALIZAR IMAGENES DE LA PROPIEDAD.
            $nombreImagen = md5( uniqid(rand(), true ) ) .".jpg";

            var_dump($imagen['tmp_name']);
            var_dump($carpetaImagenes . $nombreImagen . ".jpg");
            if (!move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen . ".jpg")) {
                $errores[] = "Hubo un error al subir el archivo";
            } else {
                $errores[] = "Archivo subido con éxito";
            }
        }


        // Insertar datos en la Base de datos que se están actualizando.
        $query = " UPDATE propiedades SET titulo = '${titulo}', precio = '${precio}', imagen = '${nombreImagen}', descripcion = '${descripcion}', habitaciones = ${habitaciones}, wc = ${wc}, estacionamiento = ${estacionamiento}, vendedores_Id = ${vendedoresId} WHERE id = ${id} ";


        //COMPROBACION DE CODIGO QUE SI ACTUALICE LOS ITEMS 
        //echo $query;


        $resultado = mysqli_query($db, $query);

        if($resultado) {
            //Redireccionar al usuario

            header('Location: http://localhost:8080/bienesraices/admin/index.php?resultado=2');

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
    <h1>Actualizar Propiedad</h1>

    <?php foreach($errores as $error):  ?>
    <div class="alerta error">
        <?php echo $error; ?>
    </div>

    <?php endforeach; ?>


    <form class="formulario" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Informacion General</legend>

            <label for="titulo">Titulo</label>
            <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" placeholder="Titulo Propiedad"
                value="<?php echo $precio; ?>">

            <label for="imagen">Imagen</label>
            <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">


            <img src="http://localhost:8080/bienesraices/imagenes/<?php echo $imagenPropiedad; ?>.jpg"
                class="imagen-small">


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
                <option <?php echo $vendedoresId === $vendedor['id'] ? 'selected' : '' ; ?>
                    value="<?php echo $vendedor['id']; ?>">
                    <?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?> </option>
                <?php endwhile; ?>
            </select>
        </fieldset>
        <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
    </form>
    <a href=/bienesraices/admin/index.php class="boton boton-verde">Volver</a>
</main>

<?php
  incluirTemplate('footer');
?>