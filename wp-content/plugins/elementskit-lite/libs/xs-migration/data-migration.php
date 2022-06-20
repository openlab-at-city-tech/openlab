<?php

namespace ElementsKit_Lite\Libs\Xs_Migration;

abstract class Data_Migration implements Migration_Contract {


	const SUB_ROUTINE_STATUS_INCOMPLETE = '__incomplete';
	const SUB_ROUTINE_STATUS_DONE       = '__done';


	const GENERIC_STATUS_YES = 'yes';
	const GENERIC_STATUS_NO  = 'no';

	const STATUS_DONE    = 'done';
	const STATUS_QUEUED  = 'queued';
	const STATUS_RUNNING = 'running';

	const STATUS_FINISHED  = 'finished';
	const STATUS_INITIATED = 'initiated';

	const STATUS_METHOD_PAUSED    = 'paused';
	const STATUS_METHOD_EXECUTED  = 'executed';
	const STATUS_METHOD_EXECUTING = 'executing';

	private $new_text_domain = 'elementskit-lite';
	private $text_domain     = 'elementskit-lite';
	private $max_iteration   = 10;


	/**
	 * @param $txtDomain
	 * @param $versionFrom
	 * @param $versionTo
	 *
	 * @return mixed
	 */
	public function input( $txtDomain, $versionFrom, $versionTo ) {

		#$versionFrom = '1.1.9';
		#$versionTo   = '1.2.0';
		$optionKey = 'data_migration_' . $txtDomain . '_log';

		$from = str_replace( '.', '_', trim( $versionFrom ) );
		$to   = str_replace( '.', '_', trim( $versionTo ) );

		$frm = $this->makeFullVersionKey( $from );
		$trm = $this->makeFullVersionKey( $to );

		$existingOption = get_option( $optionKey );

		if ( empty( $existingOption ) ) {

			$log   = array();
			$log[] = 'Migration never has been done for this domain.';
			$log[] = 'Initiating migration for version ' . $versionFrom . ' to ' . $versionTo . ' at ' . date( 'Y-m-d H:i:s' ) . ' .';
			$log[] = 'Scanning migration file for conversion methods.';

			$cStack = $this->getCallStacks( array(), $frm, $trm );

			$fn = array();

			foreach ( $cStack['stack'] as $item ) {

				$fn[ $item ] = self::STATUS_QUEUED;
			}

			$log[] = 'Execution plan prepared.';
			$log[] = 'Execution plan saved into database.';

			$existingOption['_func']   = $fn;
			$existingOption['_log']    = $log;
			$existingOption['_status'] = self::STATUS_INITIATED;

			update_option( $optionKey, $existingOption );

			return array(
				'status' => 'success',
				'log'    => $existingOption,
				'log2'   => $cStack,
			);
		}

		/**
		 * Now we have something saved into database
		 * lets check the status first
		 *
		 */

		if ( $existingOption['_status'] == self::STATUS_FINISHED ) {

			$log = $existingOption['_log'];

			/**
			 * Now we have to check up-to which version this migration is done
			 */

			$up_to = $this->makeFullVersionKey( $existingOption['_last_version_scanned'] );

			if ( $up_to < $trm ) {

				/**
				 * New version released of this plugin
				 * check if anything new need to migrate
				 */

				$cStack = $this->getCallStacks( array(), $frm, $trm );
				$fn     = $existingOption['_func'];

				$log[] = 'A new version update detected.';
				$log[] = 'Scanning for new migration method.';

				$found = false;

				foreach ( $cStack['stack'] as $item ) {

					if ( isset( $fn[ $item ] ) ) {
						continue;
					}

					$fn[ $item ] = self::STATUS_QUEUED;

					$found = true;
				}

				if ( $found ) {

					$log[] = 'New conversion method detected.';
					$log[] = 'Preparing execution plan.';
					$log[] = 'Execution plan saved into database.';

					$existingOption['_func']   = $fn;
					$existingOption['_log']    = $log;
					$existingOption['_status'] = self::STATUS_INITIATED;

					update_option( $optionKey, $existingOption );

					return array(
						'status' => 'success',
						'log'    => $existingOption,
						'log2'   => $cStack,
					);

				} else {

					$log[] = 'No new conversion method detected.';
					$log[] = 'Updating the migration plan as finished for version ' . $versionTo . ' at ' . date( 'Y-m-d H:i:s' ) . '.';

					$existingOption['_func']                 = $fn;
					$existingOption['_log']                  = $log;
					$existingOption['_status']               = self::STATUS_FINISHED;
					$existingOption['_last_version_scanned'] = $versionTo;
					$existingOption['_plan_up_to']           = $trm;

					update_option( $optionKey, $existingOption );

					return array(
						'status' => 'success',
						'log'    => $existingOption,
						'log2'   => $cStack,
					);
				}           
			}

			/**
			 * As status is finished and last scanned version is same as the current plugin version
			 * code execution should not come here
			 * If any case it come to this point we are updating the settings
			 */

			$log[] = 'In no scenario, execution pointer should not come here [something is wrong...].';

			$existingOption['_log']                  = $log;
			$existingOption['_last_version_scanned'] = $versionTo;
			$existingOption['_plan_up_to']           = $trm;

			update_option( $optionKey, $existingOption );

			return array(
				'status' => 'success',
				'log'    => $existingOption,
			);
		}

		/**
		 * At this point status of the execution plan is not finished
		 * lets do the work
		 *
		 */
		$curExecMethod = '';
		$mtdStat       = '';

		foreach ( $existingOption['_func'] as $mtd => $stat ) {

			if ( $stat == self::STATUS_METHOD_EXECUTED ) {
				continue;
			}

			$curExecMethod = $mtd;
			$mtdStat       = $stat;

			break;
		}

		if ( empty( $curExecMethod ) ) {

			/**
			 * All methods has been executed
			 */

			$log = $existingOption['_log'];

			$log[] = 'All conversion method has been executed.';
			$log[] = 'Setting the migration plan as finished for version ' . $versionTo . ' at ' . date( 'Y-m-d H:i:s' ) . '.';

			$existingOption['_log']                  = $log;
			$existingOption['_status']               = self::STATUS_FINISHED;
			$existingOption['_last_version_scanned'] = $versionTo;
			$existingOption['_plan_up_to']           = $trm;

			update_option( $optionKey, $existingOption );

			return array(
				'status' => 'success',
				'log'    => $existingOption,
			);
		}

		/**
		 * We have a conversion method to run whose status is not executed
		 *
		 */

		if ( $mtdStat == self::STATUS_QUEUED ) {

			$log = $existingOption['_log'];

			$log[] = 'Conversion method ' . $curExecMethod . ' entered into queue at ' . date( 'Y-m-d H:i:s' ) . '.';
			$log[] = '- Conversion method ' . $curExecMethod . ' has entered into execution phase at ' . date( 'Y-m-d H:i:s' );

			$fn = $existingOption['_func'];

			$fn[ $curExecMethod ] = self::STATUS_METHOD_EXECUTING;

			$existingOption['_func'] = $fn;
			$existingOption['_log']  = $log;

			update_option( $optionKey, $existingOption );

			return $this->$curExecMethod( $optionKey, $existingOption );
		}

		if ( $mtdStat == self::STATUS_METHOD_EXECUTING ) {

			return array(
				'status' => 'failed',
				'msg'    => 'Another person already initiated the execution.',
				'log'    => $existingOption['_log'],
			);
		}

		if ( $mtdStat == self::STATUS_METHOD_PAUSED ) {

			$log = $existingOption['_log'];

			$log[] = '- Conversion method ' . $curExecMethod . ' has entered into executing phase at ' . date( 'Y-m-d H:i:s' );

			$fn = $existingOption['_func'];

			$fn[ $curExecMethod ] = self::STATUS_METHOD_EXECUTING;

			$existingOption['_func'] = $fn;
			$existingOption['_log']  = $log;

			update_option( $optionKey, $existingOption );

			return $this->$curExecMethod( $optionKey, $existingOption );
		}

		/**
		 * This is the scenario that never ever should occur
		 */
		return array(
			'status' => 'failed',
			'msg'    => 'Overflow',
			'log'    => array(
				'Exiting...data is corrupted.',
			),
		);
	}


	/**
	 *
	 * @param array $data
	 */
	public function output( array $data ) {

		if ( ! empty( $data['option'] ) ) {

			foreach ( $data['option'] as $opKey => $opVal ) {

				update_option( $opKey, $opVal );
			}
		}
	}


	/**
	 *
	 * @param $versionMap
	 * @param $frm
	 * @param $trm
	 *
	 * @return array
	 */
	private function getCallStacks( $versionMap, $frm, $trm ) {

		$callStack         = array();
		$conversionMethods = array();
		$methods           = get_class_methods( $this );

		foreach ( $methods as $method ) {

			if ( substr( $method, 0, 13 ) === 'convert_from_' ) {

				$conversionMethods[] = $method;

				$tmp = str_replace( 'convert_from_', '', $method );
				$tmp = explode( '_to_', $tmp );

				$vl = $this->makeFullVersionKey( $tmp[0] );
				$vh = $this->makeFullVersionKey( $tmp[1] );

				$versionMap[ $vl ] = $tmp[0];
				$versionMap[ $vh ] = $tmp[1];
			}
		}

		ksort( $versionMap );

		foreach ( $versionMap as $k => $v ) {

			if ( $k >= $frm && $k < $trm ) {

				$fnc = '';

				foreach ( $conversionMethods as $conversionMethod ) {

					if ( strpos( $conversionMethod, 'convert_from_' . $v ) !== false ) {

						$fnc = $conversionMethod;

						break;
					}
				}

				if ( ! empty( $fnc ) ) {
					$callStack[] = $fnc;
				}
			}
		}

		return array(
			'map'   => $versionMap,
			'func'  => $conversionMethods,
			'stack' => $callStack,
		);
	}


	/**
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public function makeFullVersionKey( $string ) {

		$fr = explode( '_', $string );

		$frm = array_map(
			function( $item ) {
				return str_pad( $item, 3, '0', STR_PAD_LEFT );
			},
			$fr
		);

		return implode( '', $frm );
	}


	/**
	 * @return string
	 */
	public function getNewTextDomain() {
		return $this->new_text_domain;
	}


	/**
	 * @return string
	 */
	public function getTextDomain() {
		return $this->text_domain;
	}


	/**
	 * @return int
	 */
	public function getMaxIteration() {
		return $this->max_iteration;
	}

}
