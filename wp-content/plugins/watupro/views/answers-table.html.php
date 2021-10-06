<div id="WatuPROanswersTableArea" class="watupro-answers-table-area">

	<table class="watupro-answers-table" id="WatuPROanswersTable">
		<?php foreach($answers as $cnt => $answer):
			$cnt++;
			// define decade class			
			$decade = ceil($cnt / $per_decade);
			$decade_class = 'answers-decade-' . $decade;	?>	
			<tr class="<?php echo $decade_class;?>" <?php if($media == 'screen' and $cnt > $per_decade):?>style="display:none;"<?php endif;?>>
				<td width="75%"><?php echo $cnt .'. '.WTPQuestion :: summary($answer, false, false);?></td>
				<td width="5%" align="center">
					<img src="<?php echo WATUPRO_URL . ($answer->is_correct ? 'correct.gif' : 'wrong.gif');?>" alt="<?php echo $answer->is_correct ? 'Correct' : 'Wrong';?>">
				</td>
				<?php if($media == 'screen'):?><td width="20%"><a href="#" onclick="jQuery('#WatuPROanswerTableDetails<?php echo $answer->ID?>').show();jQuery('#WatuPROanswersTable').hide();return false;"><?php _e('Review', 'watupro');?></a></td><?php endif;?>
			</tr>
		<?php endforeach;?>
		<?php if($media == 'screen' and $num_answers > $per_decade):?>
		<tr><td colspan="3">
				<a href="#" id="WatuPROanswersTablePrevLink" onclick="WatuPRO.paginateAnswersTable('prev', <?php echo $num_answers?>, <?php echo $per_decade;?>);return false;" style="display: none;">&lt;&lt;Previous</a>
				&nbsp;
				<?php for($i = 1; $i <= $num_decades; $i++):
					if($i > 1) echo ' | ';?>
					<a href="#" onclick="WatuPRO.paginateAnswersTable(<?php echo $i?>, <?php echo $num_answers?>, <?php echo $per_decade;?>);return false;"><?php echo $i;?></a>
				<?php endfor;?>
				&nbsp;
				<a href="#" id="WatuPROanswersTableNextLink" onclick="WatuPRO.paginateAnswersTable('next', <?php echo $num_answers?>, <?php echo $per_decade;?>);return false;">Next &gt;&gt;</a>		
		</td></tr>
		<?php endif;?>
	</table>
	
	<?php if($media == 'screen'):
	 foreach($answers as $answer):?>
		<div id="WatuPROanswerTableDetails<?php echo $answer->ID?>" style="display: none;">
			<div><?php $matches = array();
				preg_match_all("/{{{([^}}}])*}}}/", $answer->snapshot, $matches);	
				foreach($matches[0] as $cnt => $match) {		
					$answer->snapshot = str_replace($match, '_____', $answer->snapshot);			
				} 
				echo $answer->snapshot?></div>
			
			<p align="center"><input type="button" value="<?php _e('Close Review', 'watupro');?>" onclick="jQuery('#WatuPROanswerTableDetails<?php echo $answer->ID?>').hide();jQuery('#WatuPROanswersTable').show();"></p>
		</div>
	<?php endforeach;
	endif; // end if media = screen?>
	
</div>