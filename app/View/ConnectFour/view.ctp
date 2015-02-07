<?php
if (!isset($settings['player'])) {
	return;
}

$player = $settings['player'];
$colors = array(
	'1' => 'red',
	'2' => 'blue'
);
$activeColor = $colors[$player];

$jsCheckPosition = json_encode(Router::url(array('controller' => 'boards', 'action' => 'ajax_check')));

$scriptBlock = $this->Html->scriptBlock(
<<<JS
$(function() {

	var pusher = new Pusher('0ac7b4b7f07ac4ecbd02');
	var channel = pusher.subscribe('connect_four_channel');

	channel.bind('turn_'+ {$player} + '_event', function(data) {
		alert(data.message);
		window.location.reload();
	});

	$('.col').click(function() {
		if ($(this).text() != 0) {
			return;
		}

		var id = $(this).attr('id'),
			row = id.substr(0, 1),
			col = id.substr(1,2);

		var data = {
			'row': row,
			'col': col,
			'player': {$player}
		};

		$.ajax({
			type: 'POST',
			url: {$jsCheckPosition},
			data: data,
			success: function(response) {
				$('#' + response['rowSaved'] + col).addClass('{$activeColor}');
				$('#' + response['rowSaved'] + col).text({$player});
				if (response['win'] === true) {
					alert('FINISH');
				}
			},
			complete: function(jqXHR, textStatus) {
				if (textStatus == 'success') {
					window.location.reload();
				}
			}
		});
	});
})
JS
);
?>
<?=$scriptBlock?>
<?=$this->Html->css('connect_four/view')?>
<script src="//js.pusher.com/2.2/pusher.min.js" type="text/javascript"></script>
<script type="text/javascript">
// Enable pusher logging - don't include this in production
Pusher.log = function(message) {
	if (window.console && window.console.log) {
		window.console.log(message);
	}
};


</script>
<h1><?=__('Connect Four')?></h1>
<h2><?=sprintf('Player: %d', $settings['player'])?></h2>
<table>
	<?php foreach ($settings['board'] as $row => $data): ?>
		<tr>
			<?php foreach ($data as $col => $value): ?>
				<?php
					$class = '';
					if (!empty($value)) {
						$class = $colors[$value];
					}
				?>
				<td id='<?=$row . $col?>' class='col <?=$class?>' style="border-style:solid;width:15px;"><?=$value?></td>
				<!-- dont use style in tag, only reason speed -->
			<?php endforeach ?>			
		</tr>
	<?php endforeach ?>
</table>