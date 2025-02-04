<?php
session_start();
include_once 'conexao.php'; // Inclui o arquivo de conexão com o banco

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: pages/login.php");
    exit;
}

echo "Bem-vindo, " . htmlspecialchars($_SESSION['usuario_nome']) . "!";

// Busca os pets do usuário logado
$sql = "SELECT * FROM pets WHERE usuario_id = :usuario_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
$stmt->execute();
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma PetMatch</title>
    <style>
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 20px;
        }
        img {
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Página Inicial</h1>
    <p>Bem-vindo(a) à plataforma PetMatch!</p>
    <h2>Seus Pets</h2>
    <?php if (!empty($pets)): ?>
        <ul>
            <?php foreach ($pets as $pet): ?>
                <li>
                    <strong><?= htmlspecialchars($pet['nome']) ?></strong> - <?= htmlspecialchars($pet['raca']) ?> (<?= htmlspecialchars($pet['idade']) ?> anos)
                    <p><?= htmlspecialchars($pet['descricao']) ?></p>
                    <?php if (!empty($pet['foto'])): ?>
                        <?php
                        // Detecta o tipo MIME da imagem diretamente no PHP
                        $imgData = stream_get_contents($pet['foto']); // Garante que a imagem é uma string
                        $finfo = new finfo(FILEINFO_MIME_TYPE); // Inicializa o finfo
                        $mimeType = $finfo->buffer($imgData); // Detecta o tipo MIME
                        ?>
                        <?php if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/jfif'])): ?>
                            <img src="data:<?= $mimeType ?>;base64,<?= base64_encode($imgData) ?>" alt="Foto do pet" style="width: 150px;">
                        <?php else: ?>
                            <p>Formato de imagem não suportado.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Imagem não disponível.</p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Você ainda não cadastrou nenhum pet.</p>
    <?php endif; ?>
    <a href="pages/logout.php">Sair</a>
</body>
</html>
