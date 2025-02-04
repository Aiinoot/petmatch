<?php
session_start();
include_once 'conexao.php';
include 'partials/menu.php'; // Inclui o menu no topo

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: pages/login.php");
    exit;
}

// Verifica se o usuário tem pets cadastrados
$sqlVerificaPets = "SELECT COUNT(*) AS total FROM pets WHERE usuario_id = :usuario_id";
$stmtVerificaPets = $conn->prepare($sqlVerificaPets);
$stmtVerificaPets->bindParam(':usuario_id', $_SESSION['usuario_id']);
$stmtVerificaPets->execute();
$temPets = $stmtVerificaPets->fetch(PDO::FETCH_ASSOC)['total'] > 0;

if (!$temPets) {
    echo "<p>Você ainda não cadastrou nenhum pet. <a href='meus_pets.php'>Cadastre aqui</a> para começar a dar matches!</p>";
    exit;
}

// Busca os pets de outros usuários
$sqlOutrosPets = "SELECT * FROM pets WHERE usuario_id != :usuario_id";
$stmtOutrosPets = $conn->prepare($sqlOutrosPets);
$stmtOutrosPets->bindParam(':usuario_id', $_SESSION['usuario_id']);
$stmtOutrosPets->execute();
$outrosPets = $stmtOutrosPets->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Pets</title>
</head>
<body>
    <h1>Buscar Pets para Match</h1>
    <?php if (!empty($outrosPets)): ?>
        <ul>
            <?php foreach ($outrosPets as $pet): ?>
                <li>
                    <strong><?= htmlspecialchars($pet['nome']) ?></strong> - <?= htmlspecialchars($pet['raca']) ?> (<?= htmlspecialchars($pet['idade']) ?> anos)
                    <p><?= htmlspecialchars($pet['descricao']) ?></p>
                    <?php if (!empty($pet['foto'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode(stream_get_contents($pet['foto'])) ?>" alt="Foto do pet" style="width: 150px;">
                    <?php endif; ?>
                    <form method="POST" action="pages/match.php">
                        <input type="hidden" name="pet1_id" value="<?= $pet['id'] ?>">
                        <button type="submit" name="acao" value="like">Curtir</button>
                        <button type="submit" name="acao" value="pass">Ignorar</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Não há pets disponíveis para match no momento. Tente novamente mais tarde.</p>
    <?php endif; ?>
</body>
</html>
