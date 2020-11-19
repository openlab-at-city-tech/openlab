import React    from "react";
import FormData from "form-data";
import fetch    from "cross-fetch";

const {dispatch}            = wp.data;
const {Button}              = wp.components
const {Fragment, Component} = wp.element
const {__}                  = wp.i18n
const {property}            = lodash

class NGGFeaturedThumbnail extends Component {
	state = {
		image_url: null,
		msg: __('Loading...')
	}

	componentDidUpdate(prevProps) {
		if (this.props.image_id !== prevProps.image_id) {
			this.updateImageUrl();
		}
	}

	componentDidMount() {
		this.updateImageUrl();
	}

	updateImageUrl = () => {
		this.getImageUrl(this.props.image_id)
			.then(image_url => this.setState({image_url}))
			.catch((err) => console.log(err) && this.setState({msg: __("Could not load image")}));
	}

	getImageUrl = image_id => {
		const data = new FormData();

		data.append('action', 'get_image');
		data.append('image_id', image_id);

		return fetch(photocrati_ajax.url,
			{
				method: 'POST',
				body: data,
				headers: {
					'Accept': 'application/json'
				}
			})
			.then(res => res.json())
			.then(property('image.image_url'));
	}

	render() {
		const {msg} = this.state;

		const style = {
			paddingTop: '5px',
			paddingBottom: '5px'
		};

		const el = this.state.image_url ? <img src={this.state.image_url}/> : <span>{msg}</span>;

		return (
			<div style={style}>{el}</div>
		);
	}
}

export default class NGGFeaturedImage extends Component {

	constructor(props) {
		super(props);

		this.state = {
			ngg_id: props.meta.ngg_post_thumbnail,
			wp_ml_id: props.featuredImageId
		}
	}

	handleOpenClick = event => {
		top.set_ngg_post_thumbnail = this.onUpdatePostThumbnail;
		tb_show(
			"Set NextGEN Featured Image",
			ngg_featured_image.modal_url.replace(/%post_id%/, this.props.currentPostId)
		);
	}

	handleRemoveClick = event => {
		const meta = {
			...this.props.meta,
			ngg_post_thumbnail: 0,
			featured_media: 0
		};
		this.setState({
			ngg_id: 0
		});
		dispatch('core/editor').editPost({meta});
	}

	onUpdatePostThumbnail = ngg_image_id => {
		tb_remove()
		const meta = {
			...this.props.meta,
			ngg_post_thumbnail: parseInt(ngg_image_id)
		};
		this.setState({
			ngg_id: meta.ngg_post_thumbnail
		});
		dispatch('core/editor').editPost({meta});
	}

	render() {
		return (
			<Fragment>
				<Button style={{marginTop: '10px'}}
						onClick={this.handleOpenClick}
						className="editor-post-featured-image__toggle">
					{__('Set NextGEN Featured Image')}
				</Button>

				{this.state.ngg_id > 0 && this.props.meta.ngg_post_thumbnail !== this.state.ngg_id ?
					<Fragment>
						<NGGFeaturedThumbnail image_id={this.state.ngg_id}/>
						<Button onClick={this.handleRemoveClick}
								className="is-link is-destructive">
							{__('Remove featured image')}
						</Button>
					</Fragment>
					: ''}
			</Fragment>
		);
	}
}