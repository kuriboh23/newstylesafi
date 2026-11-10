<?php

declare(strict_types=1);

/**
 * Edit one category. Its name, address, icon and visibility drive every menu
 * of the storefront, so the owner can reorganise the shop without code.
 *
 * @var array<string, mixed>   $category
 * @var array<string, string>  $errors
 * @var array<string, string>  $icons
 */

$error = static fn (string $key): string => isset($errors[$key]) ? ' is-invalid' : '';
$isActive = (string) ($category['is_active'] ?? '1') === '1';
?>
<?php if ($errors !== []): ?>
    <p class="admin-flash admin-flash--error" role="alert">Merci de corriger les champs signalés.</p>
<?php endif; ?>

<form
    class="admin-form"
    method="POST"
    action="<?= e(admin_url('categories/' . $category['id'] . '/modifier')) ?>"
    novalidate
>
    <?= csrf_field() ?>

    <section class="admin-card">
        <h2 class="admin-card-title">Catégorie</h2>

        <div class="field-grid">
            <label class="field">
                <span class="field-label">Nom affiché</span>
                <input class="input<?= $error('name') ?>" type="text" name="name" value="<?= e($category['name']) ?>" maxlength="100" required>
                <?php if (isset($errors['name'])): ?><span class="field-error"><?= e($errors['name']) ?></span><?php endif; ?>
            </label>

            <label class="field">
                <span class="field-label">Adresse (slug)</span>
                <input class="input<?= $error('slug') ?>" type="text" name="slug" value="<?= e($category['slug']) ?>" maxlength="120">
                <span class="field-hint">Lien : /categorie/<?= e($category['slug']) ?>. Modifier l’adresse crée un nouveau lien.</span>
                <?php if (isset($errors['slug'])): ?><span class="field-error"><?= e($errors['slug']) ?></span><?php endif; ?>
            </label>

            <div class="field">
                <span class="field-label">Icône</span>
                <?php partial('partials/icon-picker', [
                    'pickerId'    => 'editCategoryIcon',
                    'pickerName'  => 'icon',
                    'pickerValue' => (string) ($category['icon'] ?? ''),
                    'pickerIcons' => $icons,
                ]); ?>
                <span class="field-hint">Affichée dans le menu latéral et dans les raccourcis de l’accueil.</span>
            </div>

            <label class="field">
                <span class="field-label">Ordre d’affichage</span>
                <input class="input" type="number" name="sort_order" value="<?= max(1, (int) ($category['sort_order'] ?? 1)) ?>" min="1" step="1">
                <span class="field-hint">Les petits nombres apparaissent en premier.</span>
            </label>
        </div>

        <label class="admin-check">
            <input type="checkbox" name="is_active" value="1"<?= $isActive ? ' checked' : '' ?>>
            <span>Catégorie visible sur la boutique</span>
        </label>
    </section>

    <div class="admin-form-actions">
        <a class="btn btn--ghost" href="<?= e(admin_url('categories')) ?>">Annuler</a>
        <button class="btn btn--dark" type="submit">
            <?= icon('check-circle', 'icon icon--sm') ?>
            Enregistrer
        </button>
    </div>
</form>
