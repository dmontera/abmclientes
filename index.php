<?php

if (file_exists("data.txt")) {
    $jsonClientes = file_get_contents("data.txt");
    $aClientes = json_decode($jsonClientes, true);
} else {
    $aClientes = array();
}

$id = isset($_GET["id"]) ? $_GET["id"] : "";

if (isset($_GET["id"]) && isset($_GET["do"]) && $_GET["do"] == "eliminar") {
    $msg2 = "Datos borrados correctamente";
    unset($aClientes[$id]);


    $jsonClientes = json_encode($aClientes);
    file_put_contents("data.txt", $jsonClientes);
}

if ($_POST) {

    $msg = "Datos guardados correctamente";

    $dni = $_POST["txtDni"];
    $nombre = $_POST["txtNombre"];
    $telefono = $_POST["txtTelefono"];
    $correo = $_POST["txtCorreo"];
    $nombreImagen = "";

    if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
        $nombref = date("Ymdhmsi");
        $archivo_tmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreImagen = $nombref . "." . $extension;
        move_uploaded_file($archivo_tmp, "archivos/$nombreImagen");
    }

    if (isset($_GET["id"])) {

        $imagenAnterior = $aClientes[$id]["imagen"];
        //para editar nuevo archivo con respecto a uno anterior
        if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
            if ($imagenAnterior != "") {
                unlink("archivos/$imagenAnterior");
            }
        }

        //para que siga todo como antes si no se edita el archivo
        if ($_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
            $nombreImagen = $imagenAnterior;
        }

        //actualiza
        $aClientes[$id] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen,

        );
        
    } else {
        //es nuevo
        $aClientes[] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen,

        );
    }

    $jsonClientes = json_encode($aClientes);
    file_put_contents("data.txt", $jsonClientes);
    $id = "";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM clientes</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>


<body>
    <div class="container">
        <div class="row text-center">
            <div class="col-12">
                <h1>Registro de clientes </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <form action="" method="POST" enctype="multipart/form-data">
                    <?php if ($_POST) { ?>
                        <div class="alert alert-success">
                            <?php echo $msg; ?>
                        </div>
                    <?php } else if (isset($_GET["id"]) && isset($_GET["do"]) && $_GET["do"] == "eliminar") { ?>
                        <div class="alert alert-success">
                            <?php echo $msg2; ?>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="txtNombre">DNI:</label>
                            <input type="text" name="txtDni" id="txtDni" class="form-control" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["dni"] : ""; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="txtNombre">Nombre:</label>
                            <input type="text" name="txtNombre" id="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["nombre"] : ""; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="txtTelefono">Teléfono:</label>
                            <input type="text" name="txtTelefono" id="txtTelefono" class="form-control" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["telefono"] : ""; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Correo:</label>
                            <input type="mail" name="txtCorreo" id="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["correo"] : ""; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Archivo:</label>
                            <input type="file" name="archivo" id="archivo" class="form-control">
                        </div>
                    </div>
                    <div class="col-12 py-2">
                        <button type="submit" class="btn btn-primary" onclick="return confirmacion()">Guardar</button>
                    </div>
                </form>

            </div>
            <div class="col-12 col-sm-6">
                <table class="table table-hover border">

                    <tr>
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>

                    <?php foreach ($aClientes as $key => $cliente) { ?>
                        <tr>
                            <td><img src="archivos/<?php echo $cliente["imagen"]; ?>" class="img-thumbnail"></td>
                            <td><?php echo $cliente["dni"] ?></td>
                            <td><?php echo $cliente["nombre"] ?></td>
                            <td><?php echo $cliente["correo"] ?></td>
                            <td><a href="index.php?id=<?php echo $key; ?>"><i class="fas fa-edit"></i></a>
                                <a href="index.php?id=<?php echo $key; ?>&do=eliminar"><i class="fas fa-trash-alt" onclick="return confirmacionBorrar()"></i></a>
                            </td>
                        </tr>
                    <?php } ?>

                </table>
                <a href="index.php"><i class="fas fa-plus"></i></a>
            </div>

        </div>
    </div>

    <script>
        function confirmacion() {
            seguro = confirm("¿Esta seguro/a que desea guardar los datos?")

            if (seguro == true) {
                return true;
            } else {
                return false;
            }
        }

        function confirmacionBorrar() {
            borrar = confirm("¿Esta seguro/a que desea borrar los datos?")

            if (borrar == true) {
                return true;
            } else {
                return false;
            }
        }
    </script>

</body>

</html>