<?php

	/*
		Plugin Name: روتیتر
		Plugin URI: https://wordpress.org/plugins/rootitr/
		Description: توسط این افزونه می‌توانید به راحتی زمینه‌های دلخواه را کنترل، بروزرسانی و فراخوانی کنید.
		Version: 0.9
		Author: Nima Saberi
		Author URI: http://ideyeno.ir
		
	*/
    
	$rootitr = array(
		"rootitr" => "روتیتر", 
		"manba" => "منبع خبر",
		"gozaresh" => "گزارش توسط",
		"axha" => "تصویر توسط",
		"copy" => "برگرفته از",
	);
	
	function my_create_post_meta_box() {
		/*
		add_meta_box( 'my-meta-box', 'جزئیات', 'my_post_meta_box', 'post', 'normal', 'high' );
		add_meta_box( 'my-meta-box', 'جزئیات', 'my_post_meta_box', 'product', 'normal', 'high' );
		add_meta_box( 'my-meta-box', 'جزئیات', 'my_post_meta_box', 'downloadcenter', 'normal', 'high' );
		add_meta_box( 'my-meta-box', 'جزئیات', 'my_post_meta_box', 'document', 'normal', 'high' );
		add_meta_box( 'my-meta-box', 'جزئیات', 'my_post_meta_box', 'page', 'normal', 'high' );
		*/
		foreach (get_post_types() as $post_type){
			add_meta_box($post_type.'-my-meta-box', 'جزئیات', 'my_post_meta_box', $post_type, 'normal', 'high');
		}
	}
	
	function my_post_meta_box( $object, $box ) {
		global $rootitr;
		echo '<div id="postcustomstuff">';
		foreach ($rootitr as $key => &$val) {
			echo '<input name="'.$key.'" style="border-color: #DFDFDF;padding: 7px;width: 100%;" placeholder="'.$val.'" value="'.wp_specialchars( get_post_meta( $object->ID, $key, true ), 1 ).'" />';
		}
		echo '<input type="hidden" name="my_meta_box_nonce" value="'.wp_create_nonce( plugin_basename( __FILE__ ) ).'" />';
		echo '</div>';
	}
	
	function my_save_post_meta_box( $post_id, $post ) {
		if ( !wp_verify_nonce( $_POST['my_meta_box_nonce'], plugin_basename( __FILE__ ) ) ) {
			return $post_id;
		}
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		global $rootitr;
		foreach ($rootitr as $key => &$val) {
			$key_a = get_post_meta( $post_id, $key, true );
			$key_b = wp_specialchars( $_POST[$key] );
			if ( $key_b && $key_a == '' ) {
				add_post_meta( $post_id, $key, $key_b, true );
			}elseif ( $key_b != $key_a ) {
				update_post_meta( $post_id, $key, $key_b );
			}elseif ( $key_b == '' && $key_a ) {
				delete_post_meta( $post_id, $key, $key_a ); 
			}
		}
	}
	
	function get_rootitr($name) {
		global $post;
		if (empty($name)) {
			return FALSE;
		}
		return get_post_meta($post->ID, $name, true);
	}
	
	add_action( 'admin_menu', 'my_create_post_meta_box' );
	add_action( 'save_post', 'my_save_post_meta_box', 10, 2 );

?>