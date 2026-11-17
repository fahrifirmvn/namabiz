<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class PortalContent
{
    public static function premiumPrice(): string
    {
        return 'Rp'.SupplierFormat::number((int) config('namabiz.billing.price.amount'));
    }

    public static function premiumPriceLabel(): string
    {
        return self::premiumPrice().'/'.config('namabiz.billing.price.period');
    }

    public static function pageUrl(string $page): string
    {
        return route(config("namabiz.pages.{$page}.route"));
    }

    public static function email(): ?string
    {
        $email = trim((string) config('namabiz.contact.email'));

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    public static function externalUrl(?string $value): ?string
    {
        $url = trim((string) $value);

        if (! filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https'
            || parse_url($url, PHP_URL_USER) !== null || preg_match('/[\x00-\x20\x7f]/', $url)) {
            return null;
        }

        return $url;
    }

    public static function whatsappUrl(): ?string
    {
        $source = self::externalUrl(config('namabiz.contact.whatsapp_portal_source'));
        if ($source === null || ! in_array(strtolower((string) parse_url($source, PHP_URL_HOST)), config('namabiz.official_hosts'), true)) {
            return null;
        }

        $raw = trim((string) config('namabiz.contact.whatsapp'));
        if ($raw === '' || ! preg_match('/^\+?[\d ()-]+$/', $raw)) {
            return null;
        }

        $number = preg_replace('/\D/', '', $raw) ?? '';
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        if (! preg_match('/^[1-9]\d{6,14}$/', $number)) {
            return null;
        }

        return 'https://wa.me/'.$number.'?text='.rawurlencode(config('namabiz.contact.whatsapp_message'));
    }

    /** @return array{email: ?string, address: ?string, whatsapp: ?string, social: array<string, string>} */
    public static function contacts(): array
    {
        $social = [];
        foreach (config('namabiz.contact.social', []) as $label => $value) {
            if ($url = self::externalUrl($value)) {
                $social[$label] = $url;
            }
        }

        return [
            'email' => self::email(),
            'address' => trim((string) config('namabiz.contact.address')) ?: null,
            'whatsapp' => self::whatsappUrl(),
            'social' => $social,
        ];
    }

    /** @return array<string, array{label: string, url: string}> */
    public static function authOptions(): array
    {
        $options = [];
        foreach (config('namabiz.auth_options', []) as $feature => $option) {
            if (! config("namabiz.features.{$feature}") || blank($option['route'])) {
                continue;
            }

            $route = Route::getRoutes()->getByName($option['route']);
            if ($route === null || ! in_array('GET', $route->methods(), true) || ! in_array('web', $route->gatherMiddleware(), true)) {
                continue;
            }

            $options[$feature] = ['label' => $option['label'], 'url' => route($option['route'])];
        }

        return $options;
    }
}
