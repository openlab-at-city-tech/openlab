
VIEW ELEMENTS

Elements are sub-pieces of a template/view identified by a "unique" ID. The ID is unique in the sense that it uniquely defines the "origin" or creator for the element itself.

For instance if you have a module called pro_lightbox and specific adapter for trigger buttons and you add an element for it the unique ID identifying the element could be nextgen_pro_lightbox.trigger_buttons 

The ID doesn't however need to be unique in the view itself, meaning you can have multiple elements with the same ID if for instance the element is being rendered for multiple images. We might add an extra "context" parameter to elements together to the ID if we want to uniquely identify element objects.

Example of how elements are initiated:

$elem = $this->start_element('flash_cont');
echo 'cont';
$this->start_element('flash_test');
echo 'test';
$this->start_element('flash_stuff');
echo 'stuff';
$this->end_element();
$this->end_element();
$this->end_element();

var_dump($elem);

This would create this output:

object(C_MVC_View_Element)#775 (3) {
  ["_id"]=>string(10) "flash_cont"
  ["_type"]=>string(7) "element"
  ["_list"]=>array(2) {
    [0]=>string(4) "cont"
    [1]=>object(C_MVC_View_Element)#768 (3) {
      ["_id"]=>string(10) "flash_test"
      ["_type"]=>string(7) "element"
      ["_list"]=>array(2) {
        [0]=>string(4) "test"
        [1]=>object(C_MVC_View_Element)#769 (3) {
          ["_id"]=>string(11) "flash_stuff"
          ["_type"]=>string(7) "element"
          ["_list"]=>array(1) {
            [0]=>string(5) "stuff"
          }
        }
      }
    }
  }
}

The way the MVC view will render these is by creating a root View Element that contains the entire template rendered in the view and which is then "rasterized" e.g. converted to markup/text ready for output

The rendering from template to element will occur in the render_object() method while rasterization will occur in a method called rasterize_object()

Elements created for templates will have ID corresponding to template name/path so for instance _id would equal 'photocrati-nextgen_basic_gallery#slideshow/index' this way adapters adapting rasterize_object() can easily distinguish between for which template rasterization is occurring and act accordingly (for instance trigger buttons being enabled only for certain display types)

Sub-templates will also be automatically rendered to elements meaning image/before and image/after etc. will become sub-elements of the root template element. I don't think this will affect performance much but if so we could easily replace the before/after mechanism to use elements directly

So for instance instead of:
$this->include_template('image/before');
<div class="image">...</div>
$this->include_template('image/after');

We would have:
$this->start_element('nextgen_gallery.image');
$this->include_template('image/before');
<div class="image">...</div>
$this->include_template('image/after');
$this->end_element();

Then you could have an adapter like:
	
	function rasterize_object($root_element)
	{
		if ($root_element->get_id() == 'photocrati-nextgen_basic_gallery#slideshow/index')
		{
			$list = $root_element->find('nextgen_gallery.image');
			
			foreach ($list as $element)
			{
				$element->append('<div>description</div>');
			}
		}
	}

This would reduce the amount of elements overall and improve performance and possibly readability. Then the before/after templates could be left untouched as in the example above.


