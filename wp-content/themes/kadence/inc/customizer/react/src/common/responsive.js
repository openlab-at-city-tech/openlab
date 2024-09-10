import PropTypes from 'prop-types';
import Icons from './icons';
import { __ } from '@wordpress/i18n';
const {
	Component,
	Fragment
} = wp.element;
const {
	Button,
	Dashicon,
	Tooltip,
	ButtonGroup,
	Icon
} = wp.components;

class ResponsiveControl extends Component {
	constructor(props) {
		super( props );
		this.state = {
			view: undefined !== this.props.deviceSize ? this.props.deviceSize : 'desktop'
		};
		this.linkResponsiveButtons();
	}

	render() {
		let { view } = this.state,
		deviceMap = {
			'desktop': {
				'tooltip': __( 'Desktop', 'kadence' ),
				'icon': <Icon icon={ Icons.desktop }/>
			},
			'tablet': {
				'tooltip': __( 'Tablet', 'kadence' ),
				'icon': <Icon icon={ Icons.tablet }/>
			},
			'mobile': {
				'tooltip': __( 'Mobile', 'kadence' ),
				'icon': <Icon icon={ Icons.smartphone }/>
			}
		};
		return (
				<Fragment>
					<div className={ 'kadence-responsive-control-bar' }>
						{ this.props.controlLabel && (
							<span className="customize-control-title">{ this.props.controlLabel }</span>
						) }
						{
							!this.props.hideResponsive &&
							<div className="floating-controls">
								{ this.props.tooltip && (
									<ButtonGroup>
										{Object.keys( deviceMap ).map( (device) => {
											return (
													<Tooltip text={deviceMap[device].tooltip}>
														<Button
																isTertiary
																className={( device === view ?
																		'active-device ' :
																		'' ) + device}
																onClick={() => {
																	let event = new CustomEvent(
																			'kadenceChangedRepsonsivePreview', {
																				'detail': device
																			} );
																	document.dispatchEvent( event );
																}}
														>
															{ deviceMap[device].icon }
														</Button>
													</Tooltip>
											);
										} )}
									</ButtonGroup>
								) }
								{ ! this.props.tooltip && (
									<ButtonGroup>
										{Object.keys( deviceMap ).map( (device) => {
											return (
												<Button
														isTertiary
														className={( device === view ?
																'active-device ' :
																'' ) + device}
														onClick={() => {
															let event = new CustomEvent(
																	'kadenceChangedRepsonsivePreview', {
																		'detail': device
																	} );
															document.dispatchEvent( event );
														}}
												>
													{ deviceMap[device].icon }
												</Button>
											);
										} )}
									</ButtonGroup>
								) }
							</div>
						}
					</div>
					<div className="kadence-responsive-controls-content">
						{this.props.children}
					</div>
				</Fragment>
		);
	}

	changeViewType(device) {
		this.setState( { view: device } );
		wp.customize.previewedDevice( device );
		this.props.onChange( device );
	}

	linkResponsiveButtons() {
		let self = this;
		document.addEventListener( 'kadenceChangedRepsonsivePreview', function(e) {
			self.changeViewType( e.detail );
		} );
	}
}

ResponsiveControl.propTypes = {
	onChange: PropTypes.func,
	controlLabel: PropTypes.string
};
ResponsiveControl.defaultProps = {
	tooltip: true,
};

export default ResponsiveControl;
