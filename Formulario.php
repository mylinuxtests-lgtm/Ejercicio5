<?php
// Incluir el archivo de procesamiento
require_once 'Respuestas.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 80px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 1);
            text-align: center;
        }

        .error {
            color: red;
            padding: 10px;
            background-color: #ffeeee;
            border: 1px solid red;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .data-field {
            margin-bottom: 30px;
            padding: 10px;
            background-color: rgba(253, 255, 160, 1);
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 1);
            text-align: center;
        }

        .data-field h2 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 18px;
        }

        .data-fieldp {
            margin: 0;
            padding: 5px;
            background-color: blue;
            border-radius: 3px;
        }

        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        h1 {
            text-align: center;
            color: #db1811ff;
        }

        td,
        th {
            border: 1px solid #3b47b8ff;
            text-align: center;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #a0f5f8ff;
        }

        .back-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4e0848;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-link:hover {
            background-color: #a04598;
        }
    </style>
</head>

<body>
    <div class="container">

        <h1>Gracias por tus respuestas!!</h1>
        <input type="hidden" name="fecha_respuestas" value="<?php echo htmlspecialchars($fecha_respuesta); ?>">

        <?php if (!empty($error)): ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
            <div>
                <a href="Formulario.html" class="back-link">Volver al formulario</a> <br><br>
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)): ?>
            <div class="data-field">
                <h2>Nombre</h2>
                <p> <?php echo htmlspecialchars($nombre); ?> </p>
            </div>
            <div class="data-field">
                <h2>Sexo</h2>
                <p> <?php echo htmlspecialchars($sexo); ?> </p>
            </div>
            <div class="data-field">
                <h2>Edad</h2>
                <p> <?php echo htmlspecialchars($edad); ?> </p>
            </div>
            <div class="data-field">
                <h2>Cumpleaños</h2>
                <p> <?php echo htmlspecialchars($bday); ?> </p>
            </div>
            <div class="data-field">
                <h2>Nacionalidad</h2>
                <p> <?php echo htmlspecialchars($country); ?> </p>
            </div>
            <div class="data-field">
                <h2>Selfie</h2>
                <?php if (strpos($rutaArchivo, 'data:image') === 0): ?>
                    <img src="<?php echo $rutaArchivo; ?>" alt="Tu imagen" width="400">
                <?php else: ?>
                    <img src="<?php echo $rutaArchivo; ?>" alt="Tu imagen" width="400">
                <?php endif; ?>
            </div>
            <div class="data-field">
                <h2>Telefono</h2>
                <p> <?php echo htmlspecialchars($telefono); ?> </p>
            </div>
            <div class="data-field">
                <h2>Correo</h2>
                <p> <?php echo htmlspecialchars($correo); ?> </p>
            </div>
            <div class="data-field">
                <h2>Domicilio</h2>
                <p> <?php echo nl2br(htmlspecialchars($domicilio)); ?> </p>
            </div>

            <div class="table">
                <table class="table table-success table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Apellidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($lineas)) {
                            $lineasArray = explode(",", $lineas);
                            foreach ($lineasArray as $linea) {
                                if (!empty(trim($linea))) {
                                    $name = explode(" ", $linea);
                                    ?>
                                    <tr>
                                        <th scope="col"><?php echo isset($name[0]) ? htmlspecialchars($name[0]) : ''; ?></th>
                                        <th scope="col"><?php echo isset($name[1]) ? htmlspecialchars($name[1]) : ''; ?></th>
                                    </tr>
                                <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table> <br> <br>
            </div>

            <?php
            if (!empty($datosExcel)) {
                echo "<table border='1' cellpadding='5'>";

                $isFirstRow = true;
                foreach ($datosExcel as $fila) {
                    echo "<tr>";
                    $colIndex = 0;
                    foreach ($fila as $value) {
                        $style = "";

                        // Para la primera fila 
                        if ($isFirstRow) {
                            $style = "background-color: #4e4e4eab; font-weight:bold;";
                        } else {
                            // Aplica los estilos según el tipo de dato y la columna
                            if ($colIndex == 1) { //segunda columna
                                if (strtoupper($value) === "H") {
                                    $style = "background-color: #23c7c7ff; font-weight:bold;";
                                } elseif (strtoupper($value) === "M") {
                                    $style = "background-color: #e33abbff; font-weight:bold;";
                                }
                            } elseif ($colIndex == 2) { //tercera columna
                                if (is_numeric($value)) {
                                    if ((int) $value < 18) {
                                        $style = "background-color: #fa3d03ff; font-weight:bold;";
                                    } else {
                                        $style = "background-color: #8279c5ff; font-weight:bold;";
                                    }
                                }
                            } elseif ($colIndex == 3) { //cuarta columna
                                if (validarEmail($value)) {
                                    $style = "background-color: #5de51fff; font-weight:bold;";
                                } else {
                                    $style = "background-color: #ff0342ff; font-weight:bold;";
                                }
                            }
                        }

                        echo "<td style='$style'>" . htmlspecialchars($value) . "</td>";
                        $colIndex++;
                    }
                    echo "</tr>";
                    $isFirstRow = false;
                }
                echo "</table>";
            } else {
                echo "<p style='color:red;'>No se pudieron procesar los datos del archivo.</p>";
            }
            ?>

            <br><br>
            <div>
                <a href="Formulario.html" class="back-link">Volver al formulario</a> <br><br>
            </div>
        <?php else: ?>
            <div class="error">
                No se han recibido datos del formulario.
            </div>
            <div>
                <a href="Formulario.html" class="back-link">Volver al formulario</a> <br><br>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>