<?php

declare(strict_types=1);

/**
 * ==========================================================================
 *  SHOP SETTINGS - THIS IS THE ONLY FILE YOU NEED TO EDIT
 * ==========================================================================
 *
 *  1. Open this file.
 *  2. Change the text between the quotes. Keep the quotes and the comma at
 *     the end of each line.
 *  3. Save and reload the site. That is all: no build step, no cache to
 *     clear, nothing to install.
 *
 *  A. THE SHOP      name, currency, WhatsApp number, languages
 *  B. THE ADMIN     secret URL and login of the admin area
 *  C. DATABASE      the four values your hosting panel gives you
 *  D. HOME PAGE     announcement bar, slider, trust badges, promo block
 *
 *  There is no other configuration file: no .env, no .htaccess, no install
 *  step. Upload the files, fill in section C below, open the site.
 */

return [

    // ======================================================================
    // A. THE SHOP
    // ======================================================================

    // Name used in page titles, the footer and WhatsApp messages.
    'app_name' => 'Newstylesafi',

    // The wordmark is split after this prefix: "New" + "stylesafi".
    // The second part is drawn in the accent colour. Leave empty to split
    // on the first space of the shop name instead.
    'brand_split' => 'New',

    // "production" hides errors from visitors. Leave it as it is.
    'app_env' => 'production',

    // Show PHP errors on screen instead of a blank 500 page.
    // Set this to true ONLY while installing (it reveals code paths),
    // then back to false once the shop works.
    'app_debug' => false,

    // Pretty URLs: /produit/chemise instead of /?p=/produit/chemise.
    // They work thanks to the .htaccess file next to this one, which simply
    // forwards every page to index.php. Keep it true.
    // If your host shows a 404 on every page (it ignores .htaccess), or if
    // you delete .htaccess, links fall back to /?p=/panier on their own and
    // the shop keeps working. Set this to false to force that shorter form.
    'pretty_urls' => true,

    // Language of the interface and timezone used for dates.
    'locale'   => 'fr',
    'timezone' => 'Africa/Casablanca',

    // Price suffix shown everywhere ("DH 249").
    'currency' => 'DH',

    // Order total above which delivery becomes free, in DH.
    // 0 disables free delivery: the fee below then always applies.
    'free_shipping_threshold' => 500,

    // WhatsApp number that receives the orders.
    // Country code first, digits only, no "+" and no spaces.
    //
    // !! PLACEHOLDER !! 212600000000 is a stand-in, not a real line.
    // Replace it with the confirmed Newstylesafi order number before the
    // site goes live.
    'whatsapp_number' => '212600000000',

    // Instagram link in the footer (empty string hides it).
    'instagram_url' => 'https://www.instagram.com/newstylesafi/',


    // ======================================================================
    // B. THE ADMIN AREA
    // ======================================================================

    // Secret folder of the admin area: the dashboard is at /gestion
    // Change it to something only you know, e.g. 'gestion-8f2'.
    'admin_path' => 'gestion',

    // Login of the admin area. Change the password before going live.
    'admin_email'    => 'admin@newstylesafi.ma',
    'admin_password' => 'newstylesafi2026',

    // Random text used to sign cookies. Any long random string works.
    'cookie_secret' => 'change-this-to-a-long-random-string',


    // ======================================================================
    // C. DATABASE (MySQL)
    // ======================================================================
    //
    // Local XAMPP:        host 127.0.0.1 | user root  | password empty
    // InfinityFree:       copy the four values from the panel:
    //                     "MySQL Hostname"  -> db_host   (sql123.infinityfree.com)
    //                     "Database Name"   -> db_name   (if0_12345678_newstylesafi)
    //                     "Username"        -> db_user   (if0_12345678)
    //                     account password  -> db_pass
    // Keep the database name and the username exactly as the panel shows
    // them, prefix included (if0_ with a zero, not the letter o).
    //
    // If you kept the previous database and imported the new dump into it,
    // set db_name to that database name instead.

    'db_host'    => '127.0.0.1',
    'db_port'    => 3306,
    'db_name'    => 'newstylesafi',
    'db_user'    => 'root',
    'db_pass'    => '',
    'db_charset' => 'utf8mb4',


    // ======================================================================
    // D. HOME PAGE TEXT
    // ======================================================================

    // Scrolling bar at the very top of every page.
    'announcement' => [
        'highlight' => 'Nouvelle collection homme',
        'text'      => 'Commande simple sur WhatsApp',
        'location'  => 'Safi, Maroc',
        'messages'  => [
            'Nouvelle collection homme disponible',
            'T-shirts, chemises, pantalons et vestes',
            'Commande simple et directe sur WhatsApp',
            'Nouvelles pièces ajoutées régulièrement',
            'Boutique officielle Newstylesafi — Safi, Maroc',
        ],
    ],

    // Big slider on the home page. Each block is one slide:
    // eyebrow = small text above, title = big text, text = sentence below,
    // cta = button label, link = where it goes, theme = light or dark.
    'hero_slides' => [
        [
            'eyebrow' => 'Newstylesafi',
            'title'   => 'La nouvelle collection',
            'text'    => 'Des pièces masculines choisies pour un vestiaire net et actuel.',
            'cta'     => 'Découvrir',
            'link'    => '/produits',
            'theme'   => 'dark',
        ],
        [
            'eyebrow' => 'Les essentiels',
            'title'   => 'T-shirts & chemises',
            'text'    => 'Des coupes propres et des matières agréables, au quotidien.',
            'cta'     => 'Voir la sélection',
            'link'    => '/categorie/chemises',
            'theme'   => 'dark',
        ],
        [
            'eyebrow' => 'Safi, Maroc',
            'title'   => 'Commander sur WhatsApp',
            'text'    => 'Choisissez vos tailles et vos couleurs, puis envoyez votre commande.',
            'cta'     => 'Voir la boutique',
            'link'    => '/produits',
            'theme'   => 'light',
        ],
    ],

    // Four trust badges under the slider.
    'why_us' => [
        ['icon' => 'shield',   'title' => 'Sélection premium',     'text' => 'Des pièces masculines choisies avec soin.'],
        ['icon' => 'tag',      'title' => 'Tailles & couleurs',    'text' => 'Chaque fiche indique les options disponibles.'],
        ['icon' => 'whatsapp', 'title' => 'Commande sur WhatsApp', 'text' => 'Votre commande part directement vers la boutique.'],
        ['icon' => 'users',    'title' => 'Contact direct',        'text' => 'La boutique confirme la disponibilité avec vous.'],
    ],

    // Promo banner near the bottom of the home page.
    // Keep the wording factual: only promise what the catalogue offers.
    'promo_banner' => [
        'badge' => 'Nouveautés',
        'title' => 'La collection est en ligne',
        'text'  => 'Parcourez les pièces disponibles et retrouvez tous les détails sur chaque fiche produit.',
        'cta'   => 'Voir la boutique',
        'link'  => '/produits',
    ],
];
