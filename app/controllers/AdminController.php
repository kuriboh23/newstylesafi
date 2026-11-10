<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\Category;
use App\Models\Product;
use App\Models\SocialLink;

/**
 * Store admin panel.
 *
 * Single-password access configured through ADMIN_EMAIL / ADMIN_PASSWORD.
 * Everything the owner touches daily is reachable in one tap on a phone:
 * dashboard, products, categories and footer social accounts.
 */
final class AdminController
{
    private const SESSION_KEY = 'admin';

    /**
     * Icons the owner can attach to a category. The value is the icon name
     * understood by app/helpers/icons.php.
     */
    private const CATEGORY_ICONS = [
        'shirt'  => 'Chemise',
        'tshirt' => 'T-shirt',
        'jacket' => 'Veste',
        'pants'  => 'Pantalon',
        'shoe'   => 'Chaussure',
        'tag'    => 'Accessoire',
        'gift'   => 'Cadeau',
        'star'   => 'Étoile',
        'home'   => 'Maison',
        'users'  => 'Collection',
    ];

    /* ------------------------------------------------------------------
       Authentication
       ------------------------------------------------------------------ */

    public function loginForm(): void
    {
        if ($this->isLoggedIn()) {
            redirect($this->basePath());
        }

        View::render('admin/login', [
            'title'     => 'Connexion — Gestion ' . config('app_name'),
            'adminAuth' => true,
            'noindex'   => true,
            'email'     => '',
            'error'     => null,
        ], 'layouts/admin');
    }

    public function login(): void
    {
        Session::start();

        $lockedUntil = (int) Session::get('admin_login_lock', 0);

        if ($lockedUntil > time()) {
            http_response_code(429);
            header('Retry-After: 600');

            View::render('admin/login', [
                'title'     => 'Connexion — Gestion ' . config('app_name'),
                'adminAuth' => true,
                'noindex'   => true,
                'email'     => '',
                'error'     => 'Trop de tentatives. Réessayez dans quelques minutes.',
            ], 'layouts/admin');

            return;
        }

        $email    = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');

        $expectedEmail    = mb_strtolower(trim((string) config('admin_email')));
        $expectedPassword = (string) config('admin_password');

        $valid = csrf_verify($_POST['_csrf'] ?? null)
            && $expectedPassword !== ''
            && hash_equals($expectedEmail, $email)
            && hash_equals($expectedPassword, $password);

        if (!$valid) {
            $attempts = (int) Session::get('admin_login_attempts', 0) + 1;
            Session::set('admin_login_attempts', $attempts);

            if ($attempts >= 5) {
                Session::set('admin_login_lock', time() + 600);
            }

            usleep(300000);

            http_response_code(422);

            View::render('admin/login', [
                'title'     => 'Connexion — Gestion ' . config('app_name'),
                'adminAuth' => true,
                'noindex'   => true,
                'email'     => $email,
                'error'     => 'Email ou mot de passe incorrect.',
            ], 'layouts/admin');

            return;
        }

        Session::remove('admin_login_attempts');
        Session::remove('admin_login_lock');
        session_regenerate_id(true);
        Session::set(self::SESSION_KEY, ['email' => $expectedEmail, 'at' => time()]);

        flash('success', 'Bienvenue dans votre espace de gestion.');
        redirect($this->basePath());
    }

    public function logout(): void
    {
        $this->requireCsrf($this->basePath());

        Session::remove(self::SESSION_KEY);
        Session::start();
        session_regenerate_id(true);

        redirect($this->path('connexion'));
    }

    /* ------------------------------------------------------------------
       Dashboard
       ------------------------------------------------------------------ */

    public function dashboard(): void
    {
        $this->requireAdmin();

        View::render('admin/dashboard', [
            'title'       => 'Tableau de bord — Gestion ' . config('app_name'),
            'adminActive' => 'dashboard',
            'adminTitle'  => 'Tableau de bord',
            'noindex'     => true,
            'stats'       => Product::adminStats(),
            'recent'      => Product::adminAll('', 5),
            'categories'  => Category::adminAll(),
        ], 'layouts/admin');
    }

