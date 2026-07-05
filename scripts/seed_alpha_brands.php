<?php
/**
 * Alpha Net Brands Seeder - Updated from tree.html (44 brands)
 * Source: https://www.alpha.net.bd/tree.html
 * Run: php scripts/seed_alpha_brands.php
 */

define('BASE_PATH', dirname(__DIR__));

// Load env
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (!strpos($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val, '"\'');
        putenv(trim($key) . '=' . trim($val, '"\''));
    }
}

// Load helpers
require_once BASE_PATH . '/vendor/autoload.php';

// Connect to SQLite
$dbPath = BASE_PATH . '/' . ($_ENV['DB_DATABASE'] ?? 'database/database.sqlite');
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$now = date('Y-m-d H:i:s');

// -------------------------------------------------------
// Helper functions
// -------------------------------------------------------

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

function makeUniqueSlug(PDO $pdo, string $name): string {
    $slug = slugify($name);
    $original = $slug;
    $counter = 1;
    while (true) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM companies WHERE slug = ?');
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() == 0) break;
        $slug = $original . '-' . $counter++;
    }
    return $slug;
}

function downloadLogo(string $url, string $dir, string $filename): ?string {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $dest = $dir . '/' . $filename;
    $ctx = stream_context_create(['http' => [
        'timeout'         => 20,
        'user_agent'      => 'Mozilla/5.0 (compatible; BrandSeeder/1.0)',
        'follow_location' => true,
    ]]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data === false || strlen($data) < 100) return null;
    file_put_contents($dest, $data);
    return $filename;
}

function insertCompany(PDO $pdo, array $d, string $now): int {
    $stmt = $pdo->prepare('SELECT id FROM companies WHERE slug = ?');
    $stmt->execute([$d['slug']]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
        echo "  [SKIP] Already exists: {$d['name']} (id={$existing})\n";
        return (int)$existing;
    }

    $sql = 'INSERT INTO companies (name, slug, domain, description, social_links, is_public, status, created_at, updated_at';
    $vals = '(?, ?, ?, ?, ?, ?, ?, ?, ?';
    $params = [
        $d['name'], $d['slug'], $d['domain'] ?? '', $d['description'] ?? '',
        $d['social_links'] ?? '[]', 1, 'active', $now, $now,
    ];

    if (!empty($d['avatar_image_path'])) {
        $sql .= ', avatar_image_path';
        $vals .= ', ?';
        $params[] = $d['avatar_image_path'];
    }

    $sql .= ') VALUES ' . $vals . ')';
    $pdo->prepare($sql)->execute($params);
    return (int)$pdo->lastInsertId();
}

// -------------------------------------------------------
// Brand Data - All 44 from https://www.alpha.net.bd/tree.html
// Logo base URL: https://www.alpha.net.bd/Content/img/brands/
// -------------------------------------------------------
$logoBaseDir = BASE_PATH . '/storage/uploads/companies/avatar';
$brandLogoBase = 'https://www.alpha.net.bd/Content/img/brands/';
$faviconBase = 'https://www.google.com/s2/favicons?domain=%s&sz=128';

