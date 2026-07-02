<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ticket de compra</title>

    <style>
        /* VARIABLES 
        (DomPDF sí soporta variables básicas en muchos casos, pero por seguridad usar 
        valores directos) */
        body {
            background-color: #ffffff;
            color: #1c1c1b;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        h1 {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }

        h6 {
            font-weight: normal;
            margin: 0;
        }

        hr {
            /* border: 1px solid #ccc; */
            margin: 20px 0;
            border-top: 2px dashed; /* Estilo punteado */
        }

        .main {
            width: 100%;
            margin-top: 40px;
        }

        .ticket-container {
            width: 500px;
            margin: 0 auto;
            background-color: #e8e4e1;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        td {
            padding: 8px 0;
        }

        .label {
            font-weight: bold;
        }

        .importe {
            font-size: 20px;
            font-weight: bold;
        }

        .nota {
            font-size: 12px;
        }
    </style>
</head>

<body>

    <div class="main">

        <div class="ticket-container">
            <h1>Ticket de compra</h1>
            
            <hr>

            <table>
                <tr>
                    <td class="label">Fecha de compra</td>
                </tr>
                <tr>
                    <td>{{ $fecha }}</td>
                </tr>

                <tr>
                    <td class="label">Nombre</td>
                </tr>
                <tr>
                    <td>{{ $nombre }}</td>
                </tr>

                <tr>
                    <td class="label">DNI</td>
                </tr>
                <tr>
                    <td>{{ $dni }}</td>
                </tr>

                <tr>
                    <td class="label">Teléfono</td>
                </tr>
                <tr>
                    <td>{{ $telefono }}</td>
                </tr>

                <tr>
                    <td class="label">Tipo de abono</td>
                </tr>
                <tr>
                    <td>{{ $tipo }}</td>
                </tr>

                <tr>
                    <td class="label">Código de asiento</td>
                </tr>
                <tr>
                    <td>{{ $asiento }}</td>
                </tr>
                
                <tr>
                    <td style="padding-top:20px;" class="importe">
                        Importe {{ $precio }}€
                    </td>
                </tr>

                <tr>
                    <td class="nota">
                        @if ($edad < 12) * Tarifa especial Niños/as menores de 12 años: Rebaja de 80€.
                        @elseif ($edad > 65) * Tarifa especial Jubilados y mayores de 65 años: Rebaja del 50%.
                        @endif
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>




