<?php
require_once 'conexao.php';

// Verifica se veio via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id     = isset($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : 0;
    $titulo         = trim($_POST['titulo'] ?? '');
    $descricao      = trim($_POST['descricao'] ?? '');
    $data_prevista  = $_POST['data_prevista'] ?? '';
    $valor_previsto = $_POST['valor_previsto'] ?? '0';

    // Validação básica
    if ($cliente_id <= 0 || $titulo === '' || $descricao === '') {
        header('Location: index.php');
        exit;
    }

    // Ajusta o formato da data_prevista (datetime-local vem com "T")
    if ($data_prevista !== '') {
        $data_prevista = str_replace('T', ' ', $data_prevista) . ':00'; // adiciona segundos
    } else {
        $data_prevista = null; // permite NULL
    }

    // Normaliza número decimal
    $valor_previsto = str_replace(',', '.', trim($valor_previsto));
    if ($valor_previsto === '' || !is_numeric($valor_previsto)) {
        $valor_previsto = 0;
    }

    // Data de abertura
    $data_abertura = date('Y-m-d H:i:s');

    $sql = "INSERT INTO ordens_servico 
                (cliente_id, titulo, descricao, status, data_abertura, data_prevista, valor_previsto, valor_final, criado_por)
            VALUES
                (:cliente_id, :titulo, :descricao, 'aberta', :data_abertura, :data_prevista, :valor_previsto, :valor_final, :criado_por)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':cliente_id', $cliente_id, PDO::PARAM_INT);
    $stmt->bindValue(':titulo', $titulo);
    $stmt->bindValue(':descricao', $descricao);
    $stmt->bindValue(':data_abertura', $data_abertura);

    // Bind de data_prevista permitindo NULL corretamente
    if ($data_prevista === null) {
        $stmt->bindValue(':data_prevista', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':data_prevista', $data_prevista);
    }

    $stmt->bindValue(':valor_previsto', number_format((float)$valor_previsto, 2, '.', ''));
    $stmt->bindValue(':valor_final', number_format((float)$valor_previsto, 2, '.', '')); // por enquanto igual ao previsto
    $stmt->bindValue(':criado_por', 1, PDO::PARAM_INT); // fixo 1

    $stmt->execute();
}

// Volta para o index
header('Location: index.php');
exit;