    /* ------------------------------------------------------------------
       Products
       ------------------------------------------------------------------ */

    public function products(): void
    {
        $this->requireAdmin();

        $search     = mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 60);
        $categories = Category::adminAll();
        $categoryId = max(0, (int) ($_GET['categorie'] ?? 0));

        if ($categoryId > 0 && !in_array($categoryId, array_map(
            static fn (array $category): int => (int) $category['id'],
            $categories
        ), true)) {
            $categoryId = 0;
        }

        View::render('admin/products/index', [
            'title'       => 'Produits — Gestion ' . config('app_name'),
            'adminActive' => 'products',
            'adminTitle'  => 'Produits',
            'noindex'     => true,
            'products'    => Product::adminAll($search, 100, $categoryId),
            'search'      => $search,
            'categories'  => $categories,
            'categoryId'  => $categoryId,
        ], 'layouts/admin');
    }

    public function createForm(): void
    {
        $this->requireAdmin();

        $this->renderForm($this->emptyProduct(), [], 'create');
    }

    public function create(): void
    {
        $this->requireAdmin();
        $this->saveProduct(null);
    }

    public function editForm(string $id): void
    {
        $this->requireAdmin();

        $product = Product::adminFind((int) $id);

        if ($product === null) {
            $this->notFound();

            return;
        }

        $this->renderForm($product, [], 'edit');
    }

    public function update(string $id): void
    {
        $this->requireAdmin();

        $product = Product::adminFind((int) $id);

        if ($product === null) {
            $this->notFound();

            return;
        }

        $this->saveProduct($product);
    }

    public function delete(string $id): void
    {
        $this->requireAdmin();
        $this->requireCsrf($this->path('produits'));

        Product::delete((int) $id);

        flash('success', 'Produit supprimé.');
        redirect($this->path('produits'));
    }

    /**
     * Shared create/update handler.
     *
     * @param array<string, mixed>|null $existing
     */
    private function saveProduct(?array $existing): void
    {
        $id = $existing !== null ? (int) $existing['id'] : null;

        $this->requireCsrf($id !== null ? $this->path('produits/' . $id) : $this->path('produits/nouveau'));

        $post = $_POST;

        $values = [
            'id'           => $id ?? 0,
            'title'        => trim((string) ($post['title'] ?? '')),
            'slug'         => trim((string) ($post['slug'] ?? '')),
            'description'  => trim((string) ($post['description'] ?? '')),
            'price'        => trim((string) ($post['price'] ?? '')),
            'sale_price'   => trim((string) ($post['sale_price'] ?? '')),
            'stock'        => trim((string) ($post['stock'] ?? '10')),
            'sizes'        => trim((string) ($post['sizes'] ?? '')),
            'colors'       => trim((string) ($post['colors'] ?? '')),
            'category_id'  => (int) ($post['category_id'] ?? 0),
            'is_active'    => isset($post['is_active']) ? 1 : 0,
            'is_featured'  => isset($post['is_featured']) ? 1 : 0,
            'is_flash'     => isset($post['is_flash']) ? 1 : 0,
        ];

        $errors = [];

        if (mb_strlen($values['title']) < 2) {
            $errors['title'] = 'Le titre doit contenir au moins 2 caractères.';
        }

        if (!is_numeric($values['price']) || (float) $values['price'] <= 0) {
            $errors['price'] = 'Indiquez un prix supérieur à 0.';
        }

        if ($values['sale_price'] !== '') {
            if (!is_numeric($values['sale_price']) || (float) $values['sale_price'] <= 0) {
                $errors['sale_price'] = 'Prix promo invalide.';
            } elseif (is_numeric($values['price']) && (float) $values['sale_price'] >= (float) $values['price']) {
                $errors['sale_price'] = 'Le prix promo doit être inférieur au prix normal.';
            }
        }

        if (!ctype_digit($values['stock'])) {
            $errors['stock'] = 'Le stock doit être un nombre entier positif.';
        }

        $categoryIds = array_map(
            static fn (array $category): int => (int) $category['id'],
            Category::adminAll()
        );

        if ($values['category_id'] !== 0 && !in_array($values['category_id'], $categoryIds, true)) {
            $errors['category_id'] = 'Catégorie inconnue.';
        }

        // Existing gallery, pictures marked for removal and the chosen main
        // picture. New files are only stored once every field is valid.
        $gallery      = $id !== null ? Product::adminImages($id) : [];
        $galleryIds   = array_map(static fn (array $image): int => (int) $image['id'], $gallery);
        $removeIds    = array_values(array_intersect(
            array_map('intval', (array) ($post['remove_images'] ?? [])),
            $galleryIds
        ));
        $remaining    = max(0, count($gallery) - count($removeIds));
        $primaryImage = (int) ($post['primary_image'] ?? 0);

        if ($errors !== []) {
            http_response_code(422);

            $values['sizes']  = (string) $values['sizes'];
            $values['colors'] = (string) $values['colors'];

            $this->renderForm($values, $errors, $id !== null ? 'edit' : 'create');

            return;
        }

        [$newImages, $imageError] = $this->handleUploads($remaining);

        if ($imageError !== null) {
            $errors['images'] = $imageError;
            http_response_code(422);

            $this->renderForm($values, $errors, $id !== null ? 'edit' : 'create');

            return;
        }

        $slugSource = $values['slug'] !== '' ? $values['slug'] : $values['title'];
        $slug       = $this->uniqueSlug($slugSource, $id);

        $data = [
            'category_id' => $values['category_id'],
            'title'       => $values['title'],
            'slug'        => $slug,
            'description' => $values['description'],
            'price'       => number_format((float) $values['price'], 2, '.', ''),
            'sale_price'  => $values['sale_price'] !== ''
                ? number_format((float) $values['sale_price'], 2, '.', '')
                : null,
            'stock'       => (int) $values['stock'],
            'sizes'       => $this->sizesJson($values['sizes']),
            'colors'      => $this->colorsJson($values['colors']),
            'is_flash'    => $values['is_flash'],
            'is_featured' => $values['is_featured'],
            'is_active'   => $values['is_active'],
        ];

        if ($id === null) {
            $id = Product::create($data);
        } else {
            Product::update($id, $data);
        }

        foreach ($removeIds as $imageId) {
            Product::deleteImage($id, $imageId);
        }

        foreach ($newImages as $path) {
            Product::addImage($id, $path);
        }

        if ($primaryImage > 0 && !in_array($primaryImage, $removeIds, true)) {
            Product::setPrimaryImageId($id, $primaryImage);
        }

        Product::ensurePrimaryImage($id);

        flash('success', $existing !== null ? 'Produit mis à jour.' : 'Produit créé.');
        redirect($this->path('produits'));
    }

    /**
     * @param array<string, mixed> $product
     * @param array<string, string> $errors
     */
    private function renderForm(array $product, array $errors, string $mode): void
    {
        View::render('admin/products/form', [
            'title'       => ($mode === 'edit' ? 'Modifier' : 'Ajouter') . ' un produit — Gestion ' . config('app_name'),
            'adminActive' => 'products',
            'adminTitle'  => $mode === 'edit' ? 'Modifier le produit' : 'Nouveau produit',
            'noindex'     => true,
            'product'     => $product,
            'errors'      => $errors,
            'mode'        => $mode,
            'categories'  => Category::adminAll(),
            'images'      => (int) ($product['id'] ?? 0) > 0 ? Product::adminImages((int) $product['id']) : [],
        ], 'layouts/admin');
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyProduct(): array
    {
        return [
            'id'            => 0,
            'title'         => '',
            'slug'          => '',
            'description'   => '',
            'price'         => '',
            'sale_price'    => '',
            'stock'         => '10',
            'sizes'         => '',
            'colors'        => '',
            'category_id'   => 0,
            'is_active'     => 1,
            'is_featured'   => 0,
            'is_flash'      => 0,
        ];
    }

    private function uniqueSlug(string $source, ?int $exceptId): string
    {
        $base      = Product::slugify($source);
        $candidate = $base;
        $suffix    = 2;

        while (Product::slugExists($candidate, $exceptId)) {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function sizesJson(string $raw): string
    {
        $parts = preg_split('/[,;\n]+/', $raw) ?: [];
        $sizes = [];

        foreach ($parts as $part) {
            $part = mb_substr(trim($part), 0, 20);

            if ($part !== '' && !in_array($part, $sizes, true)) {
                $sizes[] = $part;
            }
        }

        return json_encode($sizes, JSON_UNESCAPED_UNICODE) ?: '[]';
    }

    private function colorsJson(string $raw): string
    {
        $parts  = preg_split('/[,;\n]+/', $raw) ?: [];
        $colors = [];

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part === '') {
                continue;
            }

            $name = $part;
            $hex  = '#CCCCCC';

            if (str_contains($part, ':')) {
                [$name, $hex] = array_map('trim', explode(':', $part, 2));
            }

            $hex = preg_match('/^#?[0-9a-fA-F]{6}$/', $hex) === 1
                ? '#' . ltrim($hex, '#')
                : '#CCCCCC';

            $colors[] = ['name' => mb_substr($name, 0, 40), 'hex' => $hex];
        }

        return json_encode($colors, JSON_UNESCAPED_UNICODE) ?: '[]';
    }

    /**
     * Store every uploaded gallery picture.
     *
     * @param int $remaining Pictures already kept for this product.
     * @return array{0: array<int, string>, 1: string|null}
     */
    private function handleUploads(int $remaining): array
    {
        if (!isset($_FILES['images']) || !is_array($_FILES['images']['name'] ?? null)) {
            return [[], null];
        }

        $files      = $_FILES['images'];
        $names      = (array) ($files['name'] ?? []);
        $errors     = (array) ($files['error'] ?? []);
        $sizes      = (array) ($files['size'] ?? []);
        $temporaries = (array) ($files['tmp_name'] ?? []);
        $incoming   = 0;

        foreach ($names as $index => $name) {
            if ((string) $name !== '' && ($errors[$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $incoming++;
            }
        }

        if ($incoming === 0) {
            return [[], null];
        }

        $slots = max(0, 8 - $remaining);

        if ($incoming > $slots) {
            return [[], $slots === 0
                ? 'Ce produit a déjà 8 photos. Supprimez-en une avant d’en ajouter une autre.'
                : 'Vous pouvez ajouter ' . $slots . ' photo(s) de plus (8 maximum par produit).'];
        }

        $extensions = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_WEBP => 'webp',
        ];

        $directory = PUBLIC_PATH . '/uploads/products';

        if (!is_dir($directory) && !@mkdir($directory, 0775, true)) {
            return [[], 'Dossier d’images inaccessible.'];
        }

        $stored = [];

        foreach ($names as $index => $name) {
            $error = (int) ($errors[$index] ?? UPLOAD_ERR_NO_FILE);

            if ($error === UPLOAD_ERR_NO_FILE || (string) $name === '') {
                continue;
            }

            if ($error !== UPLOAD_ERR_OK) {
                $this->deleteUploadedFiles($stored);

                return [[], 'Le téléversement a échoué, réessayez.'];
            }

            if ((int) ($sizes[$index] ?? 0) > 3 * 1024 * 1024) {
                $this->deleteUploadedFiles($stored);

                return [[], 'Chaque photo doit faire 3 Mo maximum.'];
            }

            $info = @getimagesize((string) ($temporaries[$index] ?? ''));

            if ($info === false || !isset($extensions[$info[2]])) {
                $this->deleteUploadedFiles($stored);

                return [[], 'Format accepté : JPG, PNG ou WebP.'];
            }

            $filename = 'p' . bin2hex(random_bytes(8)) . '.' . $extensions[$info[2]];

            if (!@move_uploaded_file((string) ($temporaries[$index] ?? ''), $directory . '/' . $filename)) {
                $this->deleteUploadedFiles($stored);

                return [[], 'Enregistrement de l’image impossible.'];
            }

            $stored[] = 'uploads/products/' . $filename;
        }

        return [$stored, null];
    }

    /**
     * @param array<int, string> $paths
     */
    private function deleteUploadedFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if (!str_starts_with($path, 'uploads/products/')) {
                continue;
            }

            $file = PUBLIC_PATH . '/' . $path;

            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    /* ------------------------------------------------------------------
       Categories
       ------------------------------------------------------------------ */

    public function categories(): void
    {
        $this->requireAdmin();

        View::render('admin/categories', [
            'title'       => 'Catégories — Gestion ' . config('app_name'),
            'adminActive' => 'categories',
            'adminTitle'  => 'Catégories',
            'noindex'     => true,
            'categories'  => Category::adminAll(),
            'icons'       => self::CATEGORY_ICONS,
        ], 'layouts/admin');
    }

    public function storeCategory(): void
    {
        $this->requireAdmin();
        $this->requireCsrf($this->path('categories'));

        $name = mb_substr(trim((string) ($_POST['name'] ?? '')), 0, 100);

        if (mb_strlen($name) < 2) {
            flash('error', 'Le nom de la catégorie est trop court.');

            redirect($this->path('categories'));
        }

        $icon = (string) ($_POST['icon'] ?? '');

        if (!array_key_exists($icon, self::CATEGORY_ICONS)) {
            $icon = '';
        }

        $slugSource = trim((string) ($_POST['slug'] ?? ''));
        $base       = Product::slugify($slugSource !== '' ? $slugSource : $name);
        $slug       = $base;
        $suffix     = 2;

        while (Category::slugExists($slug)) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        Category::create(
            $name,
            $slug,
            max(1, (int) ($_POST['sort_order'] ?? 1)),
            $icon,
            isset($_POST['is_active'])
        );

        flash('success', 'Catégorie ajoutée. Elle apparaît maintenant dans le menu, les filtres et le pied de page.');
        redirect($this->path('categories'));
    }

    public function editCategoryForm(string $id): void
    {
        $this->requireAdmin();

        $category = Category::adminFind((int) $id);

        if ($category === null) {
            flash('error', 'Catégorie introuvable.');
            redirect($this->path('categories'));
        }

        $this->renderCategoryForm($category, []);
    }

    public function updateCategory(string $id): void
    {
        $this->requireAdmin();

        $category = Category::adminFind((int) $id);

        if ($category === null) {
            flash('error', 'Catégorie introuvable.');
            redirect($this->path('categories'));
        }

        $this->requireCsrf($this->path('categories/' . $id . '/modifier'));

        $values = [
            'id'         => (int) $id,
            'name'       => mb_substr(trim((string) ($_POST['name'] ?? '')), 0, 100),
            'slug'       => trim((string) ($_POST['slug'] ?? '')),
            'icon'       => (string) ($_POST['icon'] ?? ''),
            'sort_order' => (string) max(1, (int) ($_POST['sort_order'] ?? 1)),
            'is_active'  => isset($_POST['is_active']) ? '1' : '0',
        ];

        $errors = [];

        if (mb_strlen($values['name']) < 2) {
            $errors['name'] = 'Le nom doit contenir au moins 2 caractères.';
        }

        if (!array_key_exists($values['icon'], self::CATEGORY_ICONS)) {
            $values['icon'] = '';
        }

        $slugSource = $values['slug'] !== '' ? $values['slug'] : $values['name'];
        $slug       = Product::slugify($slugSource);

        if (Category::slugExists($slug, (int) $id)) {
            $errors['slug'] = 'Cette adresse est déjà utilisée par une autre catégorie.';
        }

        if ($errors !== []) {
            http_response_code(422);
            $this->renderCategoryForm($values, $errors);

            return;
        }

        Category::update((int) $id, [
            'name'       => $values['name'],
            'slug'       => $slug,
            'icon'       => $values['icon'],
            'sort_order' => (int) $values['sort_order'],
            'is_active'  => $values['is_active'] === '1' ? 1 : 0,
        ]);

        flash('success', 'Catégorie mise à jour sur tout le site.');
        redirect($this->path('categories'));
    }

    /**
     * @param array<string, mixed>  $category
     * @param array<string, string> $errors
     */
    private function renderCategoryForm(array $category, array $errors): void
    {
        View::render('admin/category-form', [
            'title'       => 'Modifier une catégorie — Gestion ' . config('app_name'),
            'adminActive' => 'categories',
            'adminTitle'  => 'Modifier la catégorie',
            'noindex'     => true,
            'category'    => $category,
            'errors'      => $errors,
            'icons'       => self::CATEGORY_ICONS,
        ], 'layouts/admin');
    }

    public function deleteCategory(string $id): void
    {
        $this->requireAdmin();
        $this->requireCsrf($this->path('categories'));

        Category::delete((int) $id);

        flash('success', 'Catégorie supprimée. Ses produits restent en boutique, sans catégorie.');
        redirect($this->path('categories'));
    }

    /* ------------------------------------------------------------------
       Social accounts (footer)
       ------------------------------------------------------------------ */

    public function socialLinks(): void
    {
        $this->requireAdmin();

        View::render('admin/social', [
            'title'       => 'Réseaux sociaux — Gestion ' . config('app_name'),
            'adminActive' => 'social',
            'adminTitle'  => 'Réseaux sociaux',
            'noindex'     => true,
            'links'       => SocialLink::all(),
            'platforms'   => SocialLink::platforms(),
            'maxLinks'    => SocialLink::maxLinks(),
        ], 'layouts/admin');
    }

    public function addSocialLink(): void
    {
        $this->requireAdmin();
        $this->requireCsrf($this->path('reseaux'));

        $platform = (string) ($_POST['platform'] ?? '');
        $url      = trim((string) ($_POST['url'] ?? ''));
        $label    = mb_substr(trim((string) ($_POST['label'] ?? '')), 0, 40);

        if (count(SocialLink::all()) >= SocialLink::maxLinks()) {
            flash('error', 'Vous pouvez afficher ' . SocialLink::maxLinks() . ' réseaux au maximum. Supprimez-en un d’abord.');
            redirect($this->path('reseaux'));
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false || preg_match('#^https?://#i', $url) !== 1) {
            flash('error', 'Adresse invalide : elle doit commencer par https:// (ou http://).');
            redirect($this->path('reseaux'));
        }

        if (!SocialLink::add($platform, $url, $label)) {
            flash('error', 'Impossible d’ajouter ce réseau. Vérifiez le réseau et l’adresse.');
            redirect($this->path('reseaux'));
        }

        flash('success', 'Réseau ajouté au pied de page.');
        redirect($this->path('reseaux'));
    }

    public function deleteSocialLink(string $id): void
    {
        $this->requireAdmin();
        $this->requireCsrf($this->path('reseaux'));

        SocialLink::remove($id);

        flash('success', 'Réseau supprimé du pied de page.');
        redirect($this->path('reseaux'));
    }

    /* ------------------------------------------------------------------
       Helpers
       ------------------------------------------------------------------ */

    private function isLoggedIn(): bool
    {
        $admin = Session::get(self::SESSION_KEY);

        return is_array($admin) && !empty($admin['email']);
    }

    private function requireAdmin(): void
    {
        if (!$this->isLoggedIn()) {
            redirect($this->path('connexion'));
        }
    }

    private function requireCsrf(string $fallback): void
    {
        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            flash('error', 'Session expirée, merci de réessayer.');
            redirect($fallback);
        }
    }

    private function basePath(): string
    {
        return '/' . trim((string) config('admin_path'), '/');
    }

    private function path(string $suffix = ''): string
    {
        return $this->basePath() . ($suffix !== '' ? '/' . ltrim($suffix, '/') : '');
    }

    private function notFound(): void
    {
        http_response_code(404);

        View::render('errors/404', [
            'title'       => 'Produit introuvable — ' . config('app_name'),
            'description' => 'Ce produit n’existe plus.',
            'canonical'   => url('/'),
            'cartCount'   => 0,
            'wishCount'   => 0,
            'noindex'     => true,
        ]);
    }
}
