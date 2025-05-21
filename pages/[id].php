<?php

declare(strict_types=1);

use Burrow\Lobsters;


require_once __DIR__ . '/../vendor/autoload.php';

$id = $ctx->get('id');
$data = Lobsters::shared()->getItemById($id);
$domain = parse_url($data->url, PHP_URL_HOST);

$comments = [];
$children = [];

// Collect all comments and init their children
foreach ($data->comments as $comment) {
	$comments[$comment->shortId] = $comment;
	$children[$comment->shortId] = [];
}

// Another collect all children
foreach ($data->comments as $comment) {
	if ($comment->parentComment) {
		$children[$comment->parentComment][] = $comment->shortId;
	}
}

function render_comments(string $id, array $comments, array $children, int $depth): string
{
	$comment = $comments[$id];
	$margin = $depth * 5;

	// Escape user input to avoid XSS
	$author = htmlspecialchars($comment->commentingUser);
	$content = $comment->comment; // assumed to already be safe HTML already since it is from Lobsters

	// Render children
	$children_html = "";
	foreach ($children[$id] as $childId) {
		$children_html .= render_comments($childId, $comments, $children, $depth + 1);
	}

	$html = <<<HTML
		<div style="margin-left: {$margin}px;" class="comment">
			<a href="https://lobste.rs/~{$author}" target="_blank" class="author"><strong>{$author}</strong></a>
			<div class="content">{$content}</div>
			{$children_html}
		</div>
	HTML;

	return $html;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/public/style.css">
	<title><?= $data->title ?></title>
</head>

<body class="item-page-container">
	<iframe src="<?= $data->url ?>" class="item-iframe" frameborder="0"></iframe>
	<div class="side-container">
		<div class="meta">
			<a href="/" class="navigation">&larr; Home</a>
			<h3><?= $data->title ?></h3>
			<a href="<?= $data->url ?>" target="_blank" rel="noopener noreferrer"><?= $domain ?></a>
			<div class="flex items-center gap-1">
				<p class="text-muted small"><?= $data->score ?> points</p>
				<p class="text-muted small">&bull;</p>
				<p class="text-muted small"><?= $data->commentCount ?> comments</p>
				<p class="text-muted small">&bull;</p>
				<p class="text-muted small"><?= $data->submitterUser ?></p>
			</div>
		</div>
		<div class="comments">
			<?php foreach ($data->comments as $comment): ?>
				<?php if (empty($comment->parentComment)): ?>
					<?= render_comments($comment->shortId, $comments, $children, 0) ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</body>

</html>
