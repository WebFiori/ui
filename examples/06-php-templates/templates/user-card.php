<div class="user-card">
    <h4><?= htmlspecialchars($name) ?></h4>
    <p><?= htmlspecialchars($email) ?></p>
<?php if ($isAdmin): ?>
    <span class="badge">Admin</span>
<?php endif; ?>
</div>