$brands = [
    // 1
    [
        'name'        => 'Alpha Net',
        'domain'      => 'alpha.net.bd',
        'description' => 'Alpha Net - Leading web hosting and technology company in Bangladesh.',
        'logo_file'   => 'alpha-net-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook',  'icon' => 'ri-facebook-fill',  'url' => 'https://www.facebook.com/alpha.net.bd'],
            ['platform' => 'whatsapp',  'icon' => 'ri-whatsapp-fill',  'url' => 'https://wa.me/8809613250250'],
            ['platform' => 'linkedin',  'icon' => 'ri-linkedin-fill',  'url' => 'https://www.linkedin.com/company/alpha.net.bd'],
            ['platform' => 'youtube',   'icon' => 'ri-youtube-fill',   'url' => 'https://www.youtube.com/@alphanetbd'],
        ],
    ],
    // 2
    [
        'name'        => 'Central Florida IT Solutions',
        'domain'      => 'cfl-it.com',
        'description' => 'Central Florida IT Solutions - Professional IT services in Central Florida, USA.',
        'logo_file'   => 'cfl-it-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook',  'icon' => 'ri-facebook-fill',  'url' => 'https://www.facebook.com/CFLITSOLUTIONS/'],
            ['platform' => 'instagram', 'icon' => 'ri-instagram-fill', 'url' => 'https://www.instagram.com/cfl_it/'],
            ['platform' => 'linkedin',  'icon' => 'ri-linkedin-fill',  'url' => 'https://www.linkedin.com/company/central-florida-it-solutions/'],
            ['platform' => 'youtube',   'icon' => 'ri-youtube-fill',   'url' => 'https://www.youtube.com/channel/UCK1fXJ_FZN4RavIY8UtIHGg'],
            ['platform' => 'pinterest', 'icon' => 'ri-pinterest-fill', 'url' => 'https://www.pinterest.com/cflitsolutions'],
            ['platform' => 'twitter',   'icon' => 'ri-twitter-fill',   'url' => 'https://twitter.com/CFL_IT'],
        ],
    ],
    // 3
    [
        'name'        => 'Residential VPS',
        'domain'      => 'residentialvps.com',
        'description' => 'Residential VPS - Residential IP VPS hosting solutions.',
        'logo_file'   => 'residentialvps.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/residentialvps'],
        ],
    ],
    // 4
    [
        'name'        => 'vps.bd',
        'domain'      => 'vps.bd',
        'description' => 'vps.bd - Virtual private server hosting in Bangladesh.',
        'logo_file'   => 'vps-bd.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/vps.com.bd'],
            ['platform' => 'linkedin', 'icon' => 'ri-linkedin-fill', 'url' => 'https://www.linkedin.com/company/vps-com-bd/'],
            ['platform' => 'youtube',  'icon' => 'ri-youtube-fill',  'url' => 'https://www.youtube.com/@vpscombd'],
        ],
    ],
    // 5
    [
        'name'        => 'host.bd',
        'domain'      => 'host.net.bd',
        'description' => 'host.bd - Web hosting services in Bangladesh.',
        'logo_file'   => 'host-bd.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/host.net.bd/'],
            ['platform' => 'linkedin', 'icon' => 'ri-linkedin-fill', 'url' => 'https://www.linkedin.com/company/host-net-bd/'],
            ['platform' => 'youtube',  'icon' => 'ri-youtube-fill',  'url' => 'https://www.youtube.com/@host_net_bd'],
        ],
    ],
    // 6
    [
        'name'        => 'Godigital',
        'domain'      => 'godigital.bd',
        'description' => 'Godigital - Digital marketing and branding solutions in Bangladesh.',
        'logo_file'   => 'godigital.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook',  'icon' => 'ri-facebook-fill',  'url' => 'https://www.facebook.com/profile.php?id=100087336774327'],
            ['platform' => 'whatsapp',  'icon' => 'ri-whatsapp-fill',  'url' => 'https://wa.me/8809613823923'],
            ['platform' => 'linkedin',  'icon' => 'ri-linkedin-fill',  'url' => 'https://www.linkedin.com/company/go-digital-bd/'],
            ['platform' => 'youtube',   'icon' => 'ri-youtube-fill',   'url' => 'https://www.youtube.com/@godigitalmarketing'],
            ['platform' => 'pinterest', 'icon' => 'ri-pinterest-fill', 'url' => 'https://www.pinterest.com/Go_Digital_BD/_created/'],
            ['platform' => 'behance',   'icon' => 'ri-behance-fill',   'url' => 'https://www.behance.net/godigitalbd'],
            ['platform' => 'dribbble',  'icon' => 'ri-dribbble-fill',  'url' => 'https://dribbble.com/Go_Digital_BD'],
            ['platform' => 'medium',    'icon' => 'ri-medium-fill',    'url' => 'https://medium.com/@godigitalbd1'],
        ],
    ],
    // 7
    [
        'name'        => 'cms.bd',
        'domain'      => 'cms.bd',
        'description' => 'cms.bd - Content management system solutions in Bangladesh.',
        'logo_file'   => 'cmsbd.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/cms.com.bd'],
            ['platform' => 'linkedin', 'icon' => 'ri-linkedin-fill', 'url' => 'https://www.linkedin.com/company/cms-com-bd/'],
        ],
    ],
    // 8
    [
        'name'        => 'pbx.bd',
        'domain'      => 'pbx.bd',
        'description' => 'pbx.bd - IP PBX phone system solutions in Bangladesh.',
        'logo_file'   => 'alphapbx.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook',  'icon' => 'ri-facebook-fill',  'url' => 'https://www.facebook.com/pbx.bd'],
            ['platform' => 'whatsapp',  'icon' => 'ri-whatsapp-fill',  'url' => 'https://wa.me/8809613820202'],
            ['platform' => 'linkedin',  'icon' => 'ri-linkedin-fill',  'url' => 'https://www.linkedin.com/showcase/pbx-bd/'],
            ['platform' => 'youtube',   'icon' => 'ri-youtube-fill',   'url' => 'https://www.youtube.com/@Alphapbx'],
        ],
    ],
    // 9
    [
        'name'        => 'sms.bd',
        'domain'      => 'sms.bd',
        'description' => 'sms.bd - Business SMS messaging platform in Bangladesh.',
        'logo_file'   => 'alpha_sms.png',
        'logo_ext'    => 'png',
        'social' => [
            ['platform' => 'facebook',  'icon' => 'ri-facebook-fill',  'url' => 'https://www.facebook.com/sms.net.bd'],
            ['platform' => 'whatsapp',  'icon' => 'ri-whatsapp-fill',  'url' => 'https://wa.me/8809613250260'],
            ['platform' => 'linkedin',  'icon' => 'ri-linkedin-fill',  'url' => 'https://www.linkedin.com/company/sms-bd/'],
            ['platform' => 'youtube',   'icon' => 'ri-youtube-fill',   'url' => 'https://www.youtube.com/@Alpha_SMS'],
        ],
    ],
    // 10
    [
        'name'        => 'Unified Chat',
        'domain'      => 'unichat.bd',
        'description' => 'Unified Chat - Unified communication platform for businesses.',
        'logo_file'   => 'unified-chat-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/unichat.com.bd'],
            ['platform' => 'linkedin', 'icon' => 'ri-linkedin-fill', 'url' => 'https://www.linkedin.com/company/unified-chat/'],
        ],
    ],
    // 11
    [
        'name'        => 'Nova Technologies LTD',
        'domain'      => 'nova.bd',
        'description' => 'Nova Technologies LTD - Technology solutions company.',
        'logo_file'   => 'nova.bd.logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 12
    [
        'name'        => 'W3SSL',
        'domain'      => 'w3ssl.com',
        'description' => 'W3SSL - SSL certificates and web security solutions.',
        'logo_file'   => 'w3ssl-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/w3ssl'],
            ['platform' => 'linkedin', 'icon' => 'ri-linkedin-fill', 'url' => 'https://www.linkedin.com/company/w3ssl/'],
        ],
    ],
    // 13
    [
        'name'        => 'First Line Agents',
        'domain'      => 'firstlineagents.com',
        'description' => 'First Line Agents, LLC - Professional call center and support services.',
        'logo_file'   => 'first-line-agents.png',
        'logo_ext'    => 'png',
        'social'      => [],
    ],
    // 14
    [
        'name'        => 'IPBXPHONE',
        'domain'      => 'ipbxphone.com',
        'description' => 'IPBXPHONE - IP PBX phone system solutions.',
        'logo_file'   => 'ipbx-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook',  'icon' => 'ri-facebook-fill',  'url' => 'https://www.facebook.com/ipbxphone'],
            ['platform' => 'instagram', 'icon' => 'ri-instagram-fill', 'url' => 'https://www.instagram.com/ipbxphone/'],
            ['platform' => 'linkedin',  'icon' => 'ri-linkedin-fill',  'url' => 'https://www.linkedin.com/company/ipbxphone'],
            ['platform' => 'youtube',   'icon' => 'ri-youtube-fill',   'url' => 'https://www.youtube.com/channel/UCtV2aVXYEvy3A-2EkCTbLHg'],
        ],
    ],
    // 15
    [
        'name'        => 'GoCards',
        'domain'      => 'gocards.bd',
        'description' => 'GoCards - Digital business cards and networking solutions.',
        'logo_file'   => 'gocards-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/gocardsbd'],
            ['platform' => 'linkedin', 'icon' => 'ri-linkedin-fill', 'url' => 'https://www.linkedin.com/company/go-cardsbd/'],
            ['platform' => 'youtube',  'icon' => 'ri-youtube-fill',  'url' => 'https://www.youtube.com/@godigitalmarketing'],
        ],
    ],
    // 16
    [
        'name'        => 'Sineris',
        'domain'      => 'sineris.com',
        'description' => 'Sineris - Web services and technology solutions.',
        'logo_file'   => 'sineris-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook',  'icon' => 'ri-facebook-fill',  'url' => 'https://www.facebook.com/SineriS-103296028592302'],
            ['platform' => 'instagram', 'icon' => 'ri-instagram-fill', 'url' => 'https://www.instagram.com/sineriswebservices/'],
            ['platform' => 'linkedin',  'icon' => 'ri-linkedin-fill',  'url' => 'https://www.linkedin.com/company/sineris'],
            ['platform' => 'youtube',   'icon' => 'ri-youtube-fill',   'url' => 'https://www.youtube.com/channel/UCx0zl2BGC6DeVUDjLxSvbKA'],
        ],
    ],
    // 17
    [
        'name'        => 'Nova Colo',
        'domain'      => 'novacolo.com',
        'description' => 'Nova Colo - Data center colocation services.',
        'logo_file'   => 'nova-colo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 18
    [
        'name'        => 'Nova IT Institute',
        'domain'      => 'novaitinstitute.com',
        'description' => 'Nova IT Institute - Professional IT training and education.',
        'logo_file'   => 'nova-it-institute-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/NovaItInstitute'],
            ['platform' => 'linkedin', 'icon' => 'ri-linkedin-fill', 'url' => 'https://www.linkedin.com/company/nova-it-institute/'],
            ['platform' => 'youtube',  'icon' => 'ri-youtube-fill',  'url' => 'https://www.youtube.com/@novaitinstitute'],
        ],
    ],
    // 19
    [
        'name'        => 'Nova Foundation',
        'domain'      => 'nova.org.bd',
        'description' => 'Nova Foundation - Non-profit organization for community development.',
        'logo_file'   => 'nova-foundation-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/NovaFoundation/'],
            ['platform' => 'linkedin', 'icon' => 'ri-linkedin-fill', 'url' => 'https://www.linkedin.com/company/nova-foundationbd/'],
            ['platform' => 'youtube',  'icon' => 'ri-youtube-fill',  'url' => 'https://www.youtube.com/@novafoundation'],
        ],
    ],
    // 20
    [
        'name'        => 'crm.bd',
        'domain'      => 'crm.bd',
        'description' => 'crm.bd - Customer relationship management solution.',
        'logo_file'   => 'crmbd.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 21
    [
        'name'        => 'Alora Cloud',
        'domain'      => 'alora.cloud',
        'description' => 'Alora Cloud - Cloud infrastructure and services.',
        'logo_file'   => 'alora-cloud-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 22
    [
        'name'        => 'Alora Suite',
        'domain'      => 'alorasuite.com',
        'description' => 'Alora Suite - Complete business communication suite.',
        'logo_file'   => 'alorasuite-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 23
    [
        'name'        => 'Alora Email',
        'domain'      => 'alora.email',
        'description' => 'Alora Email - Professional business email hosting.',
        'logo_file'   => 'alora-email-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 24
    [
        'name'        => 'Alora Fax',
        'domain'      => 'alorafax.com',
        'description' => 'Alora Fax - Cloud fax services for businesses.',
        'logo_file'   => 'alorafax-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 25
    [
        'name'        => 'Alora SMS',
        'domain'      => 'alorasms.com',
        'description' => 'Alora SMS - Business SMS messaging platform.',
        'logo_file'   => 'alora-sms-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 26
    [
        'name'        => 'Alora Meet',
        'domain'      => 'alorameet.com',
        'description' => 'Alora Meet - Video conferencing and collaboration tool.',
        'logo_file'   => 'alorameet-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 27
    [
        'name'        => 'Alora Voice',
        'domain'      => 'aloravoice.com',
        'description' => 'Alora Voice - VoIP and voice communication services.',
        'logo_file'   => 'alora-voice-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 28
    [
        'name'        => 'Alora Chat',
        'domain'      => 'alorachat.com',
        'description' => 'Alora Chat - Team messaging and collaboration platform.',
        'logo_file'   => 'alorachat-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 29
    [
        'name'        => 'Alora Connect',
        'domain'      => 'aloraconnect.com',
        'description' => 'Alora Connect - Unified business connectivity platform.',
        'logo_file'   => 'aloraconnect-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 30
    [
        'name'        => 'Alora Web Design',
        'domain'      => 'alorawebdesign.com',
        'description' => 'Alora Web Design - Professional web design and development.',
        'logo_file'   => 'alora-webdesign-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 31
    [
        'name'        => 'Cloud App Hosting',
        'domain'      => 'cloudapphosting.com',
        'description' => 'Cloud App Hosting - Managed cloud application hosting.',
        'logo_file'   => 'cloud-app-hosting-logo.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 32
    [
        'name'        => 'Virtue Works',
        'domain'      => 'virtue.works',
        'description' => 'Virtue.works - Business solutions and managed services.',
        'logo_file'   => 'virtue-logo.png',
        'logo_ext'    => 'png',
        'social'      => [],
    ],
    // 33
    [
        'name'        => 'Rising Sun Dojo',
        'domain'      => 'rising-sun-dojo.com',
        'description' => 'Rising Sun Dojo - Martial arts training and sports.',
        'logo_file'   => 'rising-sun-dojo-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/rsma.dojo/'],
        ],
    ],
    // 34
    [
        'name'        => 'Alpha Net Ghana',
        'domain'      => 'alphanetghana.com',
        'description' => 'Alpha Net Ghana - Technology services in Ghana.',
        'logo_file'   => 'alphanet-ghana-logo.png',
        'logo_ext'    => 'png',
        'social'      => [],
    ],
    // 35
    [
        'name'        => 'Sonakania Madrasah & Eatim Khana',
        'domain'      => '',
        'description' => 'Sonakania Madrasah & Eatim Khana - Islamic education and orphanage.',
        'logo_file'   => 'sonakania-madrasha.jpg',
        'logo_ext'    => 'jpg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/Sonakaniamadrasah/'],
        ],
    ],
    // 36
    [
        'name'        => 'Hosting Summit',
        'domain'      => 'hostingsummit.org',
        'description' => 'Hosting Summit - Bangladesh web hosting industry summit and conference.',
        'logo_file'   => 'hosting-summit-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/HostingSummitBangladesh'],
        ],
    ],
    // 37
    [
        'name'        => 'Career Development Program',
        'domain'      => 'careerseminar.org',
        'description' => 'Career Development Program - Professional career development and seminar programs.',
        'logo_file'   => 'career-development-program-logo.svg',
        'logo_ext'    => 'svg',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/CareerDevelopmentProgrambd'],
            ['platform' => 'linkedin', 'icon' => 'ri-linkedin-fill', 'url' => 'https://www.linkedin.com/company/career-development-program/'],
            ['platform' => 'youtube',  'icon' => 'ri-youtube-fill',  'url' => 'https://www.youtube.com/@Career_Development_Program'],
        ],
    ],
    // 38
    [
        'name'        => 'Alpha Net Online Shop',
        'domain'      => 'shop.alpha.net.bd',
        'description' => 'Alpha Net Online Shop - Official Alpha Net online store.',
        'logo_file'   => 'shop-logo.png',
        'logo_ext'    => 'png',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/shop.alpha.net.bd'],
        ],
    ],
    // 39
    [
        'name'        => 'cdn.bd',
        'domain'      => 'cdn.bd',
        'description' => 'cdn.bd - Content delivery network services.',
        'logo_file'   => 'cdnlogo.png',
        'logo_ext'    => 'png',
        'social'      => [],
    ],
    // 40
    [
        'name'        => 'colocation.bd',
        'domain'      => 'colocation.bd',
        'description' => 'colocation.bd - Server colocation services in Bangladesh.',
        'logo_file'   => 'colocationbd.svg',
        'logo_ext'    => 'svg',
        'social'      => [],
    ],
    // 41
    [
        'name'        => 'Ecard.com.bd',
        'domain'      => 'ecard.com.bd',
        'description' => 'Ecard.com.bd - Digital e-card and greeting card services.',
        'logo_file'   => 'ecard-logo.png',
        'logo_ext'    => 'png',
        'social' => [
            ['platform' => 'facebook', 'icon' => 'ri-facebook-fill', 'url' => 'https://www.facebook.com/ecard.com.bd/'],
        ],
    ],
    // 42
    [
        'name'        => 'Passgen App',
        'domain'      => 'passgenapp.com',
        'description' => 'Passgen App - Secure password generator application.',
        'logo_file'   => 'passgen-logo.png',
        'logo_ext'    => 'png',
        'social'      => [],
    ],
    // 43
    [
        'name'        => 'Free QR Code',
        'domain'      => 'freeqrcodes.link',
        'description' => 'Free QR Code - Free QR code generator service.',
        'logo_file'   => 'freeqrcodes.png',
        'logo_ext'    => 'png',
        'social'      => [],
    ],
    // 44
    [
        'name'        => 'VMX Link',
        'domain'      => 'vmx.link',
        'description' => 'VMX Link - Link management and shortening service.',
        'logo_file'   => 'vmxlink-logo.png',
        'logo_ext'    => 'png',
        'social'      => [],
    ],
];

// -------------------------------------------------------
// Run
// -------------------------------------------------------
echo "\n=== Alpha Net Brands Seeder (44 brands from tree.html) ===\n";
echo "Total brands to seed: " . count($brands) . "\n\n";

$inserted = 0;
$skipped  = 0;

foreach ($brands as $i => $brand) {
    $num = $i + 1;
    echo "[{$num}] Processing: {$brand['name']}...\n";

    $slug = makeUniqueSlug($pdo, $brand['name']);

    // Try to download actual brand logo from alpha.net.bd
    $avatarPath = null;
    $ext         = $brand['logo_ext'] ?? 'svg';
    $logoFilename = $slug . '.' . $ext;

    if (!empty($brand['logo_file'])) {
        $logoUrl = $brandLogoBase . $brand['logo_file'];
        echo "     Downloading logo: {$logoUrl}\n";
        $result = downloadLogo($logoUrl, $logoBaseDir, $logoFilename);
        if ($result) {
            $avatarPath = 'companies/avatar/' . $result;
            echo "     ✓ Logo saved\n";
        } else {
            echo "     ✗ Failed to download brand logo\n";
        }
    }

    // Fallback: Google favicon (only for brands with a domain)
    if (!$avatarPath && !empty($brand['domain'])) {
        $faviconUrl      = sprintf($faviconBase, urlencode($brand['domain']));
        $faviconFilename = $slug . '.png';
        $result = downloadLogo($faviconUrl, $logoBaseDir, $faviconFilename);
        if ($result) {
            $avatarPath = 'companies/avatar/' . $result;
            echo "     ✓ Favicon saved as logo fallback\n";
        } else {
            echo "     ✗ No logo available\n";
        }
    }

    // Build social links JSON
    $socialLinks = [];
    foreach ($brand['social'] as $s) {
        $socialLinks[] = [
            'platform' => $s['platform'],
            'icon'     => $s['icon'],
            'url'      => $s['url'],
        ];
    }

    $data = [
        'name'         => $brand['name'],
        'slug'         => $slug,
        'domain'       => $brand['domain'] ?? '',
        'description'  => $brand['description'] ?? '',
        'social_links' => json_encode($socialLinks),
    ];
    if ($avatarPath) $data['avatar_image_path'] = $avatarPath;

    $id = insertCompany($pdo, $data, $now);
    if ($id) {
        echo "     ✓ Inserted with id={$id}\n";
        $inserted++;
    } else {
        $skipped++;
    }

    // Create uploads dir for the brand
    $uploadsDir = BASE_PATH . '/storage/uploads/' . $slug;
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);

    echo "\n";
}

echo "=== Done! ===\n";
echo "Inserted: {$inserted}\n";
echo "Skipped (already existed): {$skipped}\n";
