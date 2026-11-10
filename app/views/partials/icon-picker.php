<?php

declare(strict_types=1);

/**
 * Icon picker used by the category forms.
 *
 * The native select stays usable without JavaScript. The custom menu is
 * revealed by app.js and shows the real SVG next to every icon name.
 *
 * @var array<string, string> $pickerIcons
 * @var string                $pickerName
 * @var string                $pickerValue
 * @var string                $pickerId
 */

$pickerName  = $pickerName ?? 'icon';
$pickerValue = (string) ($pickerValue ?? '');
$pickerId    = $pickerId ?? 'categoryIcon';
$pickerIcons = is_array($pickerIcons ?? null) ? $pickerIcons : [];

$pickerLabel = $pickerValue !== '' && isset($pickerIcons[$pickerValue])
    ? $pickerIcons[$pickerValue]
    : 'Aucune icône';
$pickerIcon = $pickerValue !== '' && isset($pickerIcons[$pickerValue])
    ? $pickerValue
    : 'minus';
?>
<div class="icon-select" data-icon-select>
    <select
        class="input"
        id="<?= e($pickerId) ?>"
        name="<?= e($pickerName) ?>"
        data-icon-native
    >
        <option value="">Aucune icône</option>
        <?php foreach ($pickerIcons as $value => $label): ?>
            <option value="<?= e($value) ?>"<?= $pickerValue === (string) $value ? ' selected' : '' ?>>
                <?= e($label) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="icon-select-ui" data-icon-ui hidden>
        <button
            type="button"
            class="icon-select-toggle"
            data-icon-toggle
            aria-haspopup="listbox"
            aria-expanded="false"
            aria-label="Choisir une icône"
        >
            <span class="icon-select-preview<?= $pickerValue === '' ? ' is-empty' : '' ?>" data-icon-preview>
                <?= icon($pickerIcon, 'icon icon--sm') ?>
            </span>
            <span class="icon-select-value" data-icon-value><?= e($pickerLabel) ?></span>
            <?= icon('chevron-down', 'icon icon--sm icon-select-chevron') ?>
        </button>

        <div
            class="icon-select-menu"
            data-icon-menu
            role="listbox"
            aria-label="Icônes disponibles"
            hidden
        >
            <button
                type="button"
                class="icon-select-option<?= $pickerValue === '' ? ' is-selected' : '' ?>"
                data-icon-option
                data-value=""
                data-label="Aucune icône"
                role="option"
                aria-selected="<?= $pickerValue === '' ? 'true' : 'false' ?>"
            >
                <span class="icon-select-option-icon is-empty"><?= icon('minus', 'icon icon--sm') ?></span>
                <span>Aucune icône</span>
            </button>

            <?php foreach ($pickerIcons as $value => $label): ?>
                <button
                    type="button"
                    class="icon-select-option<?= $pickerValue === (string) $value ? ' is-selected' : '' ?>"
                    data-icon-option
                    data-value="<?= e($value) ?>"
                    data-label="<?= e($label) ?>"
                    role="option"
                    aria-selected="<?= $pickerValue === (string) $value ? 'true' : 'false' ?>"
                >
                    <span class="icon-select-option-icon"><?= icon((string) $value, 'icon icon--sm') ?></span>
                    <span><?= e($label) ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>
