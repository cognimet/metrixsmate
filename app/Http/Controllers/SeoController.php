<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

/**
 * Serves SEO / crawler discovery files dynamically:
 *   - /sitemap.xml  XML sitemap of publicly crawlable pages
 *   - /robots.txt   Crawler directives + sitemap reference
 *   - /llms.txt     Structured site overview for LLMs (https://llmstxt.org)
 *
 * Only public, indexable pages are listed. Authenticated, admin, API,
 * webhook and callback routes are intentionally excluded.
 */
class SeoController extends Controller
{
    /**
     * Publicly crawlable pages, keyed by route name (or literal path).
     * Add DB-driven URLs inside sitemap() if public content pages are introduced.
     *
     * [route/path, changefreq, priority]
     */
    private function publicPages(): array
    {
        return [
            ['/',                    'weekly',  '1.0'],
            ['login',                'monthly', '0.5'],
            ['register',             'monthly', '0.5'],
            ['certificates.verify',  'monthly', '0.6'],
        ];
    }

    /**
     * Resolve a route name or literal path to an absolute URL.
     */
    private function resolveUrl(string $nameOrPath): string
    {
        if (str_starts_with($nameOrPath, '/')) {
            return url($nameOrPath);
        }

        return route($nameOrPath, [], true);
    }

    /**
     * GET /sitemap.xml
     */
    public function sitemap(): Response
    {
        $lastmod = Carbon::now()->toAtomString();

        $urls = [];
        foreach ($this->publicPages() as [$page, $changefreq, $priority]) {
            $loc = htmlspecialchars($this->resolveUrl($page), ENT_XML1);
            $urls[] = <<<XML
    <url>
        <loc>{$loc}</loc>
        <lastmod>{$lastmod}</lastmod>
        <changefreq>{$changefreq}</changefreq>
        <priority>{$priority}</priority>
    </url>
XML;
        }

        $body = implode("\n", $urls);

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$body}
</urlset>
XML;

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * GET /robots.txt
     */
    public function robots(): Response
    {
        $sitemap = url('/sitemap.xml');

        $lines = [
            'User-agent: *',
            'Allow: /$',
            'Disallow: /admin',
            'Disallow: /dashboard',
            'Disallow: /profile',
            'Disallow: /payments',
            'Disallow: /coupons',
            'Disallow: /certificates',
            'Disallow: /results',
            'Disallow: /assessments',
            'Disallow: /school-finder',
            'Disallow: /quizzes',
            'Disallow: /auth/',
            'Disallow: /webhooks/',
            '',
            "Sitemap: {$sitemap}",
        ];

        return response(implode("\n", $lines) . "\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /**
     * GET /llms.txt
     */
    public function llms(): Response
    {
        $name    = config('app.name', 'MetrixsMate');
        $base    = rtrim(url('/'), '/');
        $sitemap = url('/sitemap.xml');

        $content = <<<MD
# {$name}

> {$name} is a psychometric and career-guidance platform. Users take assessments to receive personalized education and career recommendations, including an AI-powered school finder and downloadable reports and certificates.

## Overview

- Take psychometric assessments and quizzes
- Receive AI-driven school, university, and career recommendations
- Generate and verify certificates
- Manage payments and coupons for premium reports

Most application features require authentication. The pages below are publicly accessible.

## Public Pages

- [Home]({$base}/): Product overview and entry point
- [Login]({$base}/login): Account sign-in
- [Register]({$base}/register): Create an account
- [Verify Certificate]({$base}/verify-certificate): Public certificate verification

## Resources

- [Sitemap]({$sitemap}): XML sitemap of public pages
- [robots.txt]({$base}/robots.txt): Crawler directives

## Notes

- Authenticated, admin, API, and webhook endpoints are excluded from indexing.
- Contact the site administrators for API access or partnership inquiries.
MD;

        return response($content . "\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
