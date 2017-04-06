<?php


require_once plugin_dir_path( __FILE__ )
	. '../class-pressbooks-metadata-metadata-fetcher.php';


require_once plugin_dir_path( __FILE__ )
	. '../include-concrete-metadata-fields.php';

/**
 * The metadata included/used by this plugin.
 *
 * @since      0.1
 *
 * @package    Pressbooks_Metadata
 * @subpackage Pressbooks_Metadata/includes/metadata/actual-metadata
 * @author     julienCXX <software@chmodplusx.eu>
 * @author 	   Vasilis Georgoudis <vasilios.georgoudis@gmail.com>
 */
abstract class Pressbooks_Metadata_Plugin_Metadata {

	/**
	 * The metadata components contained in this object.
	 *
	 * @since  0.1
	 * @access private
	 * @var    array   $components The metadata components contained in this
	 * object.
	 */
	private $components;

	/**
	 * The post types used in the components used by the components
	 * contained in this object.
	 *
	 * @since  0.1
	 * @access private
	 * @var    SplObjectStorage $post_types The post types used in the
	 * components used by the components contained in this object.
	 * The SplObjectStorage object ensures the post types unicity.
	 */
	private $post_types;

	/**
	 * The prefix to prepend to each slug, when adding metadata to the
	 * dashboard.
	 *
	 * @since  0.1
	 * @access private
	 * @var    string $slug_prefix The slug prefix.
	 */
	private static $slug_prefix = 'lb_';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  0.1
	 */
	protected function __construct() {

		$this->components = array();
		$this->post_types = array();

	}

	/**
	 * Returns the metadata components contained in this object.
	 *
	 * @since  0.1
	 * @return array The metadata components contained in this object.
	 */
	protected function get_components() {

		return $this->name;

	}

	/**
	 * Returns the metadata components contained in this object.
	 *
	 * @since  0.1
	 * @return array The metadata components contained in this object.
	 */
	private function add_post_type( $post_type ) {

		if ( FALSE !== array_search( $post_type, $this->post_types ) ) {
			return; // type already present
		}
		$this->post_types[] = $post_type;

	}

	/**
	 * Adds a metadata component to the components managed by this object.
	 *
	 * @since 0.1
	 * @param Pressbooks_Metadata_Abstract_Metadata $component The component to add.
	 */
	protected function add_component(
		Pressbooks_Metadata_Abstract_Metadata $component ) {

		foreach ( $component->get_post_types() as $type ) {
			$this->add_post_type( $type );
		}
		$this->components[] = $component;

	}

	/**
	 * Adds an array of metadata components to the components managed by
	 * this object.
	 *
	 * @since 0.1
	 * @param array $components The components to add.
	 */
	protected function add_components( $components ) {

		foreach ( $components as $cpnt ) {
			$this->add_component( $cpnt );
		}

	}

	/**
	 * Adds the metadata components from this object to the current post
	 * metadata dashboard.
	 *
	 * @since 0.1
	 */
	public function add_to_current_post_metadata() {

		foreach ( $this->components as $cpnt ) {
			$cpnt->add_to_current_post_metadata(
				Pressbooks_Metadata_Plugin_Metadata::$slug_prefix );
		}

	}

	/**
	 * Returns the metadata objects for the current post (book, page, etc.)
	 * with their values.
	 *
	 * @since  0.1
	 * @return array The metadata objects with their values as an array of
	 * Pressbooks_Metadata_Abstract_Metadata.
	 */
	public function get_current_metadata() {

		// retrieve metadata from all concerned post types
		$fetched_meta = array();
		foreach ( $this->post_types as $post_type) {
			$fetched_meta = array_merge( $fetched_meta,
				Pressbooks_Metadata_Metadata_Fetcher::fetch_unprefixed_metadata(
					$post_type,
					Pressbooks_Metadata_Plugin_Metadata::$slug_prefix ) );
		}
		$ret = array();
		foreach ( $this->components as $cpnt ) {
			$clone = $cpnt->clone_with_value( $fetched_meta );
			if ( NULL !== $clone ) {
				$ret = array_merge( $ret,
					array( $cpnt->get_slug() => $clone ) );
			}
		}
		return $ret;

	}

	/**
	 * Returns the metadata objects for the current post (book, page, etc.)
	 * with their values.
	 * Keeps only actual fields with a value and flattens the metadata tree
	 * (the fields in a group are extracted from this group).
	 *
	 * @since  0.1
	 * @return array The metadata objects with their values as an array of
	 * Pressbooks_Metadata_Data_Field.
	 */
	public function get_current_metadata_flat() {

		$tree = $this->get_current_metadata();
		$ret = array();
		foreach ( $tree as $key => $val ) {
			if ( $val->is_group_of_fields() ) {
				$ret = array_merge( $ret, $val->get_fields() );
			} else {
				$ret[ $key ] = $val;
			}
		}

		return $ret;

	}


