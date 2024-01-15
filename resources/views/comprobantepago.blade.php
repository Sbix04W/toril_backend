<!DOCTYPE html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style type="text/css">
        .justifyText {
            text-align: justify;
        }

        .container {
            margin-right: 100px;
            margin-left: 50px;

        }

        .font-size {
            font-size: 16px;
        }

        .font-size-small {
            font-size: 14px;
        }

        .font-size-title {
            font-size: 25px;
            color: #00588D;
        }

        .separador {
            border-color: #9c9c9c2d;
            margin-right: 100px;
            margin-left: 50px;
        }

        .style-bold {

            font-weight: bold;
        }

        .interlineado {
            line-height: 200%
        }

        .oblique {
            font-style: oblique;
        }

        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>

<body>

    <div class="container">
        <div style="text-align: center">

        </div>
        <h2 style="text-align: center">Comprobante de Pago</h2>
        <hr class="separador">
        <p class="justifyText oblique">
        Estimado  <b>{{$data->nombre}} {{$data->apellido}} </b> se adjunta su comprobante de pago
      
        <hr class="separador">
        <br/>



    </div>
    <br>
 





</body>