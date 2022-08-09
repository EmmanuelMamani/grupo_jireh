<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @foreach ($pru as $pr)
        <img src="data:image/png;base64,<?php
            echo base64_encode($pr->Comprobante);
        ?>">
    @endforeach
</body>
</html>