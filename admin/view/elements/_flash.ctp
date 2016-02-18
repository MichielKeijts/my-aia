<?php
/**
 * Display Flash messages set in View::$_flash_messages
 */
?>
	<div id="flash-messages-holder">
<?php
	$i=0;
	foreach ($this->_flash_messages as $message) {
		foreach ($message as $type=>$content) {
			switch ($type) {
				case "error":
					$class = "error";
					$icon = "dashicons-dismiss";
					break;
				case "notice":
					$class="notice";
					$icon = "dashicons-warning";
					break;
				case "success":
					$class="success";
					$icon = "dashicons-yes";
					break;
				case "info":
				default:
					$class="info";
					$icon = "dashicons-flag";
	}
?>
		<div class="message <?= $class; ?>">
			<div class="btns">
				<button class='button-small btn up'><i class='dashicons rtmicon rtmicon-up-circled2'></i></button>
				<button class='button-small btn down'><i class='dashicons rtmicon rtmicon-down-circled2'></i></button>
				<div class='counter'><?= sprintf('(%d/%d)', ++$i, count($this->_flash_messages)); ?></div>
			</div>
			<i class="dashicons <?= $class; ?> <?= $icon; ?>"></i> <?= $content; ?>
		</div>
		<?php
		}
	}
?>
	</div>