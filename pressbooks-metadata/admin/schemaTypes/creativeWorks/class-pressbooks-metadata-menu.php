<?php

namespace schemaTypes\cw;
use schemaFunctions\Pressbooks_Metadata_General_Functions as gen_func;
use schemaTypes\Pressbooks_Metadata_Type;

/**
 * The class for the menu type including operations and metaboxes
 *
 * @link       https://github.com/Books4Languages/pressbooks-metadata
 * @since      0.8.1
 *
 * @package    Pressbooks_Metadata
 * @subpackage Pressbooks_Metadata/admin/schemaTypes
 * @author     Christos Amyrotos <christosv2@hotmail.com>
 * @author     Vasilis Georgoudis <vasilios.georgoudis@gmail.com>
 */

class Pressbooks_Metadata_Menu extends Pressbooks_Metadata_Type {

	public function __construct($type_level_input) {
		parent::__construct($type_level_input);
		$this->class_name = __CLASS__ .'_'. $this->type_level;
		$this->type_settings = array('menu_type' => array('Menu Type','http://schema.org/Menu'));
		$this->parent_type = new Pressbooks_Metadata_Creative_Work($this->type_level);
		$this->pmdt_add_metabox($this->type_level);
	}

	/**
	 * Function used for comparing the instances of the schema types
	 *
	 * @since    0.x
	 * @access   public
	 */
	public function __toString() {
		return $this->class_name;
	}

	/**
	 * The function which produces the metaboxes for the menu type
	 * @param string Accepting a string so we can distinguish on witch place each metabox is created
	 * The value passed here is also used when calling the metadata functions in the header and the footer.
	 * @since 0.8.1
	 */
	private function pmdt_add_metabox($meta_position){
		//----------- metabox ----------- //
		x_add_metadata_group( 	'menu', $meta_position, array(
			'label' 		=>	'Menu Type Properties',
			'priority' 		=>	'high',
		) );
		//----------- metafields ----------- //
		// Has Menu Item
		x_add_metadata_field( 	'pb_has_menu_item_'.$meta_position, $meta_position, array(
			'group' 		=>	'menu',
			'label' 		=>	'Has Menu Item',
			'description' 	=>	'A food or drink item contained in a menu or menu section.'
		) );
		// Has Menu Section
		x_add_metadata_field( 	'pb_has_menu_section_'.$meta_position, $meta_position, array(
			'group' 		=>	'menu',
			'label' 		=>	'Has Menu Section',
			'description' 	=>	'A subgrouping of the menu (by dishes, course, serving time period, etc.).'
		) );
	}

	/**
	 * A function that creates the metadata for the Menu type.
	 * @since 0.8.1
	 *
	 */
	public function pmdt_get_metatags() {
		//Distinguishing if we are working on a post --- chapter level or on the main site level
		//The type_level variable is the string we used to create the metabox

		$is_site; // This bool var is used to identify if the level is site level or any other post level
		if ( $this->type_level == 'metadata' || $this->type_level == 'site-meta' ) { //loading the appropriate metadata depending on the type level
			$metadata = gen_func::get_metadata();
			$is_site = true;
		} else {
			$is_site = false;
			$metadata = get_post_meta( get_the_ID() );
		}

		// array of the items needed to become microtags
		$menu_data = array(

			'hasMenuItem' => 'pb_has_menu_item',
			'hasMenuSection' => 'pb_has_menu_section'
		);

		$html = "<!-- Microtags --> \n";

		$html .= '<div itemscope itemtype="http://schema.org/Menu">';

		foreach ( $menu_data as $itemprop => $content ) {
			if ( isset( $metadata[ $content . '_' . $this->type_level ] ) ) {

				if ( !$is_site ) { //we are using the get_first function to get the value from the returned array
					$value = $this->pmdt_get_first( $metadata[ $content . '_' . $this->type_level ] );
				} else {
					if($this->type_level == 'site-meta'){
						$value = $this->pmdt_get_first($metadata[ $content . '_' . $this->type_level ]);
					}else{//We always use the get_first function except if our level is metadata coming from pressbooks
						$value = $metadata[ $content . '_' . $this->type_level ];
					}
				}
				$html .= "<meta itemprop = '" . $itemprop . "' content = '" . $value . "'>\n";
			}
		}
		$html .= '</div>';
		return $html;
	}
}