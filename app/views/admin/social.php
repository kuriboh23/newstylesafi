<?php

declare(strict_types=1);

/**
 * Admin social accounts.
 *
 * The accounts listed here are rendered as icons in the storefront footer.
 * Instagram from config.php is the starting entry until the owner saves.
 *
 * @var array<int, array{id: string, platform: string, url: string, label: string}> $links
 * @var array<string, string> $platforms
 * @var int $maxLinks
 */
?>
<section class="admin-card">
    <h2 class="admin-card-title">Ajouter un réseau</h2>
    <p class="admin-card-hint">
        Jusqu’à <?= (int) $maxLinks ?> comptes dans le pied de page.
        L’adresse doit être complète, par exemple https://www.instagram.com/votre-compte.
    </p>

    <form class="admin-inline-form" method="POST" action="<?= e(admin_url('reseaux')) ?>">
        <?= csrf_field() ?>

        <label class="field field--small">
            <span class="field-label">Réseau</span>
            <select class="input" name="platform" required>
                <?php foreach ($platforms as $value => $label): ?>
                    <option value="<?= e($value) ?>"><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="field">
            <span class="field-label">Adresse complète</span>
            <input
                class="input"
                type="url"
                name="url"
                maxlength="300"
                placeholder="https://www.facebook.com/newstylesafi"
                required
            >
        </label>

        <label class="field">
            <span class="field-label">Nom affiché (optionnel)</span>
            <input
                class="input"
                type="text"
                name="label"
                maxlength="40"
                placeholder="Ex. Newstylesafi"
            >
        </label>

        <button class="btn btn--dark" type="submit">
            <?= icon('plus', 'icon icon--sm') ?>
            Ajouter
        </button>
    </form>
</section>

<section class="admin-section">
    <div class="admin-section-head">
        <h2 class="admin-section-title">
            <?= count($links) ?> <?= count($links) === 1 ? 'réseau affiché' : 'réseaux affichés' ?>
        </h2>
    </div>

    <?php if ($links === []): ?>
        <div class="admin-empty-state">
            <?= icon('instagram', 'icon') ?>
            <p>Aucun réseau pour le moment. Le pied de page n’affiche aucune icône sociale.</p>
        </div>
    <?php else: ?>
        <div class="admin-list">
            <?php foreach ($links as $link): ?>
                <article class="admin-row admin-row--category">
                    <span class="admin-cat-icon"><?= icon($link['platform'], 'icon icon--sm') ?></span>

                    <div class="admin-row-main">
                        <p class="admin-row-title">
                            <?= e($link['label'] !== '' ? $link['label'] : $platforms[$link['platform']]) ?>
                            <span class="admin-tag"><?= e($platforms[$link['platform']]) ?></span>
                        </p>
                        <p class="admin-row-meta"><?= e($link['url']) ?></p>
                    </div>

                    <div class="admin-row-actions">
                        <a
                            class="admin-icon-btn"
                            href="<?= e($link['url']) ?>"
                            target="_blank"
                            rel="noopener"
                            aria-label="Ouvrir <?= e($platforms[$link['platform']]) ?>"
                            title="Ouvrir"
                        ><?= icon('eye', 'icon icon--sm') ?></a>

                        <form
                            method="POST"
                            action="<?= e(admin_url('reseaux/' . $link['id'] . '/supprimer')) ?>"
                            data-confirm="Retirer ce réseau du pied de page ?"
                        >
                            <?= csrf_field() ?>
                            <button
                                type="submit"
                                class="admin-icon-btn admin-icon-btn--danger"
                                aria-label="Supprimer <?= e($platforms[$link['platform']]) ?>"
                                title="Supprimer"
                            ><?= icon('trash', 'icon icon--sm') ?></button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
