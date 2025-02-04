<?php
session_start();
include_once '../conexao.php';
include '../partials/menu.php'; // Inclui o menu no topo

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: pages/login.php");
    exit;
}

// Busca os pets cadastrados pelo usuário logado
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
    <title>Meus Pets</title>
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
    <h1>Meus Pets</h1>
    <p>Bem-vindo(a) à sua lista de pets cadastrados!</p>
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
        <p>Você ainda não cadastrou nenhum pet. <a href="cadastro_pet.php">Clique aqui para cadastrar</a>.</p>
    <?php endif; ?>
</body>
</html>
