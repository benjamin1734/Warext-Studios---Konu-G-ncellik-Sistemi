<?php

$root = dirname(__DIR__) . '/upload/src/addons/WarextStudios/ThreadFreshness';
$output = $root . '/_output';
$dataDir = $root . '/_data';

if (!is_dir($output))
{
    fwrite(STDERR, "Development output bulunamadı.\n");
    exit(1);
}

if (!is_dir($dataDir) && !mkdir($dataDir, 0777, true) && !is_dir($dataDir))
{
    fwrite(STDERR, "Data dizini oluşturulamadı.\n");
    exit(1);
}

function wrxtFiles(string $dir, string $extension): array
{
    if (!is_dir($dir))
    {
        return [];
    }

    $files = glob($dir . '/*.' . $extension) ?: [];
    return array_values(array_filter($files, static function(string $file): bool
    {
        return basename($file)[0] !== '_';
    }));
}

function wrxtJson(string $file): array
{
    $data = json_decode((string)file_get_contents($file), true);
    if (!is_array($data))
    {
        throw new RuntimeException('Geçersiz JSON: ' . $file);
    }

    return $data;
}

function wrxtDoc(string $root): array
{
    $doc = new DOMDocument('1.0', 'utf-8');
    $doc->formatOutput = true;
    $node = $doc->createElement($root);
    $doc->appendChild($node);

    return [$doc, $node];
}

function wrxtAttr(DOMElement $node, string $name, mixed $value): void
{
    if (is_bool($value))
    {
        $value = $value ? '1' : '0';
    }

    $node->setAttribute($name, (string)$value);
}

function wrxtText(DOMDocument $doc, DOMElement $parent, string $name, string $value): void
{
    $node = $doc->createElement($name);
    $node->appendChild($doc->createTextNode($value));
    $parent->appendChild($node);
}

function wrxtSave(DOMDocument $doc, string $path): void
{
    if ($doc->save($path) === false)
    {
        throw new RuntimeException('XML yazılamadı: ' . $path);
    }
}

[$doc, $rootNode] = wrxtDoc('class_extensions');
foreach (wrxtFiles($output . '/class_extensions', 'json') as $file)
{
    $row = wrxtJson($file);
    $node = $doc->createElement('extension');
    wrxtAttr($node, 'from_class', $row['from_class']);
    wrxtAttr($node, 'to_class', $row['to_class']);
    wrxtAttr($node, 'execute_order', $row['execute_order'] ?? 10);
    wrxtAttr($node, 'active', $row['active'] ?? true);
    $rootNode->appendChild($node);
}
wrxtSave($doc, $dataDir . '/class_extensions.xml');

[$doc, $rootNode] = wrxtDoc('cron');
foreach (wrxtFiles($output . '/cron_entries', 'json') as $file)
{
    $row = wrxtJson($file);
    $node = $doc->createElement('entry');
    wrxtAttr($node, 'entry_id', pathinfo($file, PATHINFO_FILENAME));
    wrxtAttr($node, 'cron_class', $row['cron_class']);
    wrxtAttr($node, 'cron_method', $row['cron_method']);
    wrxtAttr($node, 'active', $row['active'] ?? true);
    $node->appendChild($doc->createCDATASection(json_encode($row['run_rules'] ?? [], JSON_UNESCAPED_SLASHES)));
    $rootNode->appendChild($node);
}
wrxtSave($doc, $dataDir . '/cron.xml');

[$doc, $rootNode] = wrxtDoc('option_groups');
foreach (wrxtFiles($output . '/option_groups', 'json') as $file)
{
    $row = wrxtJson($file);
    $node = $doc->createElement('group');
    wrxtAttr($node, 'group_id', pathinfo($file, PATHINFO_FILENAME));
    wrxtAttr($node, 'icon', $row['icon'] ?? '');
    wrxtAttr($node, 'display_order', $row['display_order'] ?? 0);
    wrxtAttr($node, 'debug_only', $row['debug_only'] ?? false);
    $rootNode->appendChild($node);
}
wrxtSave($doc, $dataDir . '/option_groups.xml');

[$doc, $rootNode] = wrxtDoc('options');
$typeMap = [
    'uint' => 'unsigned_integer',
    'int' => 'integer',
    'bool' => 'boolean',
    'boolean' => 'boolean',
    'str' => 'string'
];
foreach (wrxtFiles($output . '/options', 'json') as $file)
{
    $row = wrxtJson($file);
    $node = $doc->createElement('option');
    wrxtAttr($node, 'option_id', pathinfo($file, PATHINFO_FILENAME));
    wrxtAttr($node, 'edit_format', $row['edit_format'] ?? 'textbox');
    wrxtAttr($node, 'data_type', $typeMap[$row['data_type'] ?? 'string'] ?? ($row['data_type'] ?? 'string'));
    wrxtAttr($node, 'advanced', $row['advanced'] ?? false);

    if (!empty($row['validation_class']))
    {
        wrxtAttr($node, 'validation_class', $row['validation_class']);
    }
    if (!empty($row['validation_method']))
    {
        wrxtAttr($node, 'validation_method', $row['validation_method']);
    }

    wrxtText($doc, $node, 'default_value', (string)($row['default_value'] ?? ''));

    if (($row['edit_format_params'] ?? '') !== '')
    {
        wrxtText($doc, $node, 'edit_format_params', (string)$row['edit_format_params']);
    }

    if (!empty($row['sub_options']))
    {
        wrxtText($doc, $node, 'sub_options', implode("\n", $row['sub_options']));
    }

    foreach (($row['relations'] ?? []) as $groupId => $displayOrder)
    {
        $relation = $doc->createElement('relation');
        wrxtAttr($relation, 'group_id', $groupId);
        wrxtAttr($relation, 'display_order', $displayOrder);
        $node->appendChild($relation);
    }

    $rootNode->appendChild($node);
}
wrxtSave($doc, $dataDir . '/options.xml');

