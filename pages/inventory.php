<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Models\Inventory;

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Inventário</title>
</head>

<body>
    <h1>Inventário</h1>

    <pre>
        <?php
        // $add = Inventory::add(5, 100.00);

        $items = Inventory::getAll();
        foreach($items as $item) {
            echo $item;
        }
        // var_dump($items);
        ?>
    </pre>

</body>

</html>