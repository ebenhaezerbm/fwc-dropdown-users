<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FWC User Class.
 */
class FWCDropdownUsersMetabox {
	
	/* constructor */
	function __construct() {
		$this->init();

		$this->includes();
	}

	public function init() 
	{
		// remove metabox
		// add_action( 'admin_menu' , array($this, 'fwc_remove_meta_boxes') );
		
		add_action( 'save_post', array($this, 'fwc_update_author_post'), 10, 3 );
	}

	public function fwc_remove_meta_boxes() 
	{
		remove_meta_box( 'authordiv' , 'page' , 'normal' ); // removes author 
	}

	public function fwc_update_author_post( $post_id, $post, $update ) 
	{
		global $wpdb;

		$post_author = $post->post_author;

		if( isset($_POST['post_author']) && !empty($_POST['post_author']) )
		{
			$post_author = intval($_POST['post_author']);
		}

		if( isset($_POST['post_author_override']) && !empty($_POST['post_author_override']) )
		{
			$post_author = intval($_POST['post_author_override']);
		}

		if( isset($post_author) && !empty($post_author) )
		{
			$wpdb->update( 
				$wpdb->posts, 
				array( 
					'post_author' => $post_author,  // integer (number) 
				), 
				array( 
					'ID' => $post_id 
				), 
				array( 
					'%d' // post_author
				), 
				array( 
					'%d' // post_id
				) 
			);
		}
	}

	public function includes() {}

}

return new FWCDropdownUsersMetabox();

