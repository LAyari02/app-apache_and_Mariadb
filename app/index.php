<?php
$mysqli = new mysqli("db", "user", "password", "testdb");
if ($mysqli->connect_errno) {
    die("Échec de connexion à MariaDB: " . $mysqli->connect_error);
}

// Ajouter un utilisateur
if (isset($_POST['name'], $_POST['email'])) {
    $name = $mysqli->real_escape_string($_POST['name']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $mysqli->query("INSERT INTO users (name, email) VALUES ('$name', '$email')");
}

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM users WHERE id=$id");
}

// Récupérer tous les utilisateurs
$result = $mysqli->query("SELECT * FROM users");
?>

<h2>Ajouter un utilisateur</h2>
<form method="post">
    Nom: <input type="text" name="name" required>
    Email: <input type="email" name="email" required>
    <button type="submit">Ajouter</button>
</form>

<h2>Liste des utilisateurs</h2>
<table border="1">
    <tr><th>ID</th><th>Nom</th><th>Email</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td>
            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>