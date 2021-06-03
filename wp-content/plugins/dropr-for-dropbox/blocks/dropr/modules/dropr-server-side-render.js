import icon from './icon';

const { __, sprintf } = wp.i18n;
const { Component, createRef } = wp.element;
const apiFetch = wp.apiFetch;
const { addQueryArgs } = wp.url;
const { Placeholder, Spinner } = wp.components;

const { isEqual, debounce } = lodash;

export function rendererPath( block, attributes = null, urlQueryArgs = {} ) {
	return addQueryArgs( `/wp/v2/block-renderer/${ block }`, {
		context: 'edit',
		...( null !== attributes ? { attributes } : {} ),
		...urlQueryArgs,
	} );
}

class DroprServerSideRender extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			response: null,
		};
		this.droprRef = createRef();
	}

	componentDidMount() {
		this.isStillMounted = true;
		this.fetch( this.props );
		// Only debounce once the initial fetch occurs to ensure that the first
		// renders show data as soon as possible.
		this.fetch = debounce( this.fetch, 500 );
	}

	componentWillUnmount() {
		this.isStillMounted = false;
	}

	componentDidUpdate( prevProps, prevState ) {
		if ( ! isEqual( prevProps, this.props ) ) {
			this.fetch( this.props );
		}

		if ( this.state.response !== prevState.response && null !== this.droprRef.current ) {
			const { attributes = null } = this.props;

			if ( ( attributes !== null && attributes ) && attributes.contentType === 'document' && attributes.document.viewer === 'dropbox' ) {
				let currentRef = this.droprRef.current;
				
                let iframeWrapper = jQuery(currentRef).find('.dropr-iframe-wrapper');
                let dropboxEmbed = iframeWrapper.find('.dropbox-embed');
                var options = {
                    link: dropboxEmbed.attr('href'),
                    file: {
                        zoom: "best"
                    }
                };
                iframeWrapper.css({
                    height: dropboxEmbed.data('height'),
                    width: dropboxEmbed.data('width')
                });
                dropboxEmbed.remove();
                Dropbox.embed(options, iframeWrapper.get(0));
			}
		}
	}

	fetch( props ) {
		if ( ! this.isStillMounted ) {
			return;
		}
		if ( null !== this.state.response ) {
			this.setState( { response: null } );
		}
		const { block, attributes = null, urlQueryArgs = {} } = props;

		const path = rendererPath( block, attributes, urlQueryArgs );
		// Store the latest fetch request so that when we process it, we can
		// check if it is the current request, to avoid race conditions on slow networks.
		const fetchRequest = ( this.currentFetchRequest = apiFetch( { path } )
			.then( ( response ) => {
				if (
					this.isStillMounted &&
					fetchRequest === this.currentFetchRequest &&
					response
				) {
					this.setState( { response: response.rendered } );
				}
			} )
			.catch( ( error ) => {
				if (
					this.isStillMounted &&
					fetchRequest === this.currentFetchRequest
				) {
					this.setState( {
						response: {
							error: true,
							errorMsg: error.message,
						},
					} );
				}
			} ) );
		return fetchRequest;
	}

	render() {
		const response = this.state.response;
		const {
			className,
			EmptyResponsePlaceholder,
			ErrorResponsePlaceholder,
			LoadingResponsePlaceholder,
		} = this.props;

		if ( response === '' ) {
			return (
				<EmptyResponsePlaceholder
					response={ response }
					{ ...this.props }
				/>
			);
		} else if ( ! response ) {
			return (
				<LoadingResponsePlaceholder
					response={ response }
					{ ...this.props }
				/>
			);
		} else if ( response.error ) {
			return (
				<ErrorResponsePlaceholder
					response={ response }
					{ ...this.props }
				/>
			);
		}

		let wrapperClass = typeof className !== 'undefined' && className ? 'wp-dropr-block-content-wrapper ' + className : 'wp-dropr-block-content-wrapper';
		return (
			<div ref={ this.droprRef } className={ wrapperClass } dangerouslySetInnerHTML={ { __html: response } } />
		);
	}
}

DroprServerSideRender.defaultProps = {
	EmptyResponsePlaceholder: ( { attributes = null, className } ) => {
        let emptyMsg = __( 'No content found!', 'dropr' );
        if ( ( attributes !== null && attributes ) ) {
            if ( attributes.contentType === 'document' ) {
                emptyMsg = __( 'No document found!', 'dropr' );
            } else if ( attributes.contentType === 'audio' || attributes.contentType === 'video' ) {
                emptyMsg = __( 'No media found!', 'dropr' );
            }
        }
        return (
            <Placeholder label={ __( 'Dropr', 'dropr' ) } icon={ icon.button } className={ className }>{ emptyMsg }</Placeholder>
        );
    },
	ErrorResponsePlaceholder: ( { response, className } ) => {
		let errorMessage = sprintf(
			// translators: %s: error message describing the problem
			__( 'Error loading the block: %s', 'dropr' ),
			response.errorMsg
		);
		return (
			<Placeholder label={ __( 'Dropr', 'dropr' ) } icon={ icon.button } className={ className }>{ errorMessage }</Placeholder>
		);
	},
	LoadingResponsePlaceholder: ( { className } ) => {
		return (
			<Placeholder className={ className }>
				<Spinner />
			</Placeholder>
		);
	},
};

export default DroprServerSideRender;
