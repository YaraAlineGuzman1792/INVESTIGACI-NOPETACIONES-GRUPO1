<?php
// Función para resolver el problema de transporte usando el método de menor costo
function menorCosto($costos, $ofertas, $demandas) {
    $n = count($ofertas);
    $m = count($demandas);
    $result = array_fill(0, $n, array_fill(0, $m, 0));

    // Crear copias de las ofertas y demandas para manipulación
    $ofertasRestantes = $ofertas;
    $demandasRestantes = $demandas;

    while (array_sum($ofertasRestantes) > 0 && array_sum($demandasRestantes) > 0) {
        // Encontrar el menor costo en la matriz de costos
        $min = PHP_INT_MAX;
        $x = -1;
        $y = -1;
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                if ($costos[$i][$j] < $min && $ofertasRestantes[$i] > 0 && $demandasRestantes[$j] > 0) {
                    $min = $costos[$i][$j];
                    $x = $i;
                    $y = $j;
                }
            }
        }

        // Asignar la cantidad mínima posible a la celda seleccionada
        $cantidad = min($ofertasRestantes[$x], $demandasRestantes[$y]);
        $result[$x][$y] = $cantidad;

        // Actualizar las ofertas y demandas restantes
        $ofertasRestantes[$x] -= $cantidad;
        $demandasRestantes[$y] -= $cantidad;
    }

    return $result;
}
// Función para calcular el costo total
function calcularCostoTotal($resultado, $costos) {
    $total = 0;
    for ($i = 0; $i < count($resultado); $i++) {
        for ($j = 0; $j < count($resultado[$i]); $j++) {
            $total += $resultado[$i][$j] * $costos[$i][$j];
        }
    }
    return $total;
}

// Manejo del formulario
$ofertas = $demandas = $costos = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ofertas'], $_POST['demandas'])) {
    // Convertir las cadenas de texto en arreglos usando explode
    $ofertas = array_map('intval', explode(',', $_POST['ofertas']));
    $demandas = array_map('intval', explode(',', $_POST['demandas']));

    // Si se han enviado los costos, calcular el resultado
    if (isset($_POST['costos'])) {
        $costos = $_POST['costos'];
        foreach ($costos as $i => $fila) {
            $costos[$i] = array_map('intval', $fila);
        }
        $resultado = menorCosto($costos, $ofertas, $demandas);
        $costoTotal = calcularCostoTotal($resultado, $costos);
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema de Transporte - Método de Menor Costo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        .input-group input {
            width: calc(100% - 20px);
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Problema de Transporte - Método de Menor Costo</h1>
    <form method="POST">
        <div class="input-group">
            <label for="ofertas">Ingresar Ofertas (separadas por comas):</label>
            <input type="text" name="ofertas" id="ofertas" placeholder="Ejemplo: 30, 40, 50" required>
        </div>
        <div class="input-group">
            <label for="demandas">Ingresar Demandas (separadas por comas):</label>
            <input type="text" name="demandas" id="demandas" placeholder="Ejemplo: 20, 50, 50" required>
        </div>
        <button type="submit" class="btn">Generar Tabla de Costos</button>
    </form>

    <?php if (!empty($ofertas) && !empty($demandas) && empty($resultado)): ?>
        <form method="POST">
            <input type="hidden" name="ofertas" value="<?= implode(',', $ofertas) ?>">
            <input type="hidden" name="demandas" value="<?= implode(',', $demandas) ?>">
            <h2>Ingresar Costos de Transporte</h2>
            <table>
                <thead>
                <tr>
                    <th>Oferta\Demanda</th>
                    <?php foreach ($demandas as $index => $demanda): ?>
                        <th>Demanda <?= $index + 1 ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($ofertas as $i => $oferta): ?>
                    <tr>
                        <th>Oferta <?= $i + 1 ?></th>
                        <?php foreach ($demandas as $j => $demanda): ?>
                            <td>
                                <input type="number" name="costos[<?= $i ?>][<?= $j ?>]" required>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn">Calcular Método de Menor Costo</button>
        </form>
    <?php endif; ?>

    <?php if (!empty($resultado)): ?>
        <h2>Resultados</h2>
        <table>
            <thead>
            <tr>
                <th>Oferta\Demanda</th>
                <?php foreach ($demandas as $index => $demanda): ?>
                    <th>Demanda <?= $index + 1 ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($resultado as $i => $fila): ?>
                <tr>
                    <th>Oferta <?= $i + 1 ?></th>
                    <?php foreach ($fila as $valor): ?>
                        <td><?= $valor ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Costo Total: <?= $costoTotal ?></h3>
    <?php endif; ?>
</div>
</body>
</html>