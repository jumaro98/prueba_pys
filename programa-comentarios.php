<?php

public function generarFormatoVisita($rutaFormato, $cod_solicitud_credito, $infoAdicional, $cargaDew = 0) {
    // Se crea una instancia del combinador de PDFs
    $pdfMerger = new PDFMerger();

    // Se crea una instancia del manejador de encriptación (para cifrar/descifrar archivos)
    $boEncriptar = new Encriptacion();

    // Se agrega el formato base del PDF que servirá como plantilla o encabezado
    $pdfMerger->addPDF($rutaFormato);

    // Se obtienen las imágenes asociadas a la visita, según el código del crédito
    $archivos = $this->obtenerImagenesVisita($cod_solicitud_credito);

    // 🔁 Recorre todos los archivos de imágenes obtenidos
    foreach ($archivos as $archivo) {
        // Construye un nombre base único para cada archivo PDF
        $base = $archivo[Generali::COD_SOLICITUD_CREDITO] . '_' . $archivo[Clase::TIPO] . '_' . $archivo[BaseDatos::COD_FOTOS_VISITA] . '.pdf';

        // Ruta del archivo cifrado (fuente)
        $archivoInicial = IMAGENES_VISITA . Generali::ENCODED . $base;

        // Ruta temporal donde se guardará el archivo descifrado
        $archivoFinal = IMAGENES_VISITA . $base;

        // Descifra el documento para poder unirlo con el PDF final
        $boEncriptar->descifrarDocumento($archivoInicial, $archivoFinal, false);

        // Agrega el PDF descifrado al merger
        $pdfMerger->addPDF($archivoFinal);
    }

    // 🔄 Combina todos los PDFs (formato base + fotos descifradas) en uno solo
    $pdfMerger->merge('file', $rutaFormato);

    // 🧹 Limpia los archivos temporales descifrados (los elimina del disco)
    foreach ($archivos as $archivo) {
        $base = $archivo[Generali::COD_SOLICITUD_CREDITO] . '_' . $archivo[Clase::TIPO] . '_' . $archivo[BaseDatos::COD_FOTOS_VISITA] . '.pdf';
        $archivoFinal = IMAGENES_VISITA . $base;
        @unlink($archivoFinal); // Se usa @ para evitar warnings si el archivo no existe
    }

    // ⚙️ Si NO se está en modo de carga DEW (por defecto cargaDew = 0)
    if (!$cargaDew) {
        // Se obtienen los documentos asociados al cliente
        $documentosCliente = $infoAdicional['documentos'];

        // Se obtiene la unidad asociada a la solicitud del crédito
        $unidad = $this->obtenerUnidadSolicitud($cod_solicitud_credito);

        // Se define la ruta final donde se guardará el PDF cifrado
        $rutaFinal = DIRECCION_CONTINGENCIA . "encoded_" . $infoAdicional['nombre_pdf'];

        // Cifra el archivo PDF final antes de almacenarlo
        $boEncriptar->cifrarDocumento($rutaFormato, $rutaFinal, true);

        // Guarda la información del archivo generado en la tabla de documentos
        $this->guardarInfoArchivoDocumentos(
            BaseDatos::TABLA_DOCUMENTOS,
            $infoAdicional['nombre_pdf'],
            $unidad
        );

        // Estructura de datos que se enviará para registrar el documento en el sistema
        $data_documento = array(
            Generali::COD_TIPO_CREDITO => $infoAdicional[Generali::COD_TIPO_CREDITO],
            Generali::COD_SOLICITUD_CREDITO => $cod_solicitud_credito,
            Generali::DOCUMENTOS => array(0 => $documentosCliente),
            'nombre_adjunto' => $documentosCliente,
            'cod_motivo' => 0,
            Generali::COD_USUARIO => $_SESSION[Generali::COD_USUARIO], // usuario actual
            Generali::COD_MODULO => 139 // código del módulo (probablemente “Visitas”)
        );

        // Confirma la carga del documento en el sistema
        $this->confirmarCargaDocumento($data_documento, true);

        // Termina el proceso y devuelve 1 (éxito)
        return 1;
    }

    // 📄 Si sí se requiere mostrar el PDF directamente (modo DEW), se genera un nombre y lo muestra
    $filename = "FormatoVisita_" . $cod_solicitud_credito . ".pdf";

    // Envía el PDF generado al navegador o salida estándar
    $this->streamPdf($filename, $rutaFormato);
};

// Función para enviar un archivo PDF al navegador y luego eliminarlo del servidor
public function streamPdf($fileName, $route) {
    // Indica al navegador que se va a transferir un archivo
    header('Content-Description: File Transfer');
    
    // Especifica el tipo de contenido como PDF
    header('Content-Type: application/pdf');
    
    // Indica que el archivo se descargará con el nombre proporcionado
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    
    // Evita que el navegador almacene en caché el archivo
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // Especifica el tamaño del archivo para la descarga
    header('Content-Length: ' . filesize($route));
    
    // Lee el archivo y lo envía al navegador
    readfile($route);
    
    // Elimina el archivo del servidor después de enviarlo
    @unlink($route);
}