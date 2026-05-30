<section class="admin-shell">
    <div class="admin-topbar">
        <div><p class="eyebrow">Acceso</p><h1>Usuarios</h1></div>
    </div>
    <?php require __DIR__ . '/_menu.php'; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?><p class="alert"><?= e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p><?php endif; ?>

    <div class="admin-grid">
        <form class="panel stack" method="post" action="<?= e(url('/admin/users')) ?>">
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
            <div><p class="eyebrow">Nuevo usuario</p><h2>Crear usuario</h2></div>
            <label>Nombre<input type="text" name="name" required></label>
            <label>Email<input type="email" name="email" required></label>
            <label>Password<input type="password" name="password" required></label>
            <label>Rol<select name="role"><option value="admin">Admin</option><option value="editor">Editor</option><option value="author" selected>Autor</option></select></label>
            <div class="panel role-help">
                <strong>Admin</strong>: tiene control total sobre la web.<br>
                <strong>Editor</strong>: crea artículos, edita cualquiera y gestiona categorías.<br>
                <strong>Autor</strong>: crea artículos y edita los suyos.
            </div>
            <button class="button primary" type="submit">Crear usuario</button>
        </form>

        <div class="table-wrap">
            <table class="responsive-table">
                <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Alta</th></tr></thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td data-label="Nombre"><strong><?= e($user['name']) ?></strong></td>
                            <td data-label="Email"><?= e($user['email']) ?></td>
                            <td data-label="Rol"><?= e($user['role']) ?></td>
                            <td data-label="Alta"><?= e(substr((string) $user['created_at'], 0, 10)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$users): ?><tr><td colspan="4">No hay usuarios.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