	/**
	 * Returns the ISCED level code according to what is
	 * chosen in the field with $slug = 'isced_level'
	 *
	 * @since  0.4
	 * @return string 
	 */
	public function get_isced_level_code() {

		$meta = $this->get_current_metadata_flat();

		if ($meta['isced_level']->toMicrodataString() == 'Early Childhood Education'){
			$level_code = '0';
		}
		elseif ($meta['isced_level']->toMicrodataString() == 'Primary education') {
			$level_code = '1';
		}
		elseif ($meta['isced_level']->toMicrodataString() == 'Lower secondary education') {
			$level_code = '2';
		}
		elseif ($meta['isced_level']->toMicrodataString() == 'Upper secondary education') {
			$level_code = '3';
		}
		elseif ($meta['isced_level']->toMicrodataString() == 'Post-secondary non-tertiary education') {
			$level_code = '4';
		}
		elseif ($meta['isced_level']->toMicrodataString() == 'Short-cycle tertiary education') {
			$level_code = '5';
		}
		elseif ($meta['isced_level']->toMicrodataString() == 'Bachelor’s or equivalent level') {
			$level_code = '6';
		}
		elseif ($meta['isced_level']->toMicrodataString() == 'Master’s or equivalent level') {
			$level_code = '7';
		}
		elseif ($meta['isced_level']->toMicrodataString() == 'Doctoral or equivalent level') {
			$level_code = '8';
		}
		else{
			$level_code = '9';
		}

		return $level_code;

	}


	/**
	 * Prints the HTML meta tags containing microdata information of
	 * metadata contained in this object, for the public part of the book.
	 *
	 * @since 0.1
	 */
	public function print_microdata_meta_tags() {

		$meta = $this->get_current_metadata_flat();

		foreach ( $meta as $elt ) {
			$it = $elt->get_itemprop();
			if( ! empty( $it ) ) {
?>
<meta itemprop='<?php echo $it; ?>' content='<?php echo $elt->toMicrodataString(); ?>' id='<?php echo $it; ?>'>
<?php
			}
		}

	}

	/**
	 * Prints the HTML educationalAlignment meta tags containing microdata information of
	 * metadata contained in this object, for the public part of the book.
	 *
	 * @since 0.2
	 */
	public function print_educationalAlignment_microdata_meta_tags() {

		$meta = $this->get_current_metadata_flat();
		$level = $this->get_isced_level_code();

		if ( isset( $meta['subject'] ) ) {
?>
<span itemprop="educationalAlignment" itemscope itemtype="http://schema.org/AlignmentObject">	
	<meta itemprop="alignmentType" content="educationalSubject" />
	<meta itemprop="targetName" content='<?php echo $meta['subject']->toMicrodataString(); ?>' />
</span>

<?php
		}

		if ( isset( $meta['isced_field'] ) ) {
?>
<span itemprop="educationalAlignment" itemscope itemtype="http://schema.org/AlignmentObject">
	<meta itemprop="alignmentType" content="educationalSubject" />
	<meta itemprop="educationalFramework" content='ISCED-2013'/>
	<meta itemprop="targetName" content='<?php echo $meta['isced_field']->toMicrodataString(); ?>' />
</span>

<?php
		}

		if ( isset( $meta['isced_level'] ) ) {
?>
<span itemprop="educationalAlignment" itemscope itemtype="http://schema.org/AlignmentObject">
	<meta itemprop="alignmentType" content="educationalLevel" />
	<meta itemprop="educationalFramework" content='ISCED-2011'/>
	<meta itemprop="targetName" content='<?php echo $meta['isced_level']->toMicrodataString(); ?>' />
	<meta itemprop="alternateName" content='ISCED 2011, Level <?php echo $level; ?>' />
</span>

<?php
		}

		if ( isset( $meta['level'] ) && isset( $meta['framework'] )) {
?>
<span itemprop="educationalAlignment" itemscope itemtype="http://schema.org/AlignmentObject">
	<meta itemprop="alignmentType" content="educationalLevel" />
	<meta itemprop="educationalFramework" content='<?php echo $meta['framework']->toMicrodataString(); ?>'/>
	<meta itemprop="targetName" content='<?php echo $meta['level']->toMicrodataString(); ?>' />
</span>

<?php
		} elseif ( isset( $meta['level'] ) && !isset( $meta['framework'] )) {
?>
<span itemprop="educationalAlignment" itemscope itemtype="http://schema.org/AlignmentObject">
	<meta itemprop="alignmentType" content="educationalLevel" />
	<meta itemprop="targetName" content='<?php echo $meta['level']->toMicrodataString(); ?>' />
</span>

<?php
		}
		

	}




}

