<?php

namespace Rezah\Modules\ApiWordpress;

use Firebase\JWT\JWT;

class Api_Actions {

     function __construct() {
        add_action('rest_api_init', [$this, 'register_posts_endpoint'] );
        add_action('rest_api_init', [$this, 'register_update_post_endpoint']);
        add_action('rest_api_init', [$this, 'register_userinfo_endpoint']);
        add_action('rest_api_init', [$this, 'register_newpost_endpoint']);
        add_filter( 'jwt_auth_token_before_dispatch', [$this, 'function_jwt_auth_token_before_dispatch'], 10, 2);
        add_filter( 'jwt_auth_token_expired', [$this, 'function_jwt_auth_token_expired'], 10, 3 );
        add_filter( 'jwt_auth_token_before_sign', [$this, 'function_jwt_auth_token_before_sign'], 10, 2 );
     }

    //filter token expired
    public function function_jwt_auth_token_before_dispatch( $data, $user ){
      $data['user_id'] = $user->ID;

      /*$refreshTokenExpTime = time() + (30 * 24 * 60 * 160); 
      $refreshTokenPayload = array(
          "iss" => home_url(),
          "iat" => time(),
          "nbf" => time(),
          "exp" => $refreshTokenExpTime,
          "data" => array(
              "user" => array(
                  "id" => $user->ID,
              ),
          ),
          "user_id" => $user->ID,
      );
      $refreshToken = JWT::encode($refreshTokenPayload, AUTH_KEY, "HS256");
      $data['refresh_token'] = $refreshToken;*/

      $data['refresh_token'] = md5( $user->ID . time() );
      return $data;
    }

    public function function_jwt_auth_token_expired( $expired, $issuedAt, $ttl  ){
      //$new_ttl = 60 * 60 * 24 * 30; 
      return $issuedAt + TWO_MONTHS < time();
    }


    public function function_jwt_auth_token_before_sign( $data, $user ){
      $data['user_id'] = $user->ID;
      return $data;
    }

    /**
     * Endpoint for add new post
     */
    public function register_newpost_endpoint() {

      register_rest_route( 'custom_api/v1', '/add-post', array(
        'methods' => 'POST',
        'callback' => [$this, 'custom_api_insert_post'],
        'permission_callback' => function () {
            return true;
        }
    ) );
    }

    /**
     * Endpoint for user info
     */
    public function register_userinfo_endpoint() {

      register_rest_route( 'jwt-auth/v1', '/user', array(
        'methods' => 'GET',
        'callback' => [ $this, 'get_user_info' ],
        'permission_callback' => function () {
           return current_user_can( 'edit_posts' );
       }
      ) );
    }



    /**
     * Register REST API endpoint for retrieving all posts.
     */
    public function register_posts_endpoint() {
      register_rest_route( 'custom_api/v1', '/posts', [
          'methods' => \WP_REST_Server::READABLE,
          'callback' => [ $this, 'get_all_posts' ]
       ]);
  }

    /**
     * Register REST API endpoint for updating a post by ID.
     */
    public function register_update_post_endpoint() {
      register_rest_route( 'jwt-auth/v1', '/update-post/(?P<id>\d+)', [
        'methods' => \WP_REST_Server::EDITABLE,
        'callback' => [ $this, 'update_post_by_id' ],
        'permission_callback' => function () {
          return current_user_can( 'edit_posts' );
        }
      ]);
    }

     /**
      * Get all posts.
      *
      * @param \WP_REST_Request $request   The REST request object.
      * @return array
      */
     public function get_all_posts( \WP_REST_Request $request ) : array {
        $args = [
          'post_type' => 'post',
          'post_status' => 'publish',
          'posts_per_page' => -1,
        ];
      
        $query = new \WP_Query( $args );
      
        $response = [];

        
        foreach( $query->get_posts() as $post ) {
          $response[] = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'date' => $post->post_date,
            'author_name' => get_the_author_meta( 'user_login', $post->post_author ),
            'featured_image' => get_the_post_thumbnail_url( $post->ID, 'full' ),
            'permalink' => get_permalink( $post->ID ),
          ];
        }
      
