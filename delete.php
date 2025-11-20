<?php
require_once 'conexao.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sql = "DELETE FROM ordens_servico WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    try {
        $stmt->execute();
    } catch (PDOException $e) {
        // Se quiser, pode tratar erro (OS vinculada a algo etc.)
        // echo 'Erro ao excluir: ' . $e->getMessage();
    }
}

header('Location: index.php');
exit;
