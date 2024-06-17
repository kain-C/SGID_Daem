$(document).ready(function() {
    var table = $('#reparaciones-table').DataTable({
        "ajax": {
            "url": "../controllers/reparacionesController.php",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "ID_Reparacion" },
            { "data": "ID_Dispositivo" },
            { "data": "Fecha" },
            {
                "data": "Descripcion_Reparacion",
                "render": function(data, type, row) {
                    return '<button class="btn btn-warning descripcion-btn" data-descripcion-reparacion="' + sanitize(data) + '" data-descripcion-dispositivo="' + sanitize(row.DescripcionDispositivo) + '" data-accion="' + sanitize(row.accion) + '">Ver</button>';
                }
            },
            { "data": "NombreDispositivo" },
            { "data": "Num_Serie" },
            { "data": "NombreEstablecimiento" },
            { "data": "NombreMarca" },
            {
                "data": null,
                "defaultContent": `
                    <button class="btn btn-success generate-qr-btn"><i class="bi bi-qr-code"></i></button>
                    <button class="btn btn-danger generate-report-btn"><i class="bi bi-filetype-pdf"></i></button>
                    <button class="btn btn-primary edit-reparacion-btn"><i class="bi bi-pencil"></i></button>
                `
            }
        ],
        "paging": true,
        "lengthMenu": [5, 10, 20],
        "pageLength": 5,
        "order": [[0, 'asc']],
        "searching": true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json",
            "info": "Mostrando del _START_ al _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });

    // Evento para abrir el modal de edición con la descripción de reparación
    $(document).on("click", ".edit-reparacion-btn", function() {
        var data = table.row($(this).parents('tr')).data();
        if (data) {
            $('#editDescripcionReparacion').val(stripHtml(data.Descripcion_Reparacion));
            $('#editReparacionId').val(data.ID_Reparacion);
            $('#editReparacionModal').modal('show');
        }
    });

    // Evento para abrir el modal con la descripción y la acción
    $(document).on("click", ".descripcion-btn", function() {
        var descripcionReparacion = $(this).data("descripcion-reparacion");
        var descripcionDispositivo = $(this).data("descripcion-dispositivo");
        var accion = $(this).data("accion");
        console.log("Descripción de Reparación:", descripcionReparacion);
        console.log("Descripción de Dispositivo:", descripcionDispositivo);
        console.log("Acción:", accion);
        $("#descripcion-content").html("<strong>Descripción de Reparación:</strong> " + descripcionReparacion + "<br><strong>Descripción de Ingreso:</strong> " + descripcionDispositivo + "<br><strong>Acción:</strong> " + accion);
        $("#descripcionModal").modal("show");
    });

    // Evento para generar el QR
    $(document).on("click", ".generate-qr-btn", function() {
        var data = table.row($(this).parents('tr')).data();
        generarQR(data);
    });

    // Evento para generar el Reporte
    $(document).on("click", ".generate-report-btn", function() {
        var data = table.row($(this).parents('tr')).data();
        generarReporte(data);
    });

    // Función para generar el QR Code y mostrar el modal
    function generarQR(data) {
        if (!data) {
            console.error('Datos del dispositivo no encontrados.');
            return;
        }

        // Limpiar el contenido del contenedor del QR antes de generar uno nuevo
        document.getElementById("qrcode-container").innerHTML = '';

        // Incluir el establecimiento y reducir la cantidad de datos en el QR
        var qrData = `ID: ${data.ID_Reparacion}, Est: ${data.NombreEstablecimiento}, Serie: ${data.Num_Serie}, Modelo: ${data.NombreDispositivo}`;
        var qrcode = new QRCode(document.getElementById("qrcode-container"), {
            text: qrData,
            width: 256, // Tamaño del QR Code
            height: 256, // Tamaño del QR Code
            correctLevel: QRCode.CorrectLevel.L // Nivel de corrección de errores más bajo para permitir más datos
        });

        // Ajustar el tamaño del card al tamaño del QR
        $('.qr-card').css({
            'width': 'auto',
            'display': 'inline-block'
        });

        // Muestra el modal
        $('#myModal').modal('show');

        // Asigna el ID de reparación al botón de descarga
        $('#save-qrcode-btn').data('idreparacion', data.ID_Reparacion);
    }

    // Evento para guardar el QR como imagen
    $('#save-qrcode-btn').click(function() {
        html2canvas(document.querySelector('#qrcode-container')).then(canvas => {
            var url = canvas.toDataURL('image/png');
            var idReparacion = $(this).data('idreparacion');
            var filename = 'qr_code_' + idReparacion + '.png';
            var link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });

    // Evento para guardar los cambios en la descripción de reparación
    $('#saveEditReparacionBtn').click(function() {
        var idReparacion = $('#editReparacionId').val();
        var descripcionReparacion = $('#editDescripcionReparacion').val();

        $.ajax({
            url: '../controllers/reparacionesController.php',
            type: 'POST',
            data: {
                action: 'updateDescripcionReparacion',
                idReparacion: idReparacion,
                descripcionReparacion: descripcionReparacion
            },
            success: function(response) {
                $('#editReparacionModal').modal('hide');
                table.ajax.reload(); // Recargar la tabla para reflejar los cambios
            },
            error: function(error) {
                console.error('Error al actualizar la descripción de reparación:', error);
            }
        });
    });

    // Función para convertir centímetros a píxeles
    function cmToPx(cm) {
        return cm * 37.795275591; // 1 cm ≈ 37.795275591 pixels
    }

// Evento clic para generar el PDF con cantidad dinámica de QR
$('#generate-pdf-btn').click(function() {
    if (typeof pdfMake === 'undefined' || typeof pdfMake.createPdf !== 'function') {
    alert("pdfMake no está disponible. Por favor, asegúrate de que el script de pdfMake está correctamente cargado.");
    return;
    }
    
    var quantity = parseInt($('#qrQuantity').val());
    var qrSizeCm = parseInt($('#qrSize').val());
    var qrPositions = $('input[name="qrPosition"]:checked').map(function() {
        return this.value;
    }).get();
    
    if (isNaN(quantity) || quantity <= 0) {
        alert("Ingrese una cantidad válida mayor a cero.");
        return;
    }
    
    if (isNaN(qrSizeCm) || qrSizeCm <= 0 || qrSizeCm > 10) {
        alert("Ingrese un tamaño válido entre 1 y 10 cm.");
        return;
    }
    
    if (isNaN(quantity) || quantity <= 0 || quantity > 30) {
        alert("Ingrese una cantidad válida entre 1 y 30.");
        return;
    }
    
    if (qrPositions.length === 0) {
        alert("Seleccione al menos una posición para el QR.");
        return;
    }
    
    html2canvas(document.querySelector('#qrcode-container')).then(canvas => {
        var imgData = canvas.toDataURL('image/png');
        var qrSizePx = cmToPx(qrSizeCm);
        var margin = 10; // Margen entre QR codes
    
        var qrContent = [];
    
        function addQr() {
            return {
                image: imgData,
                width: qrSizePx,
                height: qrSizePx,
                margin: [margin / 2, margin / 2, margin / 2, margin / 2] // Margen para separar los QR codes
            };
        }
    
        function addCutLine(horizontal, length) {
            return {
                canvas: [
                    {
                        type: 'line',
                        x1: 0,
                        y1: 0,
                        x2: horizontal ? length : 0,
                        y2: horizontal ? 0 : length,
                        dash: { length: 5 },
                        lineWidth: 0.5,
                        lineColor: '#000000'
                    }
                ],
                margin: horizontal ? [0, margin / 2] : [margin / 2, 0]
            };
        }
    
        for (var i = 0; i < quantity; i++) {
            var row = [
                qrPositions.includes('start') ? addQr() : { text: '', width: qrSizePx, height: qrSizePx },
                qrPositions.includes('middle') ? addQr() : { text: '', width: qrSizePx, height: qrSizePx },
                qrPositions.includes('end') ? addQr() : { text: '', width: qrSizePx, height: qrSizePx }
            ];
    
            var rowWithLines = { 
                columns: [], 
                columnGap: margin 
            };
    
            // Añadir líneas de corte verticales entre los QR codes
            row.forEach((qr, index) => {
                rowWithLines.columns.push(qr);
                if (index < row.length - 1) {
                    rowWithLines.columns.push(addCutLine(false, qrSizePx + margin));
                }
            });
    
            qrContent.push(rowWithLines); // Añadir fila con QR y líneas verticales
    
            if (i < quantity - 1) {
                qrContent.push(addCutLine(true, (qrSizePx + margin) * row.length)); // Línea horizontal entre filas
            }
        }
    
        var docDefinition = {
            content: qrContent,
            margin: [0, 20, 0, 0] // Aumentar el margen superior
        };
    
        pdfMake.createPdf(docDefinition).open();
        });
    });
    
    async function generarReporte(data) {
        if (!data) {
            console.error('Datos del dispositivo no encontrados.');
            return;
        }
    
        const { PDFDocument, rgb, StandardFonts } = PDFLib;
    
        // Crear un nuevo documento PDF
        const pdfDoc = await PDFDocument.create();
        let page = pdfDoc.addPage([595, 842]); // Tamaño A4 en puntos
    
        // Añadir el logotipo
        const logoUrl = '../img/logoP.png'; // Ruta al logo
        const logoImageBytes = await fetch(logoUrl).then(res => res.arrayBuffer());
        const logoImage = await pdfDoc.embedPng(logoImageBytes);
        const logoDims = logoImage.scale(0.3); // Tamaño del logo
    
        page.drawImage(logoImage, {
            x: 50,
            y: page.getHeight() - logoDims.height - 40,
            width: logoDims.width,
            height: logoDims.height,
        });
    
        // Título del informe
        const font = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
        const fontNormal = await pdfDoc.embedFont(StandardFonts.Helvetica);
        page.drawText('INFORME DE EQUIPOS TÉCNICOS', {
            x: 135,
            y: page.getHeight() - 120,
            size: 20,
            font: font,
            color: rgb(0, 0, 0),
        });
    
        // Fecha del informe en la esquina superior derecha
        page.drawText(`${(new Date()).toLocaleDateString()}`, {
            x: page.getWidth() - 100,
            y: page.getHeight() - 40,
            size: 12,
            font: fontNormal,
            color: rgb(0, 0, 0),
        });
    
        // Información del cliente y del servicio en formato tabla
        const infoBox = {
            x: 50,
            y: page.getHeight() - 160,
            width: page.getWidth() - 100,
            height: 180
        };
        page.drawRectangle({
            x: infoBox.x,
            y: infoBox.y - infoBox.height,
            width: infoBox.width,
            height: infoBox.height,
            borderColor: rgb(0, 0, 0),
            borderWidth: 1,
        });
    
        let currentY = infoBox.y - 20;
        const lineHeight = 16; // Aumentar el lineHeight para más espaciado
        const leftColumnX = infoBox.x + 10;
        const rightColumnX = infoBox.x + 220;
    
        const infoData = [
            ["Acción:", data.accion || ''],
            ["Marca:", data.NombreMarca || ''],
            ["Establecimiento responsable:", data.NombreEstablecimiento || ''],
            ["Número de serie:", data.Num_Serie || ''],
            ["Modelo:", data.Modelo || ''],
            ["Tipo de dispositivo:", data.Tipo_dispositivo || ''],
            ["Número de equipos revisados:", "1"], // Placeholder
            ["Nombre del técnico:", nombreUsuario || ''],
            ["Fecha del servicio técnico:", data.Fecha || '']
        ];
    
        infoData.forEach(item => {
            page.drawText(item[0], {
                x: leftColumnX,
                y: currentY,
                size: 12,
                font: fontNormal,
                color: rgb(0, 0, 0),
            });
            page.drawText(item[1], {
                x: rightColumnX,
                y: currentY,
                size: 12,
                font: fontNormal,
                color: rgb(0, 0, 0),
            });
            currentY -= lineHeight;
        });
    
        currentY -= 40;
    
        // Secciones del informe
        const sections = [
            ["A. TÍTULO DEL INFORME", "Revisión y programación de software"], // Placeholder
            ["B. DESCRIPCIÓN DE LA FALLA", sanitize(data.DescripcionDispositivo) || ''],
            ["C. EVALUACIÓN Y ANÁLISIS TÉCNICO DE FALLA", sanitize(data.Descripcion_Reparacion) || ''],
        ];
    
        const maxY = 50; // Margen inferior
        sections.forEach(section => {
            if (currentY - lineHeight < maxY) {
                page = pdfDoc.addPage([595, 842]);
                currentY = page.getHeight() - 50; // Nuevo margen superior
            }
    
            page.drawText(section[0], {
                x: infoBox.x,
                y: currentY,
                size: 12,
                font: font,
                color: rgb(0, 0, 0),
            });
            currentY -= lineHeight;
    
            const wrappedText = wrapText(sanitize(section[1]), 90); // Ajustar ancho de línea si es necesario
            wrappedText.forEach(line => {
                if (currentY - lineHeight < maxY) {
                    page = pdfDoc.addPage([595, 842]);
                    currentY = page.getHeight() - 50; // Nuevo margen superior
                }
    
                page.drawText(line, {
                    x: infoBox.x,
                    y: currentY,
                    size: 12,
                    font: fontNormal,
                    color: rgb(0, 0, 0),
                });
                currentY -= lineHeight;
            });
    
            // Línea divisoria poco visible
            page.drawLine({
                start: { x: infoBox.x, y: currentY },
                end: { x: infoBox.x + infoBox.width, y: currentY },
                thickness: 0.5,
                color: rgb(0.5, 0.5, 0.5)
            });
    
            currentY -= lineHeight;
        });
    
        // Firmas
        if (currentY - 40 < maxY) {
            page = pdfDoc.addPage([595, 842]);
            currentY = page.getHeight() - 50; // Nuevo margen superior
        }
        currentY -= 20;
        page.drawText('Firma del técnico:', { x: 50, y: currentY, size: 12, font: font });
        page.drawText('_______________________', { x: 50, y: currentY - 10, size: 12, font: font });
        page.drawText('Firma del cliente:', { x: 350, y: currentY, size: 12, font: font });
        page.drawText('_______________________', { x: 350, y: currentY - 10, size: 12, font: font });
    
        // Guardar el PDF
        const pdfBytes = await pdfDoc.save();
        const blob = new Blob([pdfBytes], { type: 'application/pdf' });
        const url = URL.createObjectURL(blob);
        window.open(url);
    }
    
    // Función para sanitizar los datos eliminando posibles caracteres HTML
    function sanitize(data) {
        var div = document.createElement('div');
        div.innerHTML = data;
        return div.textContent || div.innerText || "";
    }
    
    // Función para eliminar las etiquetas HTML de una cadena
    function stripHtml(html) {
        var tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
    }
    
    // Función para envolver el texto a un ancho máximo
    function wrapText(text, maxLength) {
        const words = text.split(' ');
        const lines = [];
        let currentLine = '';
    
        words.forEach(word => {
            if ((currentLine + word).length > maxLength) {
                lines.push(currentLine.trim());
                currentLine = word + ' ';
            } else {
                currentLine += word + ' ';
            }
        });
    
        lines.push(currentLine.trim());
        return lines;
    }
    
    // Agregar eventos para los botones de navegación
    $('#previous-btn').click(function() {
        window.history.back();
    });
    
    $('#next-btn').click(function() {
        window.history.forward();
    });
    
    // Función para sanitizar los datos eliminando posibles caracteres HTML
    function sanitize(data) {
        var div = document.createElement('div');
        div.innerHTML = data;
        return div.textContent || div.innerText || "";
    }
    
    // Función para eliminar las etiquetas HTML de una cadena
    function stripHtml(html) {
        var tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
    }
    
    // Función para envolver el texto a un ancho máximo
    function wrapText(text, maxLength) {
        const words = text.split(' ');
        const lines = [];
        let currentLine = '';
    
        words.forEach(word => {
            if ((currentLine + word).length > maxLength) {
                lines.push(currentLine.trim());
                currentLine = word + ' ';
            } else {
                currentLine += word + ' ';
            }
        });
    
        lines.push(currentLine.trim());
        return lines;
    }
    
    // Agregar eventos para los botones de navegación
    $('#previous-btn').click(function() {
        window.history.back();
    });
    
    $('#next-btn').click(function() {
        window.history.forward();
    });
    
    
    // Función para sanitizar los datos eliminando posibles caracteres HTML
    function sanitize(data) {
        var div = document.createElement('div');
        div.innerHTML = data;
        return div.textContent || div.innerText || "";
    }
    
    // Función para eliminar las etiquetas HTML de una cadena
    function stripHtml(html) {
        var tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
    }
    
    // Función para envolver el texto a un ancho máximo
    function wrapText(text, maxLength) {
        const words = text.split(' ');
        const lines = [];
        let currentLine = '';
    
        words.forEach(word => {
            if ((currentLine + word).length > maxLength) {
                lines.push(currentLine.trim());
                currentLine = word + ' ';
            } else {
                currentLine += word + ' ';
            }
        });
    
        lines.push(currentLine.trim());
        return lines;
    }
    
    // Agregar eventos para los botones de navegación
    $('#previous-btn').click(function() {
        window.history.back();
    });
    
    $('#next-btn').click(function() {
        window.history.forward();
    });
    
    
    // Agregar eventos para los botones de navegación
    $('#previous-btn').click(function() {
        window.history.back();
    });
    
    $('#next-btn').click(function() {
        window.history.forward();
    });
    
    
    // Función para sanitizar los datos eliminando posibles caracteres HTML
    function sanitize(data) {
        var div = document.createElement('div');
        div.innerHTML = data;
        return div.textContent || div.innerText || "";
    }
    
    // Función para eliminar las etiquetas HTML de una cadena
    function stripHtml(html) {
        var tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
    }
    
    // Función para envolver el texto a un ancho máximo
    function wrapText(text, maxLength) {
        const words = text.split(' ');
        const lines = [];
        let currentLine = '';
    
        words.forEach(word => {
            if ((currentLine + word).length > maxLength) {
                lines.push(currentLine.trim());
                currentLine = word + ' ';
            } else {
                currentLine += word + ' ';
            }
        });
    
        lines.push(currentLine.trim());
        return lines;
    }
    
    // Agregar eventos para los botones de navegación
    $('#previous-btn').click(function() {
        window.history.back();
    });
    
    $('#next-btn').click(function() {
        window.history.forward();
    });
    
    // Función para sanitizar los datos eliminando posibles caracteres HTML
    function sanitize(data) {
        var div = document.createElement('div');
        div.innerHTML = data;
        return div.textContent || div.innerText || "";
    }
    
    // Función para eliminar las etiquetas HTML de una cadena
    function stripHtml(html) {
        var tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
    }
    
    
    
    // Función para sanitizar los datos eliminando posibles caracteres HTML
    function sanitize(data) {
        var div = document.createElement('div');
        div.innerHTML = data;
        return div.textContent || div.innerText || "";
    }
    
    // Función para sanitizar los datos eliminando posibles caracteres HTML
    function sanitize(data) {
        var div = document.createElement('div');
        div.innerHTML = data;
        return div.textContent || div.innerText || "";
    }

    // Función para eliminar las etiquetas HTML de una cadena
    function stripHtml(html) {
        var tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
    }

    // Agregar eventos para los botones de navegación
    $('#previous-btn').click(function() {
        window.history.back();
    });

    $('#next-btn').click(function() {
        window.history.forward();
    });
    
});
