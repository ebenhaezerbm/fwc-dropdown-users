<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FWC Dropdown User Class.
 */
class FWCDropdownUsers {
	
	/* constructor */
	function __construct() {
		$this->init();

		$this->includes();
	}

	public function init() 
	{
		// Filter to fix the Post Author Dropdown
		// add_filter( 'wp_dropdown_users_args', array($this, 'fwc_dropdown_users_args_override', 10, 2) );
		add_filter( 'wp_dropdown_users', array($this, 'fwc_dropdown_user_override') );
		
		add_action( 'wp_ajax_fwc_get_users', array($this, 'fwc_get_users_ajax_callback') );
		add_action( 'wp_ajax_nopriv_fwc_get_users', array($this, 'fwc_get_users_ajax_callback') );

		add_action( 'wp_ajax_fwc_get_selected_user', array($this, 'fwc_get_selected_user_callback') );
	}

	public function fwc_dropdown_users_args_override($query_args, $r) 
	{

		$query_args['role'] = array('administrator');

		// Unset the 'who' as this defaults to the 'author' role
		unset( $query_args['who'] );

		return $query_args;
	}

	public function fwc_dropdown_user_override($output)
	{
		$currentScreen = get_current_screen();

		$attr_name = 'author';
		$author_id = '';
		$name = '';

		if( isset($_GET['author']) && !empty($_GET['author']) ){
			$author = intval($_GET['author']);
			$user = get_user_by('ID', $author);
			if( $user ){
				$author_id = $user->ID;
				$name = $user->user_nicename . ' ('.$user->display_name.')';
			}
		}

		if( $currentScreen->base != 'edit' && $currentScreen->parent_base == 'edit' )
		{
			global $post;

			$user = get_userdata( $post->post_author );

			$attr_name = 'post_author_override';

			$author_id = $user->ID;
			$name = $user->user_nicename . ' ('.$user->display_name.')';
		}

		$output = '<input type="text" name="'.$attr_name.'" id="dropdown-users" class="dropdown-users" data-text="'.$name.'" value="'.$author_id.'" placeholder="Select User"/>';

		return $output;
	}

	public function fwc_get_users_ajax_callback() 
	{
		global $wpdb;

		$search = ( isset($_GET['q']) && !empty($_GET['q']) ) ? sanitize_text_field( $_GET['q'] ) : '';
		$page = ( isset($_GET['page']) && !empty($_GET['page']) ) ? intval( $_GET['page'] ) : 1;
		$item_per_page = 12;

		$args = array( 
			'search' => '*'.$search.'*', 
			'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' ),
			// 'role' => 'administrator',
			// 'role__in' => array('administrator','editor','author','community-moderator','author-event'),
			// 'role__not_in' => array('contributor'),
			'meta_key' => $wpdb->prefix . 'user_level',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'paged' => $page,
			'number' => $item_per_page
		);

		$users = new WP_User_Query( $args );
		$users = $users->results;
		$total_count = $users->total_users;

		$response  = array();
		if ( $users ) { 
			foreach ( $users as $user )
			{
				$response[] = array(
					'id' => $user->ID,
					'name' => $user->user_nicename . ' ('.$user->display_name.')',
					'role' => ucwords( $user->roles[0] )
				);
			}
		}

		$response['total_count'] = $total_count;
		$response['item_per_page'] = $item_per_page;

		wp_send_json_success( $response );
	}

	public function fwc_get_selected_user_callback() 
	{
		$dataForm = $_POST['dataForm'];

		$post_id = $dataForm['post_id'];

		$my_post = get_post( $post_id );

		$user = get_user_by('ID', $my_post->post_author);

		$response = array(
			'post_author' => $user->ID,
			'name' => $user->user_nicename . ' ('.$user->display_name.')'
		);

		echo json_encode($response);

		wp_die();
	}

	public function includes() {}

}

return new FWCDropdownUsers();