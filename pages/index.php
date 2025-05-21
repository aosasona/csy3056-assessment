<?php

declare(strict_types=1);

use Burrow\Lobsters;


require_once __DIR__ . '/../vendor/autoload.php';

$page = (int)($_GET['page'] ?? 1);
$data = Lobsters::shared()->getActiveItems($page);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/public/style.css">
	<title>Burrow</title>
</head>

<body class="container">
	<h1>Burrow</h1>
	<?php foreach ($data as $i => $item): ?>
		<div class="item">
			<p class="number">
				<?= ($i + 1) + (($page - 1) * count($data)) ?>
			</p>
			<div class="w-full flex flex-col gap-1">
				<a href="/<?= $item->shortId ?>"><?= $item->title ?></a>

				<div class="flex align-center justify-between gap-1 flex-wrap">
					<div class="flex align-center gap-1 flex-wrap">
						<p class="text-muted small"><?= $item->score ?> points</p>
						<p class="text-muted small">&bull;</p>
						<p class="text-muted small"><?= $item->commentCount ?> comments</p>
						<p class="text-muted small">&bull;</p>
						<p class="text-muted small"><?= $item->submitterUser ?></p>
						<p class="text-muted small">&bull;</p>
						<p class="text-muted small"><?= DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $item->createdAt)->format('F j, Y G:i') ?></p>
					</div>


					<div class="flex align-center gap-2 flex-wrap">
						<?php foreach ($item->tags as $tag): ?>
							<a href="/tag/<?= $tag ?>" class="tag"><?= $tag ?></a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>

	<div class="pagination">
		<?php if ($page > 1): ?>
			<a href="/?page=<?= $page - 1 ?>">&laquo; Previous</a>
		<?php endif; ?>
		<div class="page-info">
			<span><?= $page ?></span>
		</div>
		<a href="/?page=<?= $page + 1 ?>">Next &raquo;</a>
	</div>
</body>

</html>
