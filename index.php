<?php 
/*
 Plugin Name: VW Corporate Pro Posttype
 Plugin URI: https://www.vwthemes.com/
 Description: Creating new post type for VW Corporate Pro Theme.
 Author: VW Themes
 Version: 1.0
 Author URI: https://www.vwthemes.com/
*/

define( 'VW_CORPORATE_PRO_POSTTYPE_VERSION', '1.0' );
add_action( 'init', 'vw_corporate_pro_posttype_create_post_type' );

function vw_corporate_pro_posttype_create_post_type() {

  register_post_type( 'services',
    array(
      'labels' => array(
        'name' => __( 'Services','vw-corporate-pro-posttype' ),
        'singular_name' => __( 'Services','vw-corporate-pro-posttype' )
      ),
      'capability_type' => 'post',
      'menu_icon'  => 'dashicons-portfolio',
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'thumbnail'
      )
    )
  );
  register_post_type( 'team',
    array(
      'labels' => array(
        'name' => __( 'Our Team','vw-corporate-pro-posttype' ),
        'singular_name' => __( 'Our Team','vw-corporate-pro-posttype' )
      ),
        'capability_type' => 'post',
        'menu_icon'  => 'dashicons-businessman',
        'public' => true,
        'supports' => array( 
          'title',
          'editor',
          'thumbnail'
      )
    )
  );
  register_post_type( 'testimonials',
    array(
      'labels' => array(
        'name' => __( 'Testimonials','vw-corporate-pro-posttype' ),
        'singular_name' => __( 'Testimonials','vw-corporate-pro-posttype' )
      ),
      'capability_type' => 'post',
      'menu_icon'  => 'dashicons-businessman',
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'thumbnail'
      )
    )
  );
}
/*--------------------- Services section ----------------------*/
// Serives section
function vw_corporate_pro_posttype_images_metabox_enqueue($hook) {
  if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
    wp_enqueue_script('vw-corporate-pro-posttype-images-metabox', plugin_dir_url( __FILE__ ) . '/js/img-metabox.js', array('jquery', 'jquery-ui-sortable'));

    global $post;
    if ( $post ) {
      wp_enqueue_media( array(
          'post' => $post->ID,
        )
      );
    }

  }
}
add_action('admin_enqueue_scripts', 'vw_corporate_pro_posttype_images_metabox_enqueue');
// Services Meta
function vw_corporate_pro_posttype_bn_custom_meta_services() {

    add_meta_box( 'bn_meta', __( 'Icon Image', 'vw-corporate-pro-posttype' ), 'vw_corporate_pro_posttype_bn_meta_callback_services', 'services', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
  add_action('admin_menu', 'vw_corporate_pro_posttype_bn_custom_meta_services');
}

function vw_corporate_pro_posttype_bn_meta_callback_services( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    ?>
  <div id="property_stuff">
    <table id="list-table">     
      <tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-1">
          <p>
            <label for="meta-image"><?php echo esc_html('Icon Image'); ?></label><br>
            <input type="text" name="meta-image" id="meta-image" class="meta-image regular-text" value="<?php echo $bn_stored_meta['meta-image'][0]; ?>">
            <input type="button" class="button image-upload" value="Browse">
          </p>
          <div class="image-preview"><img src="<?php echo $bn_stored_meta['meta-image'][0]; ?>" style="max-width: 250px;"></div>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

function vw_corporate_pro_posttype_bn_meta_save_services( $post_id ) {

  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  // Save Image
  if( isset( $_POST[ 'meta-image' ] ) ) {
      update_post_meta( $post_id, 'meta-image', $_POST[ 'meta-image' ] );
  }
}
add_action( 'save_post', 'vw_corporate_pro_posttype_bn_meta_save_services' );


/*------------------- Services Shortcode -------------------------*/
function vw_corporate_pro_posttype_services_func( $atts ) {
    $services = ''; 
    $services = '<div id="services"><div class="row inner-test-bg">';
      $new = new WP_Query( array( 'post_type' => 'services') );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'medium' );
          $url = $thumb['0'];
          $excerpt = vw_corporate_pro_string_limit_words(get_the_excerpt(),20);
          $services .= '<div class="col-lg-6 col-md-6 mt-4"> 
                <div class="row m-0 shrtcod-pg">
                  <div class="col-md-6 p-3">';
                    if (has_post_thumbnail()){
                    $services.= '<img src="'.esc_url($url).'">';
                    }
                    $services.= '</div>
                  <div class="col-md-6">
                    <h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4>
                    <div class="content_box w-100">
                      <div class="short_text pb-3">'.$excerpt.'</div>
                    </div>
                  </div>
                </div>
              </div><div class="clearfix"></div>';
          $k++;         
        endwhile; 
        wp_reset_postdata();
        $services.= '</div>';
      else :
        $services = '<div id="services" class="col-md-3 mt-3 mb-4"><h2 class="center">'.__('Not Found','vw-corporate-pro-posttype').'</h2></div></div></div>';
      endif;
    return $services;
}
add_shortcode( 'vw-corporate-pro-services', 'vw_corporate_pro_posttype_services_func' );

/*------------------------- Team Section-----------------------------*/
/* Adds a meta box for Designation */
function vw_corporate_pro_posttype_bn_team_meta() {
    add_meta_box( 'vw_corporate_pro_posttype_bn_meta', __( 'Enter Details','vw-corporate-pro-posttype' ), 'vw_corporate_pro_posttype_ex_bn_meta_callback', 'team', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'vw_corporate_pro_posttype_bn_team_meta');
}
/* Adds a meta box for custom post */
function vw_corporate_pro_posttype_ex_bn_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'vw_corporate_pro_posttype_bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );

    //Email details
    if(!empty($bn_stored_meta['meta-desig'][0]))
      $bn_meta_desig = $bn_stored_meta['meta-desig'][0];
    else
      $bn_meta_desig = '';

    //Phone details
    if(!empty($bn_stored_meta['meta-call'][0]))
      $bn_meta_call = $bn_stored_meta['meta-call'][0];
    else
      $bn_meta_call = '';

    //facebook details
    if(!empty($bn_stored_meta['meta-facebookurl'][0]))
      $bn_meta_facebookurl = $bn_stored_meta['meta-facebookurl'][0];
    else
      $bn_meta_facebookurl = '';

    //linkdenurl details
    if(!empty($bn_stored_meta['meta-linkdenurl'][0]))
      $bn_meta_linkdenurl = $bn_stored_meta['meta-linkdenurl'][0];
    else
      $bn_meta_linkdenurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-twitterurl'][0]))
      $bn_meta_twitterurl = $bn_stored_meta['meta-twitterurl'][0];
    else
      $bn_meta_twitterurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-googleplusurl'][0]))
      $bn_meta_googleplusurl = $bn_stored_meta['meta-googleplusurl'][0];
    else
      $bn_meta_googleplusurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-designation'][0]))
      $bn_meta_designation = $bn_stored_meta['meta-designation'][0];
    else
      $bn_meta_designation = '';

    ?>
    <div id="agent_custom_stuff">
        <table id="list-table">         
            <tbody id="the-list" data-wp-lists="list:meta">
                <tr id="meta-1">
                    <td class="left">
                        <?php _e( 'Email', 'vw-corporate-pro-posttype' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="meta-desig" id="meta-desig" value="<?php echo esc_attr($bn_meta_desig); ?>" />
                    </td>
                </tr>
                <tr id="meta-2">
                    <td class="left">
                        <?php _e( 'Phone Number', 'vw-corporate-pro-posttype' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="meta-call" id="meta-call" value="<?php echo esc_attr($bn_meta_call); ?>" />
                    </td>
                </tr>
                <tr id="meta-3">
                  <td class="left">
                    <?php _e( 'Facebook Url', 'vw-corporate-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-facebookurl" id="meta-facebookurl" value="<?php echo esc_url($bn_meta_facebookurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-4">
                  <td class="left">
                    <?php _e( 'Linkedin URL', 'vw-corporate-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-linkdenurl" id="meta-linkdenurl" value="<?php echo esc_url($bn_meta_linkdenurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-5">
                  <td class="left">
                    <?php _e( 'Twitter Url', 'vw-corporate-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-twitterurl" id="meta-twitterurl" value="<?php echo esc_url( $bn_meta_twitterurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-6">
                  <td class="left">
                    <?php _e( 'GooglePlus URL', 'vw-corporate-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-googleplusurl" id="meta-googleplusurl" value="<?php echo esc_url($bn_meta_googleplusurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-7">
                  <td class="left">
                    <?php _e( 'Designation', 'vw-corporate-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="text" name="meta-designation" id="meta-designation" value="<?php echo esc_attr($bn_meta_designation); ?>" />
                  </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}
/* Saves the custom Designation meta input */
function vw_corporate_pro_posttype_ex_bn_metadesig_save( $post_id ) {
    if( isset( $_POST[ 'meta-desig' ] ) ) {
        update_post_meta( $post_id, 'meta-desig', esc_html($_POST[ 'meta-desig' ]) );
    }
    if( isset( $_POST[ 'meta-call' ] ) ) {
        update_post_meta( $post_id, 'meta-call', esc_html($_POST[ 'meta-call' ]) );
    }
    // Save facebookurl
    if( isset( $_POST[ 'meta-facebookurl' ] ) ) {
        update_post_meta( $post_id, 'meta-facebookurl', esc_url($_POST[ 'meta-facebookurl' ]) );
    }
    // Save linkdenurl
    if( isset( $_POST[ 'meta-linkdenurl' ] ) ) {
        update_post_meta( $post_id, 'meta-linkdenurl', esc_url($_POST[ 'meta-linkdenurl' ]) );
    }
    if( isset( $_POST[ 'meta-twitterurl' ] ) ) {
        update_post_meta( $post_id, 'meta-twitterurl', esc_url($_POST[ 'meta-twitterurl' ]) );
    }
    // Save googleplusurl
    if( isset( $_POST[ 'meta-googleplusurl' ] ) ) {
        update_post_meta( $post_id, 'meta-googleplusurl', esc_url($_POST[ 'meta-googleplusurl' ]) );
    }
    // Save designation
    if( isset( $_POST[ 'meta-designation' ] ) ) {
        update_post_meta( $post_id, 'meta-designation', esc_html($_POST[ 'meta-designation' ]) );
    }
}
add_action( 'save_post', 'vw_corporate_pro_posttype_ex_bn_metadesig_save' );

add_action( 'save_post', 'bn_meta_save' );
/* Saves the custom meta input */
function bn_meta_save( $post_id ) {
  if( isset( $_POST[ 'vw_corporate_pro_posttype_team_featured' ] )) {
      update_post_meta( $post_id, 'vw_corporate_pro_posttype_team_featured', esc_attr(1));
  }else{
    update_post_meta( $post_id, 'vw_corporate_pro_posttype_team_featured', esc_attr(0));
  }
}
/*------------------------ Team Shortcode --------------------------*/
function vw_corporate_pro_posttype_team_func( $atts ) {
    $team = ''; 
    $team = '<div class="row">';
      $new = new WP_Query( array( 'post_type' => 'team') );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'medium' );
          $url = $thumb['0'];
          $excerpt = vw_corporate_pro_string_limit_words(get_the_excerpt(),20);
          $designation = get_post_meta($post_id,'meta-designation',true);
          $call = get_post_meta($post_id,'meta-call',true);
          $email = get_post_meta($post_id,'meta-desig',true);
          $facebookurl = get_post_meta($post_id,'meta-facebookurl',true);
          $linkedin = get_post_meta($post_id,'meta-linkdenurl',true);
          $twitter = get_post_meta($post_id,'meta-twitterurl',true);
          $googleplus = get_post_meta($post_id,'meta-googleplusurl',true);

          $team .= '<div class="team_outer col-lg-6 col-sm-6 mb-4">
            <div class="team_wrap bordr">
            <div class="row">';        
                  if (has_post_thumbnail()){
                  $team .= '<div class=" col-md-6 team-image">
                   <img src="'.esc_url($url).'"></div>
                
                <div class="col-md-6"> 
                 <h4 class="team_name"><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4>
                  <div class="team-socialbox">';
                   $team .= '<div class="shrt_socio">';                           
                      if($facebookurl != '' || $linkedin != '' || $twitter != '' || $googleplus != ''){?>
                          <?php if($facebookurl != ''){
                            $team .= '<a class="" href="'.esc_url($facebookurl).'" target="_blank"><i class="fab fa-facebook-f"></i></a>';
                           } if($twitter != ''){
                            $team .= '<a class="" href="'.esc_url($twitter).'" target="_blank"><i class="fab fa-twitter"></i></a>';                          
                           } if($linkedin != ''){
                           $team .= ' <a class="" href="'.esc_url($linkedin).'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                          }if($googleplus != ''){
                            $team .= '<a class="" href="'.esc_url($googleplus).'" target="_blank"><i class="fab fa-google-plus-g"></i></a>';
                          }
                        }
                    $team .= '</div>
                  </div>
                  ';
                  if($designation != ''){
                  $team .= '<p class="mt-2">'.esc_html($designation).'</p>';
                  }
                  if($call != ''){
                  $team .= '<p class="mt-2">'.esc_html($call).'</p>';
                  }
                  if($email != ''){
                  $team .= '<p class="mt-2">'.esc_html($email).'</p>';
                  }
                }                    
              $team .='</div></div></div></div>';
            if($k%4 == 0){
            $team.= '<div class="clearfix"></div>'; 
          } 
          $k++;         
        endwhile; 
        wp_reset_postdata();
        $team.= '</div>';
      else :
        $team = '<div id="team" class="team_wrap col-md-3 mt-3 mb-4"><h2 class="center">'.__('Not Found','vw-corporate-pro-posttype').'</h2></div>';
      endif;
    return $team;
}
add_shortcode( 'vw-corporate-pro-team', 'vw_corporate_pro_posttype_team_func' );

/*----------------------Testimonial section ----------------------*/
/* Adds a meta box to the Testimonial editing screen */
function vw_corporate_pro_posttype_bn_testimonial_meta_box() {
  add_meta_box( 'vw-corporate-pro-posttype-testimonial-meta', __( 'Enter Details', 'vw-corporate-pro-posttype' ), 'vw_corporate_pro_posttype_bn_testimonial_meta_callback', 'testimonials', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'vw_corporate_pro_posttype_bn_testimonial_meta_box');
}
/* Adds a meta box for custom post */
function vw_corporate_pro_posttype_bn_testimonial_meta_callback( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'vw_corporate_pro_posttype_posttype_testimonial_meta_nonce' );
  $bn_stored_meta = get_post_meta( $post->ID );
  $desigstory = get_post_meta( $post->ID, 'vw_corporate_pro_posttype_testimonial_desigstory', true );
  ?>
  <div id="testimonials_custom_stuff">
    <table id="list">
      <tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-1">
          <td class="left">
            <?php _e( 'Designation', 'vw-corporate-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="vw_corporate_pro_posttype_testimonial_desigstory" id="vw_corporate_pro_posttype_testimonial_desigstory" value="<?php echo esc_attr( $desigstory ); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

/* Saves the custom meta input */
function vw_corporate_pro_posttype_bn_metadesig_save( $post_id ) {
  if (!isset($_POST['vw_corporate_pro_posttype_posttype_testimonial_meta_nonce']) || !wp_verify_nonce($_POST['vw_corporate_pro_posttype_posttype_testimonial_meta_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // Save desig.
  if( isset( $_POST[ 'vw_corporate_pro_posttype_testimonial_desigstory' ] ) ) {
    update_post_meta( $post_id, 'vw_corporate_pro_posttype_testimonial_desigstory', sanitize_text_field($_POST[ 'vw_corporate_pro_posttype_testimonial_desigstory']) );
  }
}

add_action( 'save_post', 'vw_corporate_pro_posttype_bn_metadesig_save' );
/*------------------- Testimonial Shortcode -------------------------*/
function vw_corporate_pro_posttype_testimonials_func( $atts ) {
    $testimonial = ''; 
    $testimonial = '<div id="testimonials"><div class="row inner-test-bg">';
      $new = new WP_Query( array( 'post_type' => 'testimonials') );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'medium' );
          $url = $thumb['0'];
          $excerpt = vw_corporate_pro_string_limit_words(get_the_excerpt(),20);
          $designation = get_post_meta($post_id,'vw_corporate_pro_posttype_testimonial_desigstory',true);

          $testimonial .= '<div class="col-lg-6 col-md-6 mt-4"> 
                <div class="row m-0 shrtcod-pg">
                  <div class="col-md-6 p-3">';
                    if (has_post_thumbnail()){
                    $testimonial.= '<img src="'.esc_url($url).'">';
                    }
                    $testimonial.= '</div>
                  <div class="col-md-6">
                    <h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4> <cite>'.esc_html($designation).'</cite>
                  </div>
                  <div class="content_box pl-3 pr-3 w-100">
                    <div class="short_text pb-3">'.$excerpt.'</div>
                  </div>
                </div>
              </div><div class="clearfix"></div>';
          $k++;         
        endwhile; 
        wp_reset_postdata();
        $testimonial.= '</div>';
      else :
        $testimonial = '<div id="testimonial" class="testimonial_wrap col-md-3 mt-3 mb-4"><h2 class="center">'.__('Not Found','vw-corporate-pro-posttype').'</h2></div></div></div>';
      endif;
    return $testimonial;
}
add_shortcode( 'vw-corporate-pro-testimonials', 'vw_corporate_pro_posttype_testimonials_func' );