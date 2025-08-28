<?php

$errores = [];
$nombre = $sexo = $edad = $bday = $country = $photo = $telefono = $correo = $domicilio = $list = $excel = "";
$error = "";
$rutaArchivo = "";
$xlsxFile = "";
$datosExcel = [];
$fecha_actual = date('Y-m-d H:i:s');
$lineas = "";
$fecha_respuesta = $fecha_actual;

// Función para obtener un directorio con permisos de escritura
function obtenerDirectorioEscritura($directorioPreferido = "uploads/")
{
    if (!file_exists($directorioPreferido)) {
        @mkdir($directorioPreferido, 0755, true);
    }

    if (file_exists($directorioPreferido) && is_writable($directorioPreferido)) {
        return $directorioPreferido;
    }

    $directorioTemporal = sys_get_temp_dir() . '/formulario_uploads/';
    if (!file_exists($directorioTemporal)) {
        @mkdir($directorioTemporal, 0755, true);
    }

    if (file_exists($directorioTemporal) && is_writable($directorioTemporal)) {
        return $directorioTemporal;
    }


    return "./";
}

// Obtener directorio con permisos de escritura
$directorio = obtenerDirectorioEscritura();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_respuesta = $fecha_actual;
    $nombre = test_input($_POST["nombre"] ?? "");
    $sexo = test_input($_POST["sexo"] ?? "");
    $edad = test_input($_POST["age"] ?? "");
    $bday = test_input($_POST["bday"] ?? "");
    $country = test_input($_POST["country"] ?? "");
    $telefono = test_input($_POST["phone"] ?? "");
    $correo = test_input($_POST["correo"] ?? "");
    $domicilio = test_input($_POST["domicilio"] ?? "");

    // Si la opcion Otro es seleccionada, un nuevo campo de texto se abre
    if ($sexo == "Otro" && isset($_POST['especifique'])) {
        $sexo = test_input($_POST['especifique']);
    }

    // Procesar la imagen
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES["photo"]["name"]);
        $rutaArchivo = $directorio . $nombreArchivo;

        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $rutaArchivo)) {
            // Si no se puede mover al directorio, usar datos temporales de la imagen
            $rutaArchivo = "data:image/jpeg;base64," . base64_encode(file_get_contents($_FILES["photo"]["tmp_name"]));
            $error = "Advertencia: La imagen se ha procesado pero no se ha guardado en el servidor. ";
            $error .= "Se mostrará directamente desde los datos temporales.";
        }
    } elseif (isset($_FILES["photo"]) && $_FILES["photo"]["error"] != UPLOAD_ERR_NO_FILE) {
        $error = "Error al cargar la imagen: " . $_FILES["photo"]["error"];
        include 'Formulario.php';
        exit();
    } else {
        $error = "Debe subir una imagen.";
        include 'Formulario.php';
        exit();
    }

    // Verifica que el archivo de texto existe
    if (isset($_FILES["list"]) && $_FILES["list"]["error"] == UPLOAD_ERR_OK) {
        $archivo = $_FILES["list"]["tmp_name"];
        if (file_exists($archivo)) {
            $lineas = file_get_contents($archivo);
        } else {
            $error = "El archivo no existe.";
            include 'Formulario.php';
            exit();
        }
    } else {
        $error = "Error al subir el archivo de texto.";
        include 'Formulario.php';
        exit();
    }

    // Comprueba la extension del archivo Excel
    if (isset($_FILES['excel']) && $_FILES['excel']['error'] == UPLOAD_ERR_OK) {
        $xlsxFile = $_FILES['excel']['tmp_name'];
        $extension = strtolower(pathinfo($_FILES['excel']['name'], PATHINFO_EXTENSION));

        if ($extension === 'xlsx') {
            $datosExcel = procesarXLSX($xlsxFile);
        } elseif ($extension === 'csv') {
            $datosExcel = procesarCSV($xlsxFile);
        } else {
            $error = "Formato de archivo no soportado. Use XLSX o CSV.";
            include 'Formulario.php';
            exit();
        }
    } else {
        $error = "Error al subir el archivo Excel.";
        include 'Formulario.php';
        exit();
    }

    // Comprueba si todos los campos estan rellenos
    if (
        empty($nombre) || empty($sexo) || empty($edad) || empty($bday) ||
        empty($country) || empty($telefono) || empty($correo) || empty($domicilio)
    ) {
        $error = "Por favor, complete todos los campos.";
        include 'Formulario.php';
        exit();
    }

    // Si todo está bien, incluir el archivo que muestra los resultados
    include 'Formulario.php';
    exit();
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Comprueba la extension del archivo y extrae los datos
function procesarCSV($archivo)
{
    $datos = [];
    if (($handle = fopen($archivo, "r")) !== FALSE) {
        while (($fila = fgetcsv($handle, 1000, ",")) !== FALSE) {
            array_push($datos, $fila);
        }
        fclose($handle);
    }
    return $datos;
}

