import React from 'react';

const body = document.body;

class NGGModal extends React.Component {

    constructor(props) {
        super(props)
        this.closeModal    = this.closeModal.bind(this);

        this.background_layer = document.createElement('div');
        this.background_layer.setAttribute('id', 'add-ngg-gallery-modal-background');
    }

    componentDidMount() {
        body.style.overflow = 'hidden';
        body.appendChild(this.background_layer);

        const self = this;

        const iframe = document.getElementById('add-ngg-gallery-block-iframe');

        iframe.addEventListener('NGG_Iframe_Ready', function() {
            document.getElementById("add-ngg-gallery-modal-spinner").classList.add("add-ngg-gallery-modal-spinner-hidden");
        });

        iframe.addEventListener('NGG_Insert_Gallery', function(event) {
            self.props.onInsertGallery(event.detail.shortcode);

        })

        iframe.addEventListener('NGG_Close_Modal', function() {
            self.props.onCloseModal();
        })
    }

    componentWillUnmount() {
        body.style.overflow = 'auto';
        body.removeChild(this.background_layer);
    }

    closeModal() {
        this.props.onCloseModal();
    }

    render() {
        let attach_to_post_url = window.igw.url + '&origin=block';

        if (this.props.content) {
            attach_to_post_url += '&shortcode=';
            let shortcode = this.props.content.replace(/\\"/g, '"');
            shortcode = shortcode.replace(/^\[ngg_images/, '');
            shortcode = shortcode.replace(/^\[ngg/, '');
            shortcode = shortcode.replace(/]$/, '');
            attach_to_post_url += Base64.encode(shortcode);
        }

        // use createPortal to insert the modal div as a child of <body> to prevent the WP-Admin sidebar
        // menu from getting in the way and causing annoying z-index issues
        return ReactDOM.createPortal(
            <div id="add-ngg-gallery-modal">
                <a href='#'
                   id='add-ngg-gallery-modal-close'
                   onClick={this.closeModal}>
                    <span className="dashicons dashicons-no"/>
                </a>
                <div id="add-ngg-gallery-modal-spinner">
                    <i className="fa fa-spin fa-spinner"/>
                </div>
                <iframe src={attach_to_post_url}
                        tabIndex="-1"
                        name="add-ngg-gallery-block-iframe"
                        id="add-ngg-gallery-block-iframe"/>
            </div>,
            body
        );
    }

}

export default class NGGEditor extends React.Component {

    constructor(props) {
        super(props)

        this.state = {
            open: false
        }

        this.openIGW       = this.openIGW.bind(this);
        this.closeIGW      = this.closeIGW.bind(this);
        this.removeGallery = this.removeGallery.bind(this);
    }

    hasGallery() {
        return this.props.content && this.props.content.length > 0
    }

    removeGallery() {
        this.props.onInsertGallery('');
    }

    closeIGW() {
        this.setState({
            open: false
        });
    }

    openIGW() {
        this.setState({
            open: true
        });
    }

    render() {
        return (
            <div className="add-ngg-gallery-parent">
                {this.state.open ?
                    <NGGModal content={this.props.content}
                              onCloseModal={this.closeIGW}
                              onInsertGallery={this.props.onInsertGallery}/>
                    : ''
                }
                {this.hasGallery() ?
                    <div className="add-ngg-gallery-block">
                        <h3>{add_ngg_gallery_block_i18n.h3}</h3>
                        <button className="add-ngg-gallery-button"
                                onClick={this.openIGW}>
                            {add_ngg_gallery_block_i18n.edit}
                        </button>
                        <button className="add-ngg-gallery-button"
                                onClick={this.removeGallery}>
                            {add_ngg_gallery_block_i18n.delete}
                        </button>
                    </div>
                    :
                    <div className="add-ngg-gallery-block">
                        <div className="add-ngg-gallery-button"
                             onClick={this.openIGW}>
                            {add_ngg_gallery_block_i18n.create}
                        </div>
                    </div>
                }
            </div>
        )
    }
}