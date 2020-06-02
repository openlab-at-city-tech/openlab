GALLERY DISPLAY MODULE
======================

 == Introduction ==
-------------------

This module is responsible for displaying galleries. It's fundamental to this
aspect. It provides the following:

	=>	An interface to attach a collection of images to a post, and display
		them using a particular "display type" (more on display types below)
	=>	A page used to configure Display Settings for "display types"
	=>	A shortcode for rendering a collection of images using a particular
		"display type"


== Terminology ==
-------------------

	*Display Type*
	=>	Used to render a collection of images or galleries.
	=>	Has the following properties: "name", "title", "entity_type", and
		"settings".
    =>  The "name" property is the name that will be referenced in code
        and shortcodes. For example, "photocrati-nextgen_basic_thumbnails".
	=>  The "title" property is the human-friendly name of the display type,
		such as "NextGen Basic Thumbnails".
	=>	The "entity_type" property specifies what kind of display type this is.
		One of two values can be specified: "gallery" or "album". This
		property essentially establishes whether the display type is a
		"gallery type" or "album type".
	=>	The "settings" property is an array of key/value pair settings, serving
		as a global configuration to be taken into consideration when rendering
		the display type on the front-end.
	=>	Is persisted as a Custom Post using the C_CustomPost_DataMapper_Driver.

	*Displayed Gallery*
	=>	An entity representing the association between a collection of images,
		a display type, and a list of display settings applied. Essentially,
		a "displayed gallery" represents a gallery being rendered.
	=>	In previous terminology, this was once called an "Attached Gallery".
	=>	Has the following properties:
		"source"
			- determines where the images or galleries will be coming from
			- acceptable values: "galleries", "albums", "recent_images",
			  "random images", and "tags".
		"container_ids":
			- specifies what ids in particular the images or galleries will be
			  coming from.
			- If the "source" property is set to "galleries", then
			  this property will be set to an array of gallery ids. If the
			  "source" property is set to "albums", then this property will be
			  set to an array of album ids.
		"entity_ids":
			- specifies what ids in particular to display.
			- If the "source"
			  property is set to "galleries", then this property will be set
			  to an array of image ids. Otherwise, if the "source" property
			  is set to "albums", then this property will be set to an array
			  of gallery ids.
			- this property is mutually exclusive of the "exclusions" property.
			  You can either use this property to specify what entities in
			  particular you would like to display, or you could leave this
			  property empty and set the "exclusions" property to specify what
			  entities in particular you don't want to display
		"exclusions":
			- specifies what entities in particular you'd like to exclude from
			  being displayed.
			- If the "source" property is set to "galleries", then this property
			  is set to an array of image ids that you wish to exclude. If the
			  "source" property is set to "albums", then this property is set to
			  an array of gallery ids that you wish to exclude.


== Display Types ==
-------------------

See above for an explanation of what a "display type" actually is. Display types
are persisted as a Custom Post in WordPress via the C_Display_Type_Mapper class.
A display type should always do the following:

	1.	Define a new module to encapsulate the display type into a single unit
	2.	Define an adapter for the C_Display_Type class.

		C_Display_Type is a model used by the datamapper to perform validation
		routines and set defaults. Each "display type module" should register
		an adapter to provide validation routines specific to it's display type.
		For an example, see "A_NextGen_Basic_Thumbnails".

	3.	Define an adapter for the C_Display_Type_Controller class..

		There are several important methods that each display type must override. 
		=> index(), details the logic of how to render the display type on the front-end
		
		=> enqueue_frontend_resources(), used to enqueue any static resources (CSS or JS) using
		   wp_enqueue_script() / wp_enqueue_style() for the front-end
		
		=> get_field_names(), returns a list of field names to render for the settings of
		   the display tab. This settings tab is used both on the "Display Settings Page" as
		   well in the "Display Settings Tab" of the "Attach to Post" interface. For each
		   field name in the returned array, the C_Display_Type_Controller will try to execute
		   a corresponding method in the format "_render_[field_name]_field($display_type)".
		   For example, if you defined the following:
		
				function get_field_names()
				{
					return array(
						'foobar'
					);
				}
		
			Then the C_Display_Type_Controller will try to execute: 
			$this->_render_foobar_field($display_type);
		   
		
		=> enqueue_backend_resources(), used to enqueue any used to enqueue any static 
		   resources (CSS or JS) using wp_enqueue_script() / wp_enqueue_style() for the
		   backend (settings)

	4.	Define an adapter for the C_NextGen_Activator class.

		The C_NextGen_Activator's install() method gets called when the WordPress plugin is
		activated. Each display type needs an adapter that adds a post hook to the install()
		method that will install the new display type, and perform any other initialization
		for the module required. See adapter.nextgen_basic_thumbnails_activation.php for
		an example.


== Display Settings Page ==
-------------------

The display settings page is provided by the C_Display_Settings_Controller. It's
an MVC Controller and has a single action, "index". This action is what renders
the "Display Settings" page under the "Gallery" menu. The "Display Settings"
page displays accordion tabs for each "Display Type" installed.

Specifically, the index() method does the following:
	=>	Finds all display types installed using the C_Display_Type_Mapper.
	=>	Iterates over each display type and...
			- If a post request, updates the display type
			- Instantiates a new C_Display_Type_Controller, passing the display type
			  name to the constructor.
			- Calls C_Display_Type_Controller->enqueue_backend_resources() to
			  enqueue necessary resources for the display type. This is useful if
			  a display type settings tab uses a JavaScript-powered widget that
			  requires an external script enqueued.
			- Calls C_Display_Type_Controller->settings(), which renders the
			  actual accordion tab to configure the display type's settings.