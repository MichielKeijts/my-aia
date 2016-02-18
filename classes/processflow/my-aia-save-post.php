<?php
/**
 * PROCESSFLOWS HOOKS
 *
 * @author Michiel (c) 2016
 */
class MY_AIA_PROCESSFLOW_SAVE_POST extends MY_AIA_PROCESSFLOW {
	/**
	 * Name of the hook to call
	 * @var string 
	 */
	protected static $hook_name		= 'save_post';	
	
	/**
	 * number of arguments accepted
	 * @var int
	 */
	protected static $hook_args		= 3;
	
	/**
	 * Post Data
	 */
	protected static $data;


	/**
	 * Main function to create the hook (add_action)
	 */
	static function create_hook() {
		add_action(self::$hook_name, 'MY_AIA_PROCESSFLOW_SAVE_POST::run', 10, self::$hook_args);
	}
	
	/**
	 * Main function to remove the hook (remove_action)
	 */
	static function remove_hook() {
		remove_action(self::$hook_name, 'MY_AIA_PROCESSFLOW_SAVE_POST::run', 10, self::$hook_args);
	}
	
	/**
	 * MAIN FUNCTION
	 * @param int $id
	 * @param \WP_Post $post
	 * @param bool $update if run is completed
	 */
	static function run($id, $post=NULL, $update=FALSE) {
		global $current_user;
		
		// only use for $update and when not review as wordpress always create a ..
		if (!$update || $post->post_status=='draft') 
			return false;
		
		self::$data = $post;
		
		// parse the processes
		self::parse_processes(self::$hook_name, self::$data);
		
		
		
		$user_query = new WP_User_Query( array( 'exclude' => array( $current_user->ID  ) ) );
		//$post->post_title = ''
		
		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				$post->post_author = $user->ID;
				break;
			}
			self::remove_hook();
			//wp_update_post($post);
			
			
			self::create_hook();
		} 		
		//echo $id;
		//var_dump($post);
		//var_dump($update);
		//exit();
	}
}
