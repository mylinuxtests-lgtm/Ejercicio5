<?php
// Incluir el archivo de procesamiento
require_once 'Respuestas.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fstyle.css" media="screen" />
    <title>Formulario</title>
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
                <?php
                $textRenderer = new TextFileRenderer($lineas);
                echo $textRenderer->renderTable();
                ?>
            </div>
            <br><br>

            <?php
            if (!empty($datosExcel)) {
                $excelRenderer = new ExcelRenderer($datosExcel);
                echo $excelRenderer->renderTable();
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