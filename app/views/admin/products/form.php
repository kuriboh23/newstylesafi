<?php

declare(strict_types=1);

/**
 * Admin product create/edit form.
 *
 * @var array<string, mixed> $product
 * @var array<string, string> $errors
 * @var string $mode
 * @var array<int, array<string, mixed>> $categories
 * @var array<int, array<string, mixed>> $images
 */

$isEdit  = $mode === 'edit';
$action  = $isEdit ? admin_url('produits/' . $product['id']) : admin_url('produits/nouveau');
$current = (int) ($product['category_id'] ?? 0);

$sizesValue = (string) ($product['sizes'] ?? '');
$decoded    = json_decode($sizesValue, true);

if (is_array($decoded)) {
    $sizesValue = implode(', ', array_map('strval', $decoded));
}

$colorsValue = (string) ($product['colors'] ?? '');
$decoded     = json_decode($colorsValue, true);
$colorItems  = [];

if (is_array($decoded)) {
    $lines = [];

    foreach ($decoded as $color) {
        if (!is_array($color) || empty($color['name'])) {
            continue;
        }

        $hex          = (string) ($color['hex'] ?? '#CCCCCC');
        $hex          = preg_match('/^#[0-9a-fA-F]{6}$/', $hex) === 1 ? $hex : '#CCCCCC';
        $colorItems[] = ['name' => (string) $color['name'], 'hex' => $hex];
        $lines[]      = $color['name'] . ':' . $hex;
    }

    $colorsValue = implode(', ', $lines);
}

// Re-rendered form (validation error): values come back as the posted
// "Nom:#hex" list rather than JSON, so rebuild the rows from that list.
if ($colorItems === [] && $colorsValue !== '') {
    foreach (preg_split('/[,;\n]+/', $colorsValue) ?: [] as $part) {
        $part = trim($part);

        if ($part === '') {
            continue;
        }

        $name = $part;
        $hex  = '#CCCCCC';

        if (str_contains($part, ':')) {
            [$name, $hex] = array_map('trim', explode(':', $part, 2));
        }

        $hex = preg_match('/^#?[0-9a-fA-F]{6}$/', $hex) === 1 ? '#' . ltrim($hex, '#') : '#CCCCCC';

        if ($name === '') {
            continue;
        }

        $colorItems[] = ['name' => $name, 'hex' => $hex];
    }
}

$gallery = is_array($images ?? null) ? $images : [];
$error   = static fn (string $key): string => isset($errors[$key]) ? ' is-invalid' : '';

$currentCategory = null;

foreach ($categories as $category) {
    if ((int) $category['id'] === $current) {
        $currentCategory = $category;
        break;
    }
}

$currentCategoryName = $currentCategory !== null ? (string) $currentCategory['name'] : 'Sans catégorie';
$currentCategoryIcon = $currentCategory !== null && trim((string) ($currentCategory['icon'] ?? '')) !== ''
    ? (string) $currentCategory['icon']
    : 'tag';
?>
<?php if ($errors !== []): ?>
    <p class="admin-flash admin-flash--error" role="alert">
        Merci de corriger les champs signalés.
    </p>
<?php endif; ?>

