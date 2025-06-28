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

do {
    $mysqli2 = @new mysqli("db2", "user2", "password2", "testdb2");
    if ($mysqli2->connect_errno) {
        sleep(2);
    }
} while ($mysqli2->connect_errno && --$tries > 0);

if ($mysqli2->connect_errno) {
    die("Échec de connexion à MariaDB: " . $mysqli2->connect_error);
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

if (isset($_POST['admin_name'], $_POST['admin_email'])) {
    $admin_name = $mysqli2->real_escape_string($_POST['admin_name']);
    $admin_email = $mysqli2->real_escape_string($_POST['admin_email']);
    $mysqli2->query("INSERT INTO admins (name, email) VALUES ('$admin_name', '$admin_email')");
}

// Supprimer un admin
if (isset($_GET['delete_admin'])) {
    $id = intval($_GET['delete_admin']);
    $mysqli2->query("DELETE FROM admins WHERE id=$id");
}

// Ajouter un commentaire
if (isset($_POST['comment_content'], $_POST['comment_admin'])) {
    $content = $mysqli2->real_escape_string($_POST['comment_content']);
    $admin_id = intval($_POST['comment_admin']);
    $mysqli2->query("INSERT INTO comments (content, admins_id) VALUES ('$content', $admin_id)");
}

// Supprimer un commentaire
if (isset($_GET['delete_comment'])) {
    $id = intval($_GET['delete_comment']);
    $mysqli2->query("DELETE FROM comments WHERE id=$id");
}
// Récupérer tous les admins
$admins_result = $mysqli2->query("SELECT * FROM admins");

// Récupérer tous les commentaires avec le nom de l'admin
$comments_result = $mysqli2->query("SELECT comments.id, comments.content, admins.name AS admin FROM comments JOIN admins ON comments.admins_id = admins.id");


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
</table>

<h2>Ajouter un admin (DB2)</h2>
<form method="post">
    Nom: <input type="text" name="admin_name" required>
    Email: <input type="email" name="admin_email" required>
    <button type="submit">Ajouter</button>
</form>

<h2>Liste des admins (DB2)</h2>
<table border="1">
    <tr><th>ID</th><th>Nom</th><th>Email</th><th>Action</th></tr>
    <?php while ($admin = $admins_result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($admin['id']) ?></td>
        <td><?= htmlspecialchars($admin['name']) ?></td>
        <td><?= htmlspecialchars($admin['email']) ?></td>
        <td>
            <a href="?delete_admin=<?= $admin['id'] ?>" onclick="return confirm('Supprimer cet admin ?')">Supprimer</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<h2>Ajouter un commentaire (DB2)</h2>
<form method="post">
    Contenu: <input type="text" name="comment_content" required>
    Admin:
    <select name="comment_admin" required>
        <?php
        $admins = $mysqli2->query("SELECT id, name FROM admins");
        while ($a = $admins->fetch_assoc()):
        ?>
            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Ajouter le commentaire</button>
</form>

<h2>Liste des commentaires (DB2)</h2>
<table border="1">
    <tr><th>ID</th><th>Contenu</th><th>Admin</th><th>Action</th></tr>
    <?php while ($comment = $comments_result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($comment['id']) ?></td>
        <td><?= htmlspecialchars($comment['content']) ?></td>
        <td><?= htmlspecialchars($comment['admin']) ?></td>
        <td>
            <a href="?delete_comment=<?= $comment['id'] ?>" onclick="return confirm('Supprimer ce commentaire ?')">Supprimer</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>