[$doc, $rootNode] = wrxtDoc('permissions');
foreach (wrxtFiles($output . '/permissions', 'json') as $file)
{
    $row = wrxtJson($file);
    $id = pathinfo($file, PATHINFO_FILENAME);
    $parts = explode('-', $id, 2);
    if (count($parts) !== 2)
    {
        throw new RuntimeException('Geçersiz permission dosya adı: ' . $file);
    }

    $node = $doc->createElement('permission');
    wrxtAttr($node, 'permission_group_id', $parts[0]);
    wrxtAttr($node, 'permission_id', $parts[1]);
    wrxtAttr($node, 'permission_type', $row['permission_type'] ?? 'flag');
    if (($row['depend_permission_id'] ?? '') !== '')
    {
        wrxtAttr($node, 'depend_permission_id', $row['depend_permission_id']);
    }
    wrxtAttr($node, 'interface_group_id', $row['interface_group_id'] ?? 'generalPermissions');
    wrxtAttr($node, 'display_order', $row['display_order'] ?? 0);
    $rootNode->appendChild($node);
}
wrxtSave($doc, $dataDir . '/permissions.xml');

[$doc, $rootNode] = wrxtDoc('phrases');
foreach (wrxtFiles($output . '/phrases', 'txt') as $file)
{
    $node = $doc->createElement('phrase');
    wrxtAttr($node, 'title', pathinfo($file, PATHINFO_FILENAME));
    wrxtAttr($node, 'version_id', 1000070);
    wrxtAttr($node, 'version_string', '1.0.0');
    $node->appendChild($doc->createCDATASection((string)file_get_contents($file)));
    $rootNode->appendChild($node);
}
wrxtSave($doc, $dataDir . '/phrases.xml');

[$doc, $rootNode] = wrxtDoc('routes');
foreach (wrxtFiles($output . '/routes', 'json') as $file)
{
    $row = wrxtJson($file);
    $node = $doc->createElement('route');
    foreach (['route_type', 'route_prefix', 'sub_name', 'format', 'build_class', 'build_method', 'controller', 'context', 'action_prefix'] as $key)
    {
        if (($row[$key] ?? '') !== '')
        {
            wrxtAttr($node, $key, $row[$key]);
        }
    }
    $rootNode->appendChild($node);
}
wrxtSave($doc, $dataDir . '/routes.xml');

[$doc, $rootNode] = wrxtDoc('admin_navigation');
foreach (wrxtFiles($output . '/admin_navigation', 'json') as $file)
{
    $row = wrxtJson($file);
    $node = $doc->createElement('admin_navigation_entry');
    wrxtAttr($node, 'navigation_id', pathinfo($file, PATHINFO_FILENAME));
    if (($row['parent_navigation_id'] ?? '') !== '')
    {
        wrxtAttr($node, 'parent_navigation_id', $row['parent_navigation_id']);
    }
    wrxtAttr($node, 'display_order', $row['display_order'] ?? 0);
    wrxtAttr($node, 'link', $row['link'] ?? '');
    wrxtAttr($node, 'icon', $row['icon'] ?? '');
    if (($row['admin_permission_id'] ?? '') !== '')
    {
        wrxtAttr($node, 'admin_permission_id', $row['admin_permission_id']);
    }
    wrxtAttr($node, 'debug_only', $row['debug_only'] ?? false);
    wrxtAttr($node, 'development_only', $row['development_only'] ?? false);
    wrxtAttr($node, 'hide_no_children', $row['hide_no_children'] ?? false);
    $rootNode->appendChild($node);
}
wrxtSave($doc, $dataDir . '/admin_navigation.xml');

[$doc, $rootNode] = wrxtDoc('template_modifications');
foreach (['admin', 'public'] as $type)
{
    foreach (wrxtFiles($output . '/template_modifications/' . $type, 'json') as $file)
    {
        $row = wrxtJson($file);
        $node = $doc->createElement('modification');
        wrxtAttr($node, 'type', $type);
        wrxtAttr($node, 'template', $row['template']);
        wrxtAttr($node, 'modification_key', pathinfo($file, PATHINFO_FILENAME));
        wrxtAttr($node, 'description', $row['description'] ?? '');
        wrxtAttr($node, 'execution_order', $row['execution_order'] ?? 10);
        wrxtAttr($node, 'enabled', $row['enabled'] ?? true);
        wrxtAttr($node, 'action', $row['action'] ?? 'str_replace');

        $find = $doc->createElement('find');
        $find->appendChild($doc->createCDATASection((string)($row['find'] ?? '')));
        $node->appendChild($find);

        $replace = $doc->createElement('replace');
        $replace->appendChild($doc->createCDATASection((string)($row['replace'] ?? '')));
        $node->appendChild($replace);

        $rootNode->appendChild($node);
    }
}
wrxtSave($doc, $dataDir . '/template_modifications.xml');

[$doc, $rootNode] = wrxtDoc('templates');
foreach (['admin', 'public'] as $type)
{
    foreach (wrxtFiles($output . '/templates/' . $type, 'html') as $file)
    {
        $node = $doc->createElement('template');
        wrxtAttr($node, 'type', $type);
        wrxtAttr($node, 'title', pathinfo($file, PATHINFO_FILENAME));
        wrxtAttr($node, 'version_id', 1000070);
        wrxtAttr($node, 'version_string', '1.0.0');
        $node->appendChild($doc->createCDATASection((string)file_get_contents($file)));
        $rootNode->appendChild($node);
    }
}
wrxtSave($doc, $dataDir . '/templates.xml');

echo "OK\n";
