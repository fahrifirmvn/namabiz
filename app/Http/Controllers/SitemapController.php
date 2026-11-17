<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Observers\CatalogCacheObserver;
use App\Support\PortalContent;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Serve cached XML sitemap covering public directory pages and supplier profiles.
     */
    public function index(): Response
    {
        $xml = Cache::remember(CatalogCacheObserver::SITEMAP_KEY, 3600, function (): string {
            $entries = [
                [
                    'loc' => route('home'),
                    'changefreq' => 'daily',
                    'priority' => '1.0',
                    'lastmod' => null,
                ],
                [
                    'loc' => route('discover.index'),
                    'changefreq' => 'daily',
                    'priority' => '0.9',
                    'lastmod' => null,
                ],
            ];

            foreach (array_keys((array) config('namabiz.pages', [])) as $pageKey) {
                $entries[] = [
                    'loc' => PortalContent::pageUrl($pageKey),
                    'changefreq' => 'monthly',
                    'priority' => '0.5',
                    'lastmod' => null,
                ];
            }

            Supplier::query()
                ->select(['slug', 'updated_at'])
                ->orderBy('id')
                ->chunk(500, function ($suppliers) use (&$entries): void {
                    foreach ($suppliers as $supplier) {
                        $entries[] = [
                            'loc' => route('suppliers.show', $supplier->slug),
                            'changefreq' => 'weekly',
                            'priority' => '0.8',
                            'lastmod' => $supplier->updated_at?->toAtomString(),
                        ];
                    }
                });

            $lines = [
                '<?xml version="1.0" encoding="UTF-8"?>',
                '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
            ];

            foreach ($entries as $entry) {
                $loc = htmlspecialchars($entry['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $lines[] = '  <url>';
                $lines[] = "    <loc>{$loc}</loc>";
                if ($entry['lastmod'] !== null) {
                    $lastmod = htmlspecialchars($entry['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
                    $lines[] = "    <lastmod>{$lastmod}</lastmod>";
                }
                $lines[] = "    <changefreq>{$entry['changefreq']}</changefreq>";
                $lines[] = "    <priority>{$entry['priority']}</priority>";
                $lines[] = '  </url>';
            }

            $lines[] = '</urlset>';

            return implode("\n", $lines);
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
