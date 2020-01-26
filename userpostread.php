<?php
/**
* Plugin Name: User Post Read
* Plugin URI: http://www.andrearufo.it/
* Description: Check if an user has read a post and when.
* Version: 1.0
* Author: Andrea Rufo
* Author URI: http://www.andrearufo.it
*/

global $upr_db_version;
$upr_db_version = '1.0';

function upr_install() {
    global $wpdb;
    global $upr_db_version;

    $table_name = $wpdb->prefix . 'upr_data';

    $users_table_name = $wpdb->users;
    $posts_table_name = $wpdb->posts;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        post_id bigint(20) NOT NULL,
        opened datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        FOREIGN KEY (user_id) REFERENCES $users_table_name(ID) ON DELETE CASCADE,
        FOREIGN KEY (post_id) REFERENCES $posts_table_name(ID) ON DELETE CASCADE
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'upr_db_version', $upr_db_version );
}
register_activation_hook( __FILE__, 'upr_install' );

function upr_install_data() {
    global $wpdb;

    $welcome_name = 'Mr. WordPress';
    $welcome_text = 'Congratulations, you just completed the installation!';

    $table_name = $wpdb->prefix . 'upr_data';

    $wpdb->insert(
        $table_name,
        array(
            'time' => current_time( 'mysql' ),
            'name' => $welcome_name,
            'text' => $welcome_text,
        )
    );
}

function upr_post_opened( ) {
    if( is_singular() ){
        $post_id = get_the_ID();
        $user_id = get_current_user_id();
        $has_read = upr_has_read($post_id, $user_id);

        if( $post_id > 0 && $user_id > 0 && !$has_read ){
            global $wpdb;
            $table_name = $wpdb->prefix . 'upr_data';
            $data = $wpdb->insert(
                $table_name,
                [
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                ]
            );
        }
    }
}
add_action( 'wp', 'upr_post_opened' );

function upr_has_read($post_id = null, $user_id = null){
    if($user_id == null && !is_user_logged_in()){
        return false;
    }elseif($user_id == null){
        $user_id = get_current_user_id();
    }

    if($post_id == null){
        global $post;
        $post_id = get_the_ID();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'upr_data';
    $query = "SELECT * FROM $table_name WHERE post_id = $post_id AND user_id = $user_id LIMIT 1";
    $data = $wpdb->get_results($query);

	return $data;
}
