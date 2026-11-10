<?php

declare(strict_types=1);

/**
 * Generates the demo (SVG) images of the catalogue.
 *
 *   php tools/make-placeholders.php
 *
 * Every product gets two minimalist images in the brand colours, to be
 * replaced later by real photos from the admin area.
 */

$products = [
    ['slug' => 't-shirt-coton-essentiel', 'word' => 'Essentiel'],
    ['slug' => 't-shirt-oversize-atlas', 'word' => 'Atlas'],
    ['slug' => 'chemise-oxford-classic', 'word' => 'Classic'],
    ['slug' => 'chemise-lin-safi', 'word' => 'Safi'],
    ['slug' => 'pantalon-chino-medina', 'word' => 'Medina'],
    ['slug' => 'pantalon-cargo-nomade', 'word' => 'Nomade'],
    ['slug' => 'veste-jean-indigo', 'word' => 'Indigo'],
    ['slug' => 'blouson-nuit', 'word' => 'Nuit'],
    ['slug' => 'ensemble-chemise-pantalon-signature', 'word' => 'Signature'],
    ['slug' => 'ensemble-polo-short-ete', 'word' => 'Été'],
    ['slug' => 'casquette-newstylesafi', 'word' => 'Casquette'],
    ['slug' => 'ceinture-cuir-safi', 'word' => 'Ceinture'],
];

$target = dirname(__DIR__) . '/assets/images/placeholders';

if (!is_dir($target) && !mkdir($target, 0775, true) && !is_dir($target)) {
    fwrite(STDERR, "Cannot create {$target}\n");
    exit(1);
}

/**
 * @param array{slug: string, word: string} $product
 */
function placeholder(array $product, int $variant): string
{
    $word    = mb_strtoupper($product['word'], 'UTF-8');
    $initial = mb_substr($word, 0, 1, 'UTF-8');
    $label   = htmlspecialchars($word, ENT_QUOTES, 'UTF-8');
    $alt     = htmlspecialchars('Visuel de démonstration — ' . $product['word'], ENT_QUOTES, 'UTF-8');

    // Two different compositions for the product gallery.
    if ($variant === 1) {
        $glow = '<circle cx="640" cy="190" r="210" fill="#FFD400" opacity="0.10"/>'
            . '<circle cx="120" cy="880" r="170" fill="#FFD400" opacity="0.06"/>';
        $frame = '<rect x="56" y="56" width="688" height="888" rx="4" fill="none" stroke="#292929"/>'
            . '<line x1="56" y1="770" x2="744" y2="770" stroke="#292929"/>';
        $nameY = 650;
    } else {
        $glow = '<circle cx="150" cy="220" r="180" fill="#FFD400" opacity="0.07"/>'
            . '<circle cx="690" cy="810" r="220" fill="#FFD400" opacity="0.10"/>';
        $frame = '<rect x="56" y="56" width="688" height="888" rx="4" fill="none" stroke="#292929"/>'
            . '<line x1="56" y1="230" x2="744" y2="230" stroke="#292929"/>';
        $nameY = 680;
    }

    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1000" role="img" aria-label="{$alt}">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#0F0F0F"/>
      <stop offset="1" stop-color="#080808"/>
    </linearGradient>
  </defs>
  <rect width="800" height="1000" fill="url(#bg)"/>
  {$glow}
  {$frame}
  <text x="400" y="165" text-anchor="middle" font-family="system-ui, -apple-system, 'Segoe UI', sans-serif"
        font-size="22" font-weight="800" letter-spacing="8" fill="#FFFFFF">NEW<tspan fill="#FFD400">STYLESAFI</tspan></text>
  <text x="400" y="470" text-anchor="middle" font-family="system-ui, -apple-system, 'Segoe UI', sans-serif"
        font-size="190" font-weight="800" fill="#FFD400">{$initial}</text>
  <text x="400" y="{$nameY}" text-anchor="middle" font-family="system-ui, -apple-system, 'Segoe UI', sans-serif"
        font-size="30" font-weight="700" letter-spacing="10" fill="#FFFFFF">{$label}</text>
  <line x1="330" y1="{$nameY}" x2="470" y2="{$nameY}" stroke="#FFD400" stroke-width="2" transform="translate(0 40)"/>
  <text x="400" y="860" text-anchor="middle" font-family="system-ui, -apple-system, 'Segoe UI', sans-serif"
        font-size="16" letter-spacing="6" fill="#BDBDBD">PHOTO À VENIR</text>
</svg>

SVG;
}

$count = 0;

foreach ($products as $product) {
    foreach ([1, 2] as $variant) {
        $suffix = $variant === 1 ? '' : '-2';
        $file   = $target . '/' . $product['slug'] . $suffix . '.svg';

        if (file_put_contents($file, placeholder($product, $variant)) === false) {
            fwrite(STDERR, "Failed: {$file}\n");
            exit(1);
        }

        $count++;
    }
}

echo "{$count} images written to assets/images/placeholders/\n";
