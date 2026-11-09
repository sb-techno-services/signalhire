<div class="split"><section class="panel"><h2>Messages</h2>
<?php if (!$messages): ?><p>No messages yet.</p><?php endif; ?>
<?php foreach ($messages as $message): ?>
<article class="message"><h3><?= esc($message['name']) ?></h3><p class="meta"><?= esc($message['sent_at']) ?> UTC</p>
<p class="prose"><?= esc($message['body']) ?></p></article>
<?php endforeach; ?>
<?php formStart('message-send',['id'=>$id]); ?>
<label>Message<textarea name="message" maxlength="4000" rows="4" required></textarea></label><button>Send message</button></form>
</section><aside class="panel"><h2>Status history</h2>
<ol class="timeline"><?php foreach ($timeline as $event): ?><li><strong><?= esc($event['status']) ?></strong>
<span class="meta"><?= esc($event['changed_at']) ?> UTC</span></li><?php endforeach; ?></ol></aside></div>
<?php return 'Application messages';
