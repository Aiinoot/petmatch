<?php
// Inclui a conexão com o banco
include_once '../conexao.php';

// Inicializa mensagem de erro ou sucesso
$msg = "";

// Processa o formulário ao enviar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografa a senha

    try {
        // Verifica se o email já existe
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            $msg = "E-mail já cadastrado!";
        } else {
            // Insere o usuário no banco
            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha);

            if ($stmt->execute()) {
                $msg = "Usuário cadastrado com sucesso!";
            } else {
                $msg = "Erro ao cadastrar usuário!";
            }
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
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Crie seu CSS personalizado -->
</head>
<body>
    <h1>Cadastro de Usuário</h1>
    <?php if (!empty($msg)): ?>
        <p><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required><br>

        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required><br>

        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
