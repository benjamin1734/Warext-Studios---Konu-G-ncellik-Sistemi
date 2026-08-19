<?php

$root = __DIR__ . '/../upload/src/addons/WarextStudios/ThreadFreshness';
$required = [
    'class_extensions.xml',
    'cron.xml',
    'option_groups.xml',
    'options.xml',
    'permissions.xml',
    'phrases.xml',
    'routes.xml',
    'admin_navigation.xml',
    'template_modifications.xml',
    'templates.xml'
];

foreach ($required as $file)
{
    $path = $root . '/_data/' . $file;
    if (!is_file($path))
    {
        fwrite(STDERR, "Eksik data dosyası: $file\n");
        exit(1);
    }

    libxml_use_internal_errors(true);
    if (simplexml_load_file($path) === false)
    {
        fwrite(STDERR, "Geçersiz XML: $file\n");
        exit(1);
    }
}

$routes = (string)file_get_contents($root . '/_data/routes.xml');
foreach (['thread-freshness', 'guncel-cozumler'] as $needle)
{
    if (strpos($routes, $needle) === false)
    {
        fwrite(STDERR, "Route eksik: $needle\n");
        exit(1);
    }
}

$classExtensions = (string)file_get_contents($root . '/_data/class_extensions.xml');
if (strpos($classExtensions, 'WarextStudios\\ThreadFreshness\\XF\\Entity\\Thread') === false)
{
    fwrite(STDERR, "Thread class extension eksik\n");
    exit(1);
}

$permissions = (string)file_get_contents($root . '/_data/permissions.xml');
foreach (['vote', 'changeVote', 'voteOwn', 'moderate'] as $needle)
{
    if (strpos($permissions, 'permission_id="' . $needle . '"') === false)
    {
        fwrite(STDERR, "Permission eksik: $needle\n");
        exit(1);
    }
}

echo "OK\n";
