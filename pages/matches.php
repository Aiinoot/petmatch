<?php include '../partials/menu.php'; ?>

<h1>Seus Matches</h1>
<?php
$sqlMatches = "SELECT p1.nome AS pet1_nome, p2.nome AS pet2_nome
               FROM matches
               JOIN pets p1 ON matches.pet1_id = p1.id
               JOIN pets p2 ON matches.pet2_id = p2.id
               WHERE p1.usuario_id = :usuario_id AND matches.status = 'aceito'";
$stmtMatches = $conn->prepare($sqlMatches);
$stmtMatches->bindParam(':usuario_id', $_SESSION['usuario_id']);
$stmtMatches->execute();
$matches = $stmtMatches->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (!empty($matches)): ?>
    <ul>
        <?php foreach ($matches as $match): ?>
            <li>
                <?= htmlspecialchars($match['pet1_nome']) ?> deu match com <?= htmlspecialchars($match['pet2_nome']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Você ainda não tem nenhum match.</p>
<?php endif; ?>
