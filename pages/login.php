<?php
// Inicia a sessão
session_start();

// Inclui a conexão com o banco
include_once '../conexao.php';

// Inicializa mensagem de erro ou sucesso
$msg = "";

// Processa o formulário ao enviar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    try {
        // Verifica se o email existe no banco
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Recupera os dados do usuário
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica a senha
            if (password_verify($senha, $usuario['senha'])) {
                // Cria a sessão do usuário
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];

                // Redireciona para a página inicial
                header("Location: ../index.php");
                exit;
            } else {
                $msg = "Senha incorreta!";
            }
        } else {
            $msg = "E-mail não encontrado!";
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
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Crie seu CSS personalizado -->
</head>
<body>
    <h1>Login</h1>
    <?php if (!empty($msg)): ?>
        <p><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required><br>

        <button type="submit">Entrar</button>
        <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui</a>.</p>
    </form>
</body>
</html>
