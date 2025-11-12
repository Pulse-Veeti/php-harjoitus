<?php
$flash = getFlashMessage();

if ($flash): ?>
<div class="alert alert-<?php echo $flash['type'];?>">
    <?php echo htmlspecialchars($flash['message']);?>
</div>
<?php endif; ?>