// Comprueba la extension del archivo y extrae los datos
function procesarXLSX($archivo)
{
    $datos = [];
    $zip = new ZipArchive;
    if ($zip->open($archivo) === TRUE) {
        $sharedStrings = [];
        if (($xmlStrings = $zip->getFromName("xl/sharedStrings.xml")) !== false) {
            $xmlStrings = simplexml_load_string($xmlStrings);
            foreach ($xmlStrings->si as $item) {
                array_push($sharedStrings, (string) $item->t);
            }
        }
        // extrae los datos y los organiza en columnas y filas 
        $xmlSheet = simplexml_load_string($zip->getFromName("xl/worksheets/sheet1.xml"));

        foreach ($xmlSheet->sheetData->row as $row) {
            $fila = [];
            foreach ($row->c as $c) {
                $value = (string) $c->v;
                if (isset($c['t']) && $c['t'] == 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                }
                $fila[] = $value;
            }
            $datos[] = $fila;
        }
        $zip->close();
        return $datos;
    } else {
        return [];
    }
}

// Son las validaciones del correo para verificar si es valido
function validarEmail($email)
{
    // sirve para eliminar espacios en blanco al inicio y final
    $email = trim($email);

    // verifica si el correo empieza con numeros
    if (preg_match('/^\d/', $email)) {
        return false;
    }

    // cerifica si contiene espacios dentro del correo
    if (strpos($email, ' ') !== false) {
        return false;
    }

    // verificar si contiene simbolos o caracterres invalidos
    if (preg_match('/[^a-zA-Z0-9@._-]/', $email)) {
        return false;
    }

    // verifica el formato basico del correo
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    return true;
}

class TextFileRenderer
{
    private $lineas;

    public function __construct($lineas)
    {
        $this->lineas = $lineas;
    }

    public function renderTable()
    {
        if (empty($this->lineas)) {
            return "";
        }

        $output = '<table class="table table-success table-striped">';
        $output .= '<thead class="table-dark">';
        $output .= '<tr><th scope="col">Nombre</th><th scope="col">Apellidos</th></tr>';
        $output .= '</thead><tbody>';

        $lineasArray = explode(",", $this->lineas);
        foreach ($lineasArray as $linea) {
            if (!empty(trim($linea))) {
                $name = explode(" ", $linea, 2);
                $nombre = isset($name[0]) ? htmlspecialchars($name[0]) : '';
                $apellidos = isset($name[1]) ? htmlspecialchars($name[1]) : '';

                $output .= '<tr>';
                $output .= '<th scope="col">' . $nombre . '</th>';
                $output .= '<th scope="col">' . $apellidos . '</th>';
                $output .= '</tr>';
            }
        }

        $output .= '</tbody></table>';
        return $output;
    }
}
class ExcelRenderer
{
    private $datosExcel;

    public function __construct($datosExcel)
    {
        $this->datosExcel = $datosExcel;
    }

    public function renderTable()
    {
        if (empty($this->datosExcel)) {
            return "<p style='color:red;'>No se pudieron procesar los datos del archivo.</p>";
        }

        $output = "<table border='1' cellpadding='5'>";
        $isFirstRow = true;

        foreach ($this->datosExcel as $fila) {
            $output .= "<tr>";
            $colIndex = 0;

            foreach ($fila as $value) {
                $style = "";

                // Para la primera fila (encabezados)
                if ($isFirstRow) {
                    $style = "background-color: #4e4e4eab; font-weight:bold;";
                } else {
                    // Aplica los estilos según el tipo de dato y la columna
                    if ($colIndex == 1) { // segunda columna
                        if (strtoupper($value) === "H") {
                            $style = "background-color: #23c7c7ff; font-weight:bold;";
                        } elseif (strtoupper($value) === "M") {
                            $style = "background-color: #e33abbff; font-weight:bold;";
                        }
                    } elseif ($colIndex == 2) { // tercera columna
                        if (is_numeric($value)) {
                            if ((int) $value < 18) {
                                $style = "background-color: #fa3d03ff; font-weight:bold;";
                            } else {
                                $style = "background-color: #8279c5ff; font-weight:bold;";
                            }
                        }
                    } elseif ($colIndex == 3) { // cuarta columna
                        if (validarEmail($value)) {
                            $style = "background-color: #5de51fff; font-weight:bold;";
                        } else {
                            $style = "background-color: #ff0342ff; font-weight:bold;";
                        }
                    }
                }

                $output .= "<td style='$style'>" . htmlspecialchars($value) . "</td>";
                $colIndex++;
            }

            $output .= "</tr>";
            $isFirstRow = false;
        }

        $output .= "</table>";
        return $output;
    }
}
?>