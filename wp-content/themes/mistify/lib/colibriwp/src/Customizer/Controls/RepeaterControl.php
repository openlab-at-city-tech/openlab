<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Core\Utils;
use ColibriWP\Theme\Translations;

class RepeaterControl extends VueControl {

	public $type    = 'colibri-repeater';
	private $fields = array();


	public static function sanitize( $value, $control_data, $default = '' ) {
		return Utils::sanitizeEscapedJSON( $value );
	}


	protected function printVueContent() {
		?>

		<div class="colibri-fullwidth">
			<div class="colibri-fullwidth">
				<el-collapse v-sortable-el-accordion="onSortEnd">

					<el-collapse-item v-for="(item,index) in items" :name="index" :key="item.index">

						<template slot="title">
							<?php $this->vueEcho( 'itemsLabels[index]' ); ?>
						</template>

						<ul class="field-data">
							<li v-for="(field,name) in fields" :key="name">
								<label class="field-label"><?php $this->vueEcho( 'field.label' ); ?></label>
								<div class="component-holder"
									 :is="getComponentType(field.type)"
									 :value="item[name]"
									 v-bind="field.props"
									 @change="propChanged($event,item,name)"></div>
							</li>
						</ul>

						<el-button class="el-button--danger" type="text" v-show="canRemoveItem"
								   @click="removeItem(index)"><?php Translations::escHtmlE( 'remove' ); ?></el-button>

					</el-collapse-item>

				</el-collapse>
			</div>

			<div class="colibri-fullwidth">
				<el-button size="medium" v-show="canAdd"
						   @click="addItem()"><?php $this->vueEcho( 'item_add_label' ); ?></el-button>
			</div>
		</div>
		<?php
	}

}