<form class="admin-form" method="POST" action="<?= e($action) ?>" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>

    <section class="admin-card">
        <h2 class="admin-card-title">Informations</h2>

        <div class="field-grid">
            <label class="field field--wide">
                <span class="field-label">Titre *</span>
                <input
                    class="input<?= $error('title') ?>"
                    type="text"
                    name="title"
                    value="<?= e($product['title'] ?? '') ?>"
                    maxlength="255"
                    required
                >
                <?php if (isset($errors['title'])): ?><span class="field-error"><?= e($errors['title']) ?></span><?php endif; ?>
            </label>

            <div class="field category-field">
                <span class="field-label" id="categoryPickerLabel">Catégorie</span>

                <select class="input<?= $error('category_id') ?>" name="category_id" data-category-native>
                    <option value="0">Sans catégorie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>"<?= $current === (int) $category['id'] ? ' selected' : '' ?>>
                            <?= e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="category-picker" data-category-picker hidden>
                    <button
                        type="button"
                        class="category-picker-toggle<?= $error('category_id') ?>"
                        data-category-toggle
                        aria-labelledby="categoryPickerLabel"
                        aria-haspopup="listbox"
                        aria-expanded="false"
                    >
                        <span class="category-picker-icon" data-category-icon><?= icon($currentCategoryIcon, 'icon icon--sm') ?></span>
                        <span class="category-picker-value" data-category-value><?= e($currentCategoryName) ?></span>
                        <?= icon('chevron-down', 'icon icon--sm category-picker-chevron') ?>
                    </button>

                    <div class="category-picker-menu" data-category-menu role="listbox" aria-labelledby="categoryPickerLabel" hidden>
                        <button
                            type="button"
                            class="category-option<?= $current === 0 ? ' is-selected' : '' ?>"
                            data-category-option
                            data-value="0"
                            data-label="Sans catégorie"
                            role="option"
                            aria-selected="<?= $current === 0 ? 'true' : 'false' ?>"
                        >
                            <span class="category-option-icon" data-category-option-icon><?= icon('tag', 'icon icon--sm') ?></span>
                            <span>Sans catégorie</span>
                        </button>

                        <?php foreach ($categories as $category): ?>
                            <button
                                type="button"
                                class="category-option<?= $current === (int) $category['id'] ? ' is-selected' : '' ?>"
                                data-category-option
                                data-value="<?= (int) $category['id'] ?>"
                                data-label="<?= e($category['name']) ?>"
                                role="option"
                                aria-selected="<?= $current === (int) $category['id'] ? 'true' : 'false' ?>"
                            >
                                <span class="category-option-icon" data-category-option-icon><?= icon((string) ($category['icon'] ?: 'tag'), 'icon icon--sm') ?></span>
                                <span><?= e($category['name']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (isset($errors['category_id'])): ?><span class="field-error"><?= e($errors['category_id']) ?></span><?php endif; ?>
            </div>

            <label class="field">
                <span class="field-label">Slug (URL)</span>
                <input
                    class="input"
                    type="text"
                    name="slug"
                    value="<?= e($product['slug'] ?? '') ?>"
                    placeholder="auto depuis le titre"
                    maxlength="120"
                >
                <span class="field-hint">Laissez vide pour générer automatiquement.</span>
            </label>

            <label class="field">
                <span class="field-label">Prix (DH) *</span>
                <input
                    class="input<?= $error('price') ?>"
                    type="number"
                    step="0.01"
                    min="0"
                    name="price"
                    value="<?= e($product['price'] ?? '') ?>"
                    inputmode="decimal"
                    required
                >
                <?php if (isset($errors['price'])): ?><span class="field-error"><?= e($errors['price']) ?></span><?php endif; ?>
            </label>

            <label class="field">
                <span class="field-label">Prix promo (DH)</span>
                <input
                    class="input<?= $error('sale_price') ?>"
                    type="number"
                    step="0.01"
                    min="0"
                    name="sale_price"
                    value="<?= e($product['sale_price'] ?? '') ?>"
                    inputmode="decimal"
                >
                <span class="field-hint">Vide = pas de promotion.</span>
                <?php if (isset($errors['sale_price'])): ?><span class="field-error"><?= e($errors['sale_price']) ?></span><?php endif; ?>
            </label>

            <label class="field">
                <span class="field-label">Stock *</span>
                <input
                    class="input<?= $error('stock') ?>"
                    type="number"
                    step="1"
                    min="0"
                    name="stock"
                    value="<?= e($product['stock'] ?? '10') ?>"
                    inputmode="numeric"
                    required
                >
                <?php if (isset($errors['stock'])): ?><span class="field-error"><?= e($errors['stock']) ?></span><?php endif; ?>
            </label>

            <label class="field field--wide">
                <span class="field-label">Description</span>
                <textarea class="input" name="description" rows="4" maxlength="2000"><?= e($product['description'] ?? '') ?></textarea>
            </label>
        </div>
    </section>

    <section class="admin-card">
        <h2 class="admin-card-title">Options</h2>

        <div class="field-grid">
            <label class="field">
                <span class="field-label">Tailles</span>
                <input
                    class="input"
                    type="text"
                    name="sizes"
                    value="<?= e($sizesValue) ?>"
                    placeholder="S, M, L, XL"
                >
                <span class="field-hint">Séparées par des virgules.</span>
            </label>

            <div class="field field--wide">
                <span class="field-label">Couleurs</span>

                <div class="color-editor" data-color-editor>
                    <div class="color-rows" data-color-rows>
                        <?php foreach ($colorItems as $color): ?>
                            <div class="color-row">
                                <input
                                    class="color-row-swatch"
                                    type="color"
                                    value="<?= e($color['hex']) ?>"
                                    data-color-hex
                                    aria-label="Teinte de la couleur <?= e($color['name']) ?>"
                                >
                                <input
                                    class="input color-row-name"
                                    type="text"
                                    value="<?= e($color['name']) ?>"
                                    maxlength="40"
                                    placeholder="Nom (ex. Noir)"
                                    data-color-name
                                    aria-label="Nom de la couleur"
                                >
                                <button
                                    class="admin-icon-btn admin-icon-btn--danger"
                                    type="button"
                                    data-color-remove
                                    aria-label="Supprimer la couleur <?= e($color['name']) ?>"
                                    title="Supprimer"
                                ><?= icon('trash', 'icon icon--sm') ?></button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="color-editor-actions">
                        <button class="btn btn--ghost btn--compact" type="button" data-color-add>
                            <?= icon('plus', 'icon icon--sm') ?>
                            Ajouter une couleur
                        </button>
                        <span class="field-hint">Choisissez le nom, puis cliquez sur la pastille pour la teinte.</span>
                    </div>

                    <input type="hidden" name="colors" value="<?= e($colorsValue) ?>" data-color-value>

                    <template data-color-template>
                        <div class="color-row">
                            <input
                                class="color-row-swatch"
                                type="color"
                                value="#9C4A26"
                                data-color-hex
                                aria-label="Teinte de la couleur"
                            >
                            <input
                                class="input color-row-name"
                                type="text"
                                value=""
                                maxlength="40"
                                placeholder="Nom (ex. Noir)"
                                data-color-name
                                aria-label="Nom de la couleur"
                            >
                            <button
                                class="admin-icon-btn admin-icon-btn--danger"
                                type="button"
                                data-color-remove
                                aria-label="Supprimer cette couleur"
                                title="Supprimer"
                            ><?= icon('trash', 'icon icon--sm') ?></button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-card">
        <h2 class="admin-card-title">Visibilité</h2>

        <div class="admin-checks">
            <label class="admin-check">
                <input type="checkbox" name="is_active" value="1"<?= !empty($product['is_active']) ? ' checked' : '' ?>>
                <span class="admin-check-text">
                    <strong>En ligne</strong>
                    <small>Visible dans la boutique</small>
                </span>
            </label>

            <label class="admin-check">
                <input type="checkbox" name="is_featured" value="1"<?= !empty($product['is_featured']) ? ' checked' : '' ?>>
                <span class="admin-check-text">
                    <strong>Coup de cœur</strong>
                    <small>Badge sur la fiche et priorité d’affichage</small>
                </span>
            </label>

            <label class="admin-check">
                <input type="checkbox" name="is_flash" value="1"<?= !empty($product['is_flash']) ? ' checked' : '' ?>>
                <span class="admin-check-text">
                    <strong>Promo flash</strong>
                    <small>Badge « Promo flash » et compte à rebours</small>
                </span>
            </label>
        </div>
    </section>

    <section class="admin-card">
        <h2 class="admin-card-title">Photos</h2>
        <p class="admin-card-hint">Ajoutez plusieurs photos pour montrer la pièce sous tous les angles. Radio « Principale » = photo affichée sur les cartes et en premier sur la fiche.</p>

        <?php if ($gallery !== []): ?>
            <div class="admin-gallery">
                <?php foreach ($gallery as $image): ?>
                    <div class="admin-gallery-item">
                        <img
                            src="<?= e(product_image_url((int) $product['id'], (string) $image['image_path'])) ?>"
                            alt=""
                            width="140"
                            height="175"
                            loading="lazy"
                        >

                        <label class="admin-gallery-choice">
                            <input
                                type="radio"
                                name="primary_image"
                                value="<?= (int) $image['id'] ?>"
                                <?= !empty($image['is_primary']) ? 'checked' : '' ?>
                            >
                            Principale
                        </label>

                        <label class="admin-gallery-choice admin-gallery-choice--remove">
                            <input type="checkbox" name="remove_images[]" value="<?= (int) $image['id'] ?>">
                            Supprimer
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="admin-empty">Aucune photo pour ce produit.</p>
        <?php endif; ?>

        <div class="admin-image-input">
            <label class="field">
                <span class="field-label">Ajouter des photos</span>
                <input
                    class="input"
                    type="file"
                    name="images[]"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                    data-images-input
                >
            </label>
            <p class="field-hint">JPG, PNG ou WebP — 3 Mo maximum par photo, 8 photos par produit. Les nouvelles photos s’ajoutent à la galerie.</p>
            <div class="admin-gallery-preview" data-images-preview></div>
            <?php if (isset($errors['images'])): ?><span class="field-error"><?= e($errors['images']) ?></span><?php endif; ?>
        </div>
    </section>

    <div class="admin-form-actions">
        <a class="btn btn--ghost" href="<?= e(admin_url('produits')) ?>">Annuler</a>
        <button class="btn btn--dark" type="submit">
            <?= icon('check-circle', 'icon icon--sm') ?>
            <?= $isEdit ? 'Enregistrer' : 'Créer le produit' ?>
        </button>
    </div>
</form>
