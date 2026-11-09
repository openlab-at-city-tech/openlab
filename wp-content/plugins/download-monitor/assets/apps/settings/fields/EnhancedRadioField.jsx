import {
	BaseControl,
	ToggleGroupControl as StableToggleGroupControl,
	ToggleGroupControlOption as StableToggleGroupControlOption,
	__experimentalToggleGroupControl,
	__experimentalToggleGroupControlOption,
} from '@wordpress/components';
import htmlHelp from './htmlHelp';

const ToggleGroupControl = StableToggleGroupControl || __experimentalToggleGroupControl;
const ToggleGroupControlOption = StableToggleGroupControlOption || __experimentalToggleGroupControlOption;

export default function EnhancedRadioField( { field, value, onChange, disabled } ) {
	return (
		<BaseControl label={ field.label } help={ htmlHelp( field.desc ) }>
			<ToggleGroupControl
				value={ value || field.std || '' }
				isBlock
				disabled={ disabled }
				onChange={ onChange }
				__nextHasNoMarginBottom
			>
				{ Object.entries( field.options || {} ).map( ( [ optValue, label ] ) => (
					<ToggleGroupControlOption key={ optValue } value={ optValue } label={ label } />
				) ) }
			</ToggleGroupControl>
		</BaseControl>
	);
}
