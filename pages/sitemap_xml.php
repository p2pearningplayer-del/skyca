<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — DYNAMIC XML SITEMAP (pages/sitemap_xml.php)
 * Outputs valid application/xml sitemap for all public indexable URLs.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';

header('Content-Type: application/xml; charset=UTF-8');
header('X-Robots-Tag: noindex');

$allServices = skyca_get_all_services();
$lastmod     = date('Y-m-d');

$urls = [
    ['loc' => skyca_canonical_url('/'),          'changefreq' => 'weekly',  'priority' => '1.0'],
    ['loc' => skyca_canonical_url('/services'),  'changefreq' => 'weekly',  'priority' => '0.9'],
];

foreach ($allServices as $srv) {
    $urls[] = [
        'loc'        => skyca_canonical_url('/services/' . $srv['slug']),
        'changefreq' => 'weekly',
        'priority'   => '0.9',
    ];
}

$urls[] = ['loc' => skyca_canonical_url('/about'),                'changefreq' => 'monthly', 'priority' => '0.8'];
$urls[] = ['loc' => skyca_canonical_url('/contact'),              'changefreq' => 'monthly', 'priority' => '0.9'];
$urls[] = ['loc' => skyca_canonical_url('/faq'),                  'changefreq' => 'monthly', 'priority' => '0.7'];
$urls[] = ['loc' => skyca_canonical_url('/privacy-policy'),       'changefreq' => 'yearly',  'priority' => '0.4'];
$urls[] = ['loc' => skyca_canonical_url('/terms-and-conditions'), 'changefreq' => 'yearly',  'priority' => '0.4'];
$urls[] = ['loc' => skyca_canonical_url('/cookie-policy'),        'changefreq' => 'yearly',  'priority' => '0.3'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u): ?>
    <url>
        <loc><?php echo htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8'); ?></loc>
        <lastmod><?php echo $lastmod; ?></lastmod>
        <changefreq><?php echo $u['changefreq']; ?></changefreq>
        <priority><?php echo $u['priority']; ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
