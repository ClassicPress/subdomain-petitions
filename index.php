<?php

$path = strtok($_SERVER['REQUEST_URI'], '?');
$qs = '?' . strtok('?');
if ($qs === '?') $qs = '';
$path_canonical = '/' . trim($path, '/');

$petition_file = null;
if (preg_match('#^/posts/(\d+)(/|$)#', $path_canonical, $matches)) {
	$petition_id = $matches[1];
	$slugs = json_decode(file_get_contents(__DIR__ . '/slugs.json'), true);
	$petition_slug = $slugs[$petition_id] ?? null;
	if ($petition_slug) {
		$petition_slug = $slugs[$petition_id];
		$path_canonical = "/posts/$petition_id/$petition_slug";
		$petition_file = __DIR__ . "/petitions/$petition_id.html";
	}
} else if ($path === '/posts') {
	$path_canonical = '/';
}

if ($path !== $path_canonical) {
	header('HTTP/1.1 302 Found');
	header('Location: ' . $path_canonical . $qs);
	die();
}

if ($path === '/') {
	$view = $_GET['view'] ?? 'most-wanted';
	switch ($view) {
		case 'most-wanted':
			readfile(__DIR__ . '/index-mostwanted-120.html');
			break;
		case 'trending':
			readfile(__DIR__ . '/index-trending-120.html');
			break;
		case 'most-discussed':
			readfile(__DIR__ . '/index-mostdiscussed-120.html');
			break;
		case 'recent':
			readfile(__DIR__ . '/index-recent-120.html');
			break;
		case 'completed':
			readfile(__DIR__ . '/index-completed.html');
			break;
		case 'planned':
			readfile(__DIR__ . '/index-planned.html');
			break;
		case 'declined':
			readfile(__DIR__ . '/index-declined.html');
			break;
		default:
			header('HTTP/1.1 404 Not Found');
			readfile(__DIR__ . '/404.html');
			break;
	}
	die();
} else if ($petition_file) {
	readfile($petition_file);
} else {
	header('HTTP/1.1 404 Not Found');
	readfile(__DIR__ . '/404.html');
}
