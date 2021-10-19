<?php
// handle timer functions
class WatuPROTimer {
	static function calculate($start_time, &$exam) {
		$limit_in_seconds = intval($exam->time_limit*60);
		$time_elapsed = current_time('timestamp') - $start_time;	

		// time_elapsed is zero if we have adjusted the timer down due to schedule!
		if(!empty($exam->timer_adjusted_by_schedule)) $time_elapsed = 0;		
		
		$new_limit_seconds = $limit_in_seconds - $time_elapsed;
		// echo $new_limit_seconds;
		if($new_limit_seconds < 0) {		
			//unset($in_progress); // unset this so we will submit empty the results 	
			$exam->time_limit = 0.003;
			$timer_warning = __("Warning: your unfinished attempt was recorded. You ran out of time and your answers will be submitted automatically.", 'watupro');	
		}		 	
		else {	
			// echo $new_limit_seconds;		
			$exam->time_limit = round($new_limit_seconds/60, 3);
			// never zero
			if(empty($exam->time_limit)) $exam->time_limit = 0.003;
			$timer_warning = __("Warning: you have started this test earlier and the timer is running behind the scene!", 'watupro');
		}
		
		return $timer_warning;
	}
}