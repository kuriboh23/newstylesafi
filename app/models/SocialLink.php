<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Social accounts shown in the storefront footer.
 *
 * The list is stored as JSON in the settings table so the owner can add or
 * remove networks without touching a template. When nothing has ever been
 * saved, the Instagram address from config.php is used as the starting point.
 */
final class SocialLink
{
    private const SETTING_KEY = 'social_links';
    private const MAX_LINKS   = 8;

    /**
     * Platforms supported by the footer icon set.
     *
     * @return array<string, string>
     */
    public static function platforms(): array
    {
        return [
            'instagram' => 'Instagram',
            'facebook'  => 'Facebook',
            'tiktok'    => 'TikTok',
            'youtube'   => 'YouTube',
            'whatsapp'  => 'WhatsApp',
            'link'      => 'Autre lien',
        ];
    }

    public static function maxLinks(): int
    {
        return self::MAX_LINKS;
    }

    /**
     * @return array<int, array{id: string, platform: string, url: string, label: string}>
     */
    public static function all(): array
    {
        $stored = Setting::raw(self::SETTING_KEY, null);

        if ($stored === null || $stored === '') {
            return self::defaults();
        }

        $decoded = json_decode((string) $stored, true);

        if (!is_array($decoded)) {
            return [];
        }

        return self::normalize($decoded);
    }

    /**
     * Add one social account. Returns false when the input or the limit is
     * invalid; the controller shows the matching message.
     */
    public static function add(string $platform, string $url, string $label): bool
    {
        $platforms = self::platforms();
        $url       = trim($url);
        $label     = trim($label);

        if (!isset($platforms[$platform])) {
            return false;
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false || preg_match('#^https?://#i', $url) !== 1) {
            return false;
        }

        $links = self::all();

        if (count($links) >= self::MAX_LINKS) {
            return false;
        }

        $links[] = [
            'id'       => 's' . bin2hex(random_bytes(6)),
            'platform' => $platform,
            'url'      => mb_substr($url, 0, 300),
            'label'    => mb_substr($label, 0, 40),
        ];

        self::save($links);

        return true;
    }

    public static function remove(string $id): void
    {
        $links = array_values(array_filter(
            self::all(),
            static fn (array $link): bool => $link['id'] !== $id
        ));

        self::save($links);
    }

    /**
     * @return array<int, array{id: string, platform: string, url: string, label: string}>
     */
    private static function defaults(): array
    {
        $instagram = trim((string) Setting::raw('instagram_url', (string) config('instagram_url')));

        if ($instagram === '' || filter_var($instagram, FILTER_VALIDATE_URL) === false) {
            return [];
        }

        return [[
            'id'       => 'default-instagram',
            'platform' => 'instagram',
            'url'      => mb_substr($instagram, 0, 300),
            'label'    => '',
        ]];
    }

    /**
     * @param array<int, mixed> $rows
     * @return array<int, array{id: string, platform: string, url: string, label: string}>
     */
    private static function normalize(array $rows): array
    {
        $platforms = self::platforms();
        $links     = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $platform = (string) ($row['platform'] ?? '');
            $url      = trim((string) ($row['url'] ?? ''));

            if (!isset($platforms[$platform]) || filter_var($url, FILTER_VALIDATE_URL) === false) {
                continue;
            }

            if (preg_match('#^https?://#i', $url) !== 1) {
                continue;
            }

            $id = preg_replace('/[^a-zA-Z0-9-]/', '', (string) ($row['id'] ?? '')) ?? '';

            $links[] = [
                'id'       => $id !== '' ? $id : 's' . bin2hex(random_bytes(4)),
                'platform' => $platform,
                'url'      => mb_substr($url, 0, 300),
                'label'    => mb_substr(trim((string) ($row['label'] ?? '')), 0, 40),
            ];
        }

        return array_slice($links, 0, self::MAX_LINKS);
    }

    /**
     * @param array<int, array<string, string>> $links
     */
    private static function save(array $links): void
    {
        $json = json_encode(array_values($links), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        Setting::put(self::SETTING_KEY, $json === false ? '[]' : $json);
    }
}
