<?php
session_start();
include_once '../conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$msg = "";

// Processa o formulário ao enviar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $raca = $_POST['raca'];
    $descricao = $_POST['descricao'];
    $usuario_id = $_SESSION['usuario_id'];
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $fileType = mime_content_type($_FILES['foto']['tmp_name']); // Obtém o tipo MIME da imagem
    
        // Verifica o tipo MIME e aceita JPEG, JFIF ou PNG
        if (in_array($fileType, ['image/jpeg', 'image/jfif', 'image/png'])) {
            $foto = file_get_contents($_FILES['foto']['tmp_name']); // Lê o conteúdo da imagem como binário
        } else {
            echo "Formato de imagem não suportado. Apenas JPEG, JFIF ou PNG são aceitos.";
            exit;
        }
    } else {
        $foto = null; // Caso nenhuma foto seja enviada
    }
    echo "Tipo MIME detectado: " . mime_content_type($_FILES['foto']['tmp_name']);
    exit;
    

    try {
        // Insere o pet no banco
        $sql = "INSERT INTO pets (usuario_id, nome, idade, raca, descricao, foto) 
                VALUES (:usuario_id, :nome, :idade, :raca, :descricao, :foto)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':idade', $idade);
        $stmt->bindParam(':raca', $raca);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':foto', $foto, PDO::PARAM_LOB);

        if ($stmt->execute()) {
            $msg = "Pet cadastrado com sucesso!";
        } else {
            $msg = "Erro ao cadastrar o pet.";
        }
    } catch (PDOException $e) {
        $msg = "Erro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pet</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Cadastro de Pet</h1>
    <?php if (!empty($msg)): ?>
        <p><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
        <label for="nome">Nome do Pet:</label>
        <input type="text" name="nome" id="nome" required><br>

        <label for="idade">Idade:</label>
        <input type="number" name="idade" id="idade" required><br>

        <label for="raca">Raça:</label>
        <input type="text" name="raca" id="raca" required><br>

        <label for="descricao">Descrição:</label>
        <textarea name="descricao" id="descricao" required></textarea><br>

        <label for="foto">Foto do Pet:</label>
        <input type="file" name="foto" id="foto" accept="image/*" required><br>

        <button type="submit">Cadastrar</button>
    </form>
    <a href="../index.php">Voltar para a Página Inicial</a>
</body>
</html>