        return $response;
      }

      /**
           * Update a post by ID.
           *
           * @param WP_REST_Request $request The REST request object.
           * @return array|WP_Error
           */
          public function update_post_by_id( \WP_REST_Request $request ) {
            
            $id = (int) $request['id'];
                  
            // Update post data
            $post = get_post($id);

            if (!$post) {
              return new \WP_Error('invalid_post', __('Invalid post ID'), array('status' => 404));
            }

            $updates = $request->get_params();

            $post_data = array();

            foreach ($updates as $key => $value) {
              // Use wp_kses_post() to strip all HTML tags except those considered safe.
              $post_data[$key] = wp_kses_post($value);
            }

            // check if there is a file in the request
            if (isset($_FILES['featured_image'])) {
                    $featured_image = $_FILES['featured_image'];

                                // check if the file was uploaded successfully
                if ($featured_image['error'] === UPLOAD_ERR_OK) {

                      //Update the post, using the $post_data array created above
                      wp_update_post(array(
                        'ID' => $id,
                        'post_content' => $post_data['post_content'],
                        'post_title' => $post_data['post_title']
                      ));
                
                      // upload the featured image and set it as the post thumbnail
                      $upload = wp_upload_bits($featured_image['name'], null, file_get_contents($featured_image['tmp_name']));
                      if (!$upload['error']) {
                        $attachment = array(
                          'post_mime_type' => $featured_image['type'],
                          'post_title' => sanitize_file_name($featured_image['name']),
                          'post_content' => '',
                          'post_status' => 'inherit'
                        );
                        $attachment_id = wp_insert_attachment($attachment, $upload['file'], $id);
                        if (!is_wp_error($attachment_id)) {
                          require_once(ABSPATH . 'wp-admin/includes/image.php');
                          $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                          wp_update_attachment_metadata($attachment_id, $attachment_data);
                          set_post_thumbnail($id, $attachment_id);
                        }
                      }
                
                      // return the post ID as a response
                      return new \WP_REST_Response(array(
                        'post_id' => $id
                      ), 200);
                
                } else {
                      // handle file upload errors
                      return new \WP_REST_Response(array(
                        'error' => 'File upload failed.'
                      ), 400);
                 }
                
                  } else {

                    wp_update_post(array(
                      'ID' => $id,
                      'post_content' => $post_data['post_content'],
                      'post_title' => $post_data['post_title']
                    ));
                    // handle missing file error
                    return new \WP_REST_Response(array(
                      'sucess' => 'No file detected in the request but this post is updated'
                    ));
                  }

          } 


        

          /**
           * get user info
           */

          public function get_user_info() {
            $user = wp_get_current_user();
            error_log(print_r($user, true)); // log the $user object to the error log

            $profile_picture = get_user_meta($user->ID, 'profilepicture', true);

            return array(
              'display_name' => $user->display_name,
              'first_name' => $user->user_firstname,
              'last_name' => $user->user_lastname,
              'user_email' => $user->user_email,
              'url_picture' => $profile_picture
            );
        }

        /**
        * register post
        */       
        public function custom_api_insert_post( \WP_REST_Request $request ) {
          // get the title, content, and featured image from the request
          $title = $request['title'];
          $content = $request['content'];
          $user_id = $request['user_id'];
         
        
          // check if there is a file in the request
          if (isset($_FILES['featured_image'])) {
            $featured_image = $_FILES['featured_image'];
        
            // check if the file was uploaded successfully
            if ($featured_image['error'] === UPLOAD_ERR_OK) {
              // create a new post with the data
              $post = array(
                'post_title' => $title,
                'post_content' => $content,
                'post_status' => 'publish',
                'post_type' => 'post',
                'post_author' => $user_id
              );
              $post_id = wp_insert_post($post);
        
              // check if the post was created successfully
              if (is_wp_error($post_id)) {
                return new \WP_REST_Response(array(
                  'error' => $post_id->get_error_message()
                ), 400);
              }
        
              // upload the featured image and set it as the post thumbnail
              $upload = wp_upload_bits($featured_image['name'], null, file_get_contents($featured_image['tmp_name']));
              if (!$upload['error']) {
                $attachment = array(
                  'post_mime_type' => $featured_image['type'],
                  'post_title' => sanitize_file_name($featured_image['name']),
                  'post_content' => '',
                  'post_status' => 'inherit'
                );
                $attachment_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
                if (!is_wp_error($attachment_id)) {
                  require_once(ABSPATH . 'wp-admin/includes/image.php');
                  $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                  wp_update_attachment_metadata($attachment_id, $attachment_data);
                  set_post_thumbnail($post_id, $attachment_id);
                }
              }
        
              // return the post ID as a response
              return new \WP_REST_Response(array(
                'post_id' => $post_id
              ), 200);
        
            } else {
              // handle file upload errors
              return new \WP_REST_Response(array(
                'error' => 'File upload failed.'
              ), 400);
            }
        
          } else {
            // handle missing file error
            return new \WP_REST_Response(array(
              'error' => 'No file detected in the request.'
            ), 400);
          }
        }
            
}