<?php
$tries = 10;
do {
    $mysqli = @new mysqli("db", "user", "password", "testdb");
    if ($mysqli->connect_errno) {
        sleep(2);
    }
} while ($mysqli->connect_errno && --$tries > 0);

if ($mysqli->connect_errno) {
    die("Échec de connexion à MariaDB: " . $mysqli->connect_error);
}

// Ajouter un utilisateur
if (isset($_POST['name'], $_POST['email']) && !isset($_POST['post_title'])) {
    $name = $mysqli->real_escape_string($_POST['name']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $mysqli->query("INSERT INTO users (name, email) VALUES ('$name', '$email')");
}

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM users WHERE id=$id");
}

// Ajouter un post
if (isset($_POST['post_title'], $_POST['post_content'], $_POST['post_user'])) {
    $title = $mysqli->real_escape_string($_POST['post_title']);
    $content = $mysqli->real_escape_string($_POST['post_content']);
    $user_id = intval($_POST['post_user']);
    $mysqli->query("INSERT INTO posts (title, content, user_id) VALUES ('$title', '$content', $user_id)");
}

// Supprimer un post
if (isset($_GET['delete_post'])) {
    $id = intval($_GET['delete_post']);
    $mysqli->query("DELETE FROM posts WHERE id=$id");
}

// Récupérer tous les utilisateurs
$result = $mysqli->query("SELECT * FROM users");

// Récupérer tous les posts avec le nom de l'auteur
$posts_result = $mysqli->query("SELECT posts.id, posts.title, posts.content, users.name AS author FROM posts JOIN users ON posts.user_id = users.id");
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

<h2>Ajouter un post</h2>
<form method="post">
    Titre: <input type="text" name="post_title" required>
    Contenu: <input type="text" name="post_content" required>
    Utilisateur:
    <select name="post_user" required>
        <?php
        $users = $mysqli->query("SELECT id, name FROM users");
        while ($u = $users->fetch_assoc()):
        ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Ajouter le post</button>
</form>

<h2>Liste des posts</h2>
<table border="1">
    <tr><th>ID</th><th>Titre</th><th>Contenu</th><th>Auteur</th><th>Action</th></tr>
    <?php while ($post = $posts_result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($post['id']) ?></td>
        <td><?= htmlspecialchars($post['title']) ?></td>
        <td><?= htmlspecialchars($post['content']) ?></td>
        <td><?= htmlspecialchars($post['author']) ?></td>
        <td>
            <a href="?delete_post=<?= $post['id'] ?>" onclick="return confirm('Supprimer ce post ?')">Supprimer</a>
        </td>
    </tr>
    <?php endwhile; ?>