<?php

$root = __DIR__ . '/../upload/src/addons/WarextStudios/ThreadFreshness';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$forbiddenFunctions = ['eval', 'exec', 'shell_exec', 'system', 'passthru', 'proc_open', 'popen'];

foreach ($iterator as $file)
{
    if (!$file->isFile() || $file->getExtension() !== 'php')
    {
        continue;
    }

    $source = (string)file_get_contents($file->getPathname());
    $tokens = token_get_all($source);

    foreach ($tokens as $token)
    {
        if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true))
        {
            fwrite(STDERR, "Yorum bulundu: {$file->getPathname()}\n");
            exit(1);
        }
    }

    foreach ($forbiddenFunctions as $function)
    {
        if (preg_match('/\b' . preg_quote($function, '/') . '\s*\(/i', $source))
        {
            fwrite(STDERR, "Yasak fonksiyon bulundu: $function\n");
            exit(1);
        }
    }
}

$threadController = (string)file_get_contents(
    $root . '/XF/Pub/Controller/Thread.php'
);

foreach (['actionFreshnessVote', 'actionFreshnessModerate', 'actionFreshnessReplacement'] as $action)
{
    $position = strpos($threadController, 'function ' . $action);
    if ($position === false)
    {
        fwrite(STDERR, "Action bulunamadı: $action\n");
        exit(1);
    }

    $section = substr($threadController, $position, 500);
    if (strpos($section, 'assertPostOnly') === false)
    {
        fwrite(STDERR, "POST koruması bulunamadı: $action\n");
        exit(1);
    }
}

$setup = (string)file_get_contents($root . '/Setup.php');
foreach ([
    "addUniqueKey(['thread_id', 'user_id'], 'thread_user')",
    "addKey(['thread_id', 'version'], 'thread_version')",
    "addKey('replacement_thread_id')"
] as $needle)
{
    if (strpos($setup, $needle) === false)
    {
        fwrite(STDERR, "Şema güvenlik kontrolü başarısız: $needle\n");
        exit(1);
    }
}

$repository = (string)file_get_contents($root . '/Repository/ThreadFreshness.php');
foreach (['FOR UPDATE', 'beginTransaction', 'rollback', 'preloadStatesForThreads'] as $needle)
{
    if (strpos($repository, $needle) === false)
    {
        fwrite(STDERR, "Repository güvenlik kontrolü başarısız: $needle\n");
        exit(1);
    }
}

echo "OK\n";
