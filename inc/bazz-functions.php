<?php
// custom JS
add_action( 'wp_enqueue_scripts', 'bazz_scripts');
function bazz_scripts(){
	wp_enqueue_script( 'jquery' ); // jQuery
	wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/js/bazz-script.js', array('jquery'), false, true );
	
}
// LOAD MORE AJAX
function true_load_posts(){
	$args = unserialize(stripslashes($_POST['query']));
	$args['paged'] = $_POST['page'] + 1; // следующая страница
	$args['post_status'] = 'publish';
	$q = new WP_Query($args);
	if( $q->have_posts() ):
		while($q->have_posts()): $q->the_post();
			/* HTML шаблон поста */
			get_template_part( 'template-parts/content', get_post_format() );
		endwhile;
	endif;
	wp_reset_postdata();
	die();
}
add_action('wp_ajax_loadmore', 'true_load_posts');
add_action('wp_ajax_nopriv_loadmore', 'true_load_posts');

// New options
add_action('customize_register', function($bazz_options){
    $bazz_options->add_section(
        'bazz_options',
        array(
            'title' => 'Настройки для теста',
            'description' => 'Настройки для теста',
            'priority' => 11
        )
    );
	$bazz_options->add_setting(
		'bazz_options_color',
		array('default' => '#000')
	);
	$bazz_options->add_control(
		'bazz_options_color',
		array(
			'label' => 'Настройка цвета заголовков',
			'section' => 'bazz_options',
			'type' => 'color'
		)
	);
	$bazz_options->add_setting(
		'bazz_options_logo',
		array('default' => '')
	);
	$bazz_options->add_control(
		new WP_Customize_Image_Control(
			$bazz_options,
			'bazz_options_logo',
			array(
				'label' => 'Загрузка лого',
				'section' => 'bazz_options',
				'settings' => 'bazz_options_logo'
			)
		)
	);
});
add_action( 'wp_head', 'bazz_options_show' );
function bazz_options_show() {
	$title_color = get_theme_mod('bazz_options_color'); ?>
	<style>
	h2, h2 a { color: <?php echo $title_color; ?>; }
	</style>
<?php }

// Field a-la raiting
add_action('admin_init', 'a_la_raiting_field', 21);
function a_la_raiting_field( $post ) {
	add_meta_box( 'a_la_raiting_field', 'Рейтинг', 'a_la_raiting_field_func', 'post', 'normal', 'high' );
}
function a_la_raiting_field_func( $post ) {
	$selected = get_post_meta($post->ID, 'a_la_raiting_field', true); 
	if (empty($selected)) {
		$selected = 1;
	}?>
	<p>Выберите рейтинг статьи</p>
	<select name="a_la_raiting_select" id="a_la_raiting">
		<option value="1" <?php selected( $selected, '1' )?>>1</option>
		<option value="2" <?php selected( $selected, '2' )?>>2</option>
		<option value="3" <?php selected( $selected, '3' )?>>3</option>
		<option value="4" <?php selected( $selected, '4' )?>>4</option>
		<option value="5" <?php selected( $selected, '5' )?>>5</option>
	</select>
<?php }

add_action('save_post', 'a_la_raiting_field_update', 20);
function a_la_raiting_field_update( $post_id ) {
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false; // если это автосохранение
	if ( ! current_user_can('edit_post', $post_id) ) return false; // если юзер не имеет право редактировать запись
	if ($_POST['a_la_raiting_select']) {
		$raiting = $_POST['a_la_raiting_select'];
		update_post_meta($post_id, 'a_la_raiting_field', $raiting);
	}
	return $post_id;
}