const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { SelectControl, TextControl } = wp.components;

class IconListPopup extends Component {

    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        this.state = {
            searchedText: '',
            selectedIcon: '',
            selectedIconTheme: 'outlined',
        }
    }

    componentWillMount() {
        const {searchedText, selectedIconTheme} = this.state;
        if(this.props.selectedIcon !== searchedText) {
            this.setState({
                selectedIcon: this.props.selectedIcon,
            });
        }
        if(this.props.selectedIconTheme !== selectedIconTheme) {
            this.setState({
                selectedIconTheme: this.props.selectedIconTheme
            });
        }
        document.addEventListener('click', this.handleClick)
    }

    componentWillUnmount() {
        // important
        document.removeEventListener('click', this.handleClick)
    }

    handleClick(e) {
        if (this.node.contains(e.target)) {
            return;
        }
        this.props.closePopup();
    }


    render() {
        const {searchedText, selectedIcon, selectedIconTheme} = this.state;
        const popUpTitle = __('Icon List', 'advanced-gutenberg');
        const iconType = 'material';

        const applyIconButtonClass = [
            'apply-btn',
            'components-button',
            'button button-large',
            'advgb-icon-apply-btn',
            'is-primary'
        ].filter( Boolean ).join( ' ' );

        const closeButtonClass = [
            'close-btn',
        ].filter( Boolean ).join( ' ' );

        return (
            <Fragment>
                <div className='advgb-icon-popup'>
                    <div
                        className='popup-inner'
                        ref={node => {this.node = node}}
                    >
                        <div className="popup-content">
                            <div className="popup-header">
                                <h3>{popUpTitle}</h3>
                                <button
                                    className={closeButtonClass}
                                    onClick={this.props.closePopup}>
                                    <i className="material-icons">close</i>
                                </button>
                            </div>
                            <div className="popup-body">
                                <TextControl
                                    placeholder={ __( 'Search icons', 'advanced-gutenberg' ) }
                                    value={ searchedText }
                                    onChange={ (value) => this.setState( { searchedText: value } ) }
                                />
                                <SelectControl
                                    label={ __('Style', 'advanced-gutenberg') }
                                    value={ selectedIconTheme }
                                    className="advgb-icon-style-select"
                                    options={ [
                                        { label: __('Filled', 'advanced-gutenberg'), value: '' },
                                        { label: __('Outlined', 'advanced-gutenberg'), value: 'outlined' },
                                        { label: __('Rounded', 'advanced-gutenberg'), value: 'round' },
                                        { label: __('Two-Tone', 'advanced-gutenberg'), value: 'two-tone' },
                                        { label: __('Sharp', 'advanced-gutenberg'), value: 'sharp' },
                                    ] }
                                    onChange={ ( value ) => {
                                        this.setState({
                                            selectedIconTheme: value,
                                        });
                                    } }
                                />
                                <div className="advgb-icon-items-wrapper button-icons-list" style={ {maxHeight: 300, overflowY: 'auto', overflowX: 'hidden'} }>

                                    {Object.keys(advgbBlocks.iconList[iconType])
                                        .filter((icon) => icon.indexOf(searchedText.trim().split(' ').join('_')) > -1)
                                        .map( (icon, index) => {

                                            const iconClass = [
                                                iconType === 'material' && 'material-icons',
                                                selectedIconTheme !== '' && `-${selectedIconTheme}`
                                            ].filter( Boolean ).join('');

                                            return (
                                                <div className="advgb-icon-item" key={ index }>
                                                        <span
                                                            onClick={ () => {
                                                                this.setState({
                                                                    selectedIcon: icon
                                                                })
                                                            } }
                                                            className={ icon === selectedIcon && 'active' }
                                                            title={ icon }
                                                        >
                                                            <i className={ iconClass }>{icon}</i>
                                                        </span>
                                                </div>
                                            )
                                        } ) }
                                </div>
                            </div>
                            <div className="popup-footer">
                                <button
                                    disabled={selectedIcon === ''}
                                    className={applyIconButtonClass}
                                    onClick={() => {
                                        this.props.onSelectIcon( selectedIcon );
                                        this.props.onSelectIconTheme(selectedIconTheme);
                                        this.props.closePopup();
                                    }}>
                                    { __('Apply', 'advanced-gutenberg') }
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Fragment>
        );
    }
}
export default IconListPopup;

export function IconListPopupHook(props) {
    const { closePopup, onSelectIcon, onSelectIconTheme, selectedIcon, selectedIconTheme } = props;
    return (
        <IconListPopup
            closePopup={ closePopup }
            onSelectIcon={ onSelectIcon }
            onSelectIconTheme={ onSelectIconTheme }
            selectedIcon={ selectedIcon }
            selectedIconTheme={ selectedIconTheme }
        />
    );
}