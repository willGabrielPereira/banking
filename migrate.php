<?php

require __DIR__ . '/vendor/autoload.php';

use App\Helpers\DB;

$migrationsDir = __DIR__ . '/database/migrations';

try {
    echo "Iniciando processo de migração...\n";

    $db = new DB();
    $pdo = $db->getConnection();

    // 2. Criar a tabela de migrações se ela não existir (executando a primeira migração manualmente)
    $pdo->exec(file_get_contents($migrationsDir . '/0001_create_migrations_table.sql'));

    $stmt = $pdo->query("SELECT migration FROM migrations where migration != '0001_create_migrations_table.sql'");
    $executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $allMigrationFiles = glob($migrationsDir . '/*.sql');
    sort($allMigrationFiles);

    $newMigrations = 0;

    if (!count($allMigrationFiles)) {
        echo "Nenhum arquivo de migração encontrado no diretório: $migrationsDir\n";
        exit(0);
    }

    foreach ($allMigrationFiles as $file) {
        $migrationName = basename($file);

        if ($migrationName === '0001_create_migrations_table.sql') {
            continue;
        }

        if (!in_array($migrationName, $executedMigrations)) {
            echo "Executando migração: $migrationName\n";

            try {
                $sql = file_get_contents($file);
                $pdo->exec($sql);

                $insertStmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
                $insertStmt->execute([':migration' => $migrationName]);

                echo "Migração $migrationName executada e registrada com sucesso.\n";
            } catch (Exception $e) {
                echo "ERRO ao executar a migração $migrationName: " . $e->getMessage() . "\n";
                exit(1);
            }
        }
    }

    echo "Processo de migração concluído.\n";

} catch (PDOException $e) {
    echo "ERRO de conexão com o banco de dados: " . $e->getMessage() . "\n";
    exit(1);
}