<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php if($proverb->type == 'sanonta'): ?>
"<?=$proverb->what?>", <?=$proverb->how?> <?=$proverb->who?> <?=$proverb->when?>.
<?php else: ?>
"<?=$proverb->what?>" - <?=$proverb->who?>
<?php endif?>

