// Version 1.0 - Initial version
// Version 1.1 - Add Resend functionality

var privateSetting = '';

wp.apiFetch( { path: '/s2/v1/settings/private' } ).then(
	function ( setting ) {
		privateSetting = setting;
	}
);

wp.apiFetch( { path: '/s2/v1/settings/s2meta_default' } ).then(
	function ( setting ) {
		var s2mail = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )._s2mail;

		if ( '' === s2mail ) {
			if ( '0' === setting ) {
				s2mail = 'yes';
			} else if ( '1' === setting ) {
				s2mail = 'no';
			}
			wp.data.dispatch( 'core/editor' ).editPost( { meta: { '_s2mail': s2mail } } );
			wp.data.dispatch( 'core/editor' ).savePost();
		}
	}
);

( function( plugins, element, i18n, editPost, components, data, compose, apiFetch ) {
	var registerPlugin            = plugins.registerPlugin,
		el                        = element.createElement,
		__                        = i18n.__,
		Fragment                  = element.Fragment,
		PluginSidebar             = editPost.PluginSidebar,
		PluginSidebarMoreMenuItem = editPost.PluginSidebarMoreMenuItem,
		PanelBody                 = components.PanelBody,
		PanelRow                  = components.PanelRow,
		CheckboxControl           = components.CheckboxControl,
		Button                    = components.Button,
		select                    = data.select,
		dispatch                  = data.dispatch,
		withSelect                = data.withSelect,
		withDispatch              = data.withDispatch,
		Compose                   = compose.compose;

	var CheckboxControlMeta = Compose(
		withSelect(
			function( select, props ) {
				var s2mail = select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ props.fieldName ];
				return {
					metaChecked: ( 'no' === s2mail ? true : false )
				};
			}
		),
		withDispatch(
			function( dispatch, props ) {
				return {
					setMetaChecked: function( value ) {
						var s2mail = ( true === value ? 'no' : 'yes'  );
						dispatch( 'core/editor' ).editPost( { meta: { [ props.fieldName ]: s2mail } } );
						dispatch( 'core/editor' ).savePost();
					}
				};
			}
		)
	)(
		function( props ) {
			return el(
				CheckboxControl,
				{
					label: __( 'Check here to disable sending of an email notification for this post/page', 'subscribe2' ),
					checked: props.metaChecked,
					onChange: function( content ) {
						props.setMetaChecked( content );
					}
				}
			);
		}
	);

	var maybeRenderResend = function() {
		if ( 'publish' === select( 'core/editor' ).getEditedPostAttribute( 'status' ) ) {
			return renderResendPanel();
		} else if ( 'private' === select( 'core/editor' ).getEditedPostAttribute( 'status' ) && 'yes' === privateSetting ) {
			return renderResendPanel();
		}
	};

	var renderResendPanel = function() {
		return el(
			PanelBody,
			{
				title: __( 'Subscribe2 Resend', 'subscribe2' ),
				initialOpen: false
			},
			el(
				PanelRow,
				{},
				el(
					'div',
					null,
					__( 'Resend the notification email of this post to current subscribers:', 'subscribe2' )
				)
			),
			el(
				PanelRow,
				{},
				el(
					Button,
					{
						isDefault: true,
						onClick: resendClick
					},
					__( 'Resend Notification', 'subscribe2' )
				)
			)
		);
	};

	var previewClick = function() {
		var postid = select( 'core/editor' ).getCurrentPostId();
		dispatch( 'core/editor' ).savePost();
		apiFetch( { path: '/s2/v1/preview/' + postid } );
		dispatch( 'core/notices' ).createInfoNotice( __( 'Attempt made to send email preview', 'subscribe2' ) );
	};

	var resendClick = function() {
		var postid = select( 'core/editor' ).getCurrentPostId();
		dispatch( 'core/editor' ).savePost();
		apiFetch( { path: '/s2/v1/resend/' + postid } );
		dispatch( 'core/notices' ).createInfoNotice( __( 'Attempt made to resend email notification', 'subscribe2' ) );
	};

	var s2sidebar = function() {
		return el(
			Fragment,
			{},
			el(
				PluginSidebarMoreMenuItem,
				{
					target: 's2-sidebar',
					icon: 'email'
				},
				__( 'Subscribe2 Sidebar', 'subscribe2' )
			),
			el(
				PluginSidebar,
				{
					name: 's2-sidebar',
					title: __( 'Subscribe2 Sidebar', 'subscribe2' ),
					icon: 'email',
					isPinned: true,
					isPinnable: true,
					togglePin: true,
					togglesidebar: false
				},
				el(
					PanelBody,
					{
						title: __( 'Subscribe2 Override', 'subscribe2' ),
						initialOpen: true
					},
					el(
						PanelRow,
						{},
						el(
							CheckboxControlMeta,
							{
								fieldName: '_s2mail'
							}
						)
					)
				),
				el(
					PanelBody,
					{
						title: __( 'Subscribe2 Preview', 'subscribe2' ),
						initialOpen: false
					},
					el(
						PanelRow,
						{},
						el(
							'div',
							null,
							__( 'Send preview email of this post to currently logged in user:', 'subscribe2' )
						)
					),
					el(
						PanelRow,
						{},
						el(
							Button,
							{
								isDefault: true,
								onClick: previewClick
							},
							__( 'Send Preview', 'subscribe2' )
						)
					)
				),
				maybeRenderResend()
			)
		);
	};

	registerPlugin(
		'subscribe2-sidebar',
		{
			render: s2sidebar
		}
	);
} (
	wp.plugins,
	wp.element,
	wp.i18n,
	wp.editPost,
	wp.components,
	wp.data,
	wp.compose,
	wp.apiFetch
) );
