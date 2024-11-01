<?php
/**
 * Contains action hooks and functions for user authentication.
 *
 * @class Ticktify_Event_Auth
 * @package ticktify-event\classes

 * @version 1.0.0
 */
if (!class_exists('Ticktify_Event_Auth')) :
    class Ticktify_Event_Auth
    {
        /**
         * Constructor for the auth class. Loads options and hooks in the init method.
         */
        public function __construct()
        {
            add_action('admin_post_ticktify_action_login', [$this, 'ticktify_action_login_callback']);
            add_action('admin_post_nopriv_ticktify_action_login', [$this, 'ticktify_action_login_callback']);
            add_action('admin_post_ticktify_action_lostpassword', [$this, 'ticktify_action_lostpassword_callback']);
            add_action('admin_post_nopriv_ticktify_action_lostpassword', [$this, 'ticktify_action_lostpassword_callback']);
            add_action('admin_post_ticktify_action_resetpassword', [$this, 'ticktify_action_resetpassword_callback']);
            add_action('admin_post_nopriv_ticktify_action_resetpassword', [$this, 'ticktify_action_resetpassword_callback']);
            add_action('admin_post_ticktify_action_register', [$this, 'ticktify_action_register_callback']);
            add_action('admin_post_nopriv_ticktify_action_register', [$this, 'ticktify_action_register_callback']);
            add_filter('lostpassword_url', [$this, 'ticktify_lostpassword_url'], 20, 1);
            add_action('wp_logout', [$this, 'ticktify_wp_logout_callback']);
        }
        /**
         * Login callback
         * 
         * Reponsible for login feature
         *
         */
        public function ticktify_action_login_callback()
        {
            // Make the WP_Error object global    
            global $form_error;
            $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
            // instantiate the class
            $form_error = new WP_Error();
            // If any field is left empty, add the error message to the error object
            if (empty($_POST['username'])) {
                $form_error->add('username_field', __('Username should not be left empty', "ticktify"));
            }
            if (empty($_POST['password'])) {
                $form_error->add('password_field', __('Password should not be left empty', "ticktify"));
            }
            if (!isset($_POST['_ticktify_nonce_login']) || !wp_verify_nonce(sanitize_text_field($_POST['_ticktify_nonce_login']), 'ticktify_nonce_login')) {
                $form_error->add('nonce_field', __('Nonce is invalid', "ticktify"));
            } else {
               
                if (isset($_POST['username'], $_POST['password'])) {
                    $remember_me = isset($_POST['rememberme']) && $_POST['rememberme'] == 1 ? true : false;
                    // process form data
                    $creds = array(
                        'user_login'    => sanitize_text_field($_POST['username']),
                        'user_password' => sanitize_text_field($_POST['password']),
                        'remember'      => $remember_me
                    );
                    if (!$form_error->has_errors()) {
                        $user = wp_signon($creds, false);

                        if (is_wp_error($user)) {
                            $form_error->add('login_invalid', $user->get_error_message());
                        } else {
                            wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_profile']));
                            exit();
                        }
                    }
                }
                if (is_wp_error($form_error)) {
                    ticktify_set_transient('ticktify_login_errors', $form_error->get_error_messages());

                    wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_login']) . '?er=true');
                    exit();
                }
            }
            if (is_wp_error($form_error)) {
                ticktify_set_transient('ticktify_login_errors', $form_error->get_error_messages());
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_login']) . '?er=true');
                exit();
            }
        }

        /**
         * Lost Password callback
         * 
         * Reponsible for Lost Password feature
         *
         */
        public function ticktify_action_lostpassword_callback()
        {
            // Make the WP_Error object global    
            global $form_error;
            $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
            // instantiate the class
            $form_error = new WP_Error();

            // If any field is left empty, add the error message to the error object
            if (empty($_POST['email'])) {
                $form_error->add('email_field', __('Email should not be left empty', "ticktify"));
            } else {
                if (!is_email($_POST['email'])) {
                    $form_error->add('email_field', __('Email is invalid', "ticktify"));
                }
            }
            if (!isset($_POST['_ticktify_nonce_lostpassword']) || !wp_verify_nonce(sanitize_text_field($_POST['_ticktify_nonce_lostpassword']), 'ticktify_nonce_lostpassword')) {
                $form_error->add('nonce_field', __('Nonce is invalid', "ticktify"));
            } else {
                //get user by its email
                $user = get_user_by('email', sanitize_email($_POST['email']));
                if ($user != false) {
                    //use get_password_reset_key
                    $reset_key = get_password_reset_key($user);
                    if (!is_wp_error($reset_key)) {
                        //send reset password email
                        $resetpass_link = get_permalink($ticktify_settings['pages']['ticktify_resetpassword']) . '?key=' . esc_attr($reset_key) . '&id=' . esc_attr($user->ID);
                        $to = $user->user_email;
                        $subject = esc_html(__('Password Reset Request for ', "ticktify")) . esc_html(get_bloginfo('name'));
                        $body = '
                        <p>Hi ' . esc_html($user->display_name) . ',</p>
                        <p>Someone has requested a new password for the following account on ' . esc_html(get_bloginfo('name')) . ':</p>
                        <p>Username: ' . esc_html($user->user_login) . '</p>
                        <p>If you didn\'t make this request, just ignore this email. If you\'d like to proceed:</p>
                        <p><a href="' . esc_url($resetpass_link) . '">Click here to reset your password</a></p>
                        ';
                        $headers = array('Content-Type: text/html; charset=UTF-8');
                        wp_mail($to, $subject, $body, $headers);

                        //redirect with success message 'Password reset email has been sent.'
                        $form_messages = new WP_Error();
                        $form_messages->add('lost_pass_sent', __('Password reset email has been sent.', "ticktify"));
                        ticktify_set_transient('ticktify_lostpassword_messages', $form_messages->get_error_messages());

                        wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_lostpassword']));
                        exit();
                    }
                    $form_error->add('email_field', $reset_key->get_error_message());
                } else {
                    $form_error->add('email_field', __('Email is invalid or We don\'t have any user with that email', "ticktify"));
                }
                if (is_wp_error($form_error)) {
                    ticktify_set_transient('ticktify_lostpassword_errors', $form_error->get_error_messages());
                    wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_lostpassword']) . '?er=true');
                    exit();
                }
            }
            if (is_wp_error($form_error)) {
                ticktify_set_transient('ticktify_lostpassword_errors', $form_error->get_error_messages());
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_lostpassword']) . '?er=true');
                exit();
            }
        }
        /**
         * Reset Password callback
         * 
         * Reponsible for Reset Password feature
         *
         */
        public function ticktify_action_resetpassword_callback()
        {
            // Make the WP_Error object global    
            global $form_error;
            $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
            // instantiate the class
            $form_error = new WP_Error();

            if (!isset($_POST['password'], $_POST['re_password'])) {
                $form_error->add('password_field', __('Passwords should not be left empty', "ticktify"));
                ticktify_set_transient('ticktify_resetpassword_errors', $form_error->get_error_messages());
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_resetpassword']) . '?er=true');
                exit();
            }
            // If any field is left empty, add the error message to the error object
            if (empty($_POST['password'])) {
                $form_error->add('password_field', __('Password should not be left empty', "ticktify"));
            }
            if (!isset($_POST['_ticktify_nonce_resetpassword']) || !wp_verify_nonce(sanitize_text_field($_POST['_ticktify_nonce_resetpassword']), 'ticktify_nonce_resetpassword')) {
                $form_error->add('nonce_field', __('Nonce is invalid', "ticktify"));
            } else {
                if (isset($_POST['password'], $_POST['re_password']) && $_POST['password'] != $_POST['re_password']) {
                    $form_error->add('password_field', __('Passwords do not match.', "ticktify"));
                }
                if (!$form_error->has_errors()) {
                    $user = $this->ticktify_check_password_reset_key(sanitize_text_field($_POST['reset_key']), sanitize_user($_POST['reset_login']));
                    $user = get_user_by('login', sanitize_user($_POST['reset_login']));

                    if ($user instanceof WP_User) {
                        //change user's password as requested
                        wp_set_password(sanitize_text_field($_POST['password']), sanitize_text_field($user->ID));

                        //send password change notification to the user
                        $to = $user->user_email;
                        $subject = esc_html(__('Your password has been changed successfully', "ticktify"));
                        $body = '
                        <p>Hi ' . esc_html($user->display_name) . ',</p>
                        <p>Your password has been changed successfully. You can now log in with your new password.</p>
                        ';
                        $headers = array('Content-Type: text/html; charset=UTF-8');
                        wp_mail($to, $subject, $body, $headers);

                        wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_login']) . '?password-reset=true');
                        exit;
                    }
                }
            }

            if ($form_error->has_errors()) {
                ticktify_set_transient('ticktify_resetpassword_errors', $form_error->get_error_messages());
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_resetpassword']) . '?er=true');
                exit();
            }
        }

        /**
         * Register callback
         * 
         * Reponsible for Register feature
         *
         */
        public function ticktify_action_register_callback()
        {
            // Make the WP_Error object global    
            global $form_error;
            $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
            // instantiate the class
            $form_error = new WP_Error();

            // If any field is left empty, add the error message to the error object

            if (empty($_POST['email'])) {
                $form_error->add('email_field', __('Email should not be left empty', "ticktify"));
            }

            if (empty($_POST['password'])) {
                $form_error->add('password_field', __('Password should not be left empty', "ticktify"));
            }

            if ($_POST['password']  != $_POST['confpass']) {
                $form_error->add('password_mismatch', __('please enter valid password', "ticktify"));
            }
            if ($_POST['password']  == '') {
                $form_error->add('password_empty', __('please enter valid password', "ticktify"));
            }
            if (!isset($_POST['_ticktify_nonce_register']) || !wp_verify_nonce(sanitize_text_field($_POST['_ticktify_nonce_register']), 'ticktify_nonce_register')) {
                $form_error->add('nonce_field', __('Nonce is invalid', "ticktify"));
            } else {
                if (isset($_POST['first'], $_POST['last'], $_POST['email'], $_POST['password'], $_POST['confpass'])) {
                    if (!is_email($_POST['email'])) {
                        $form_error->add('email_field', __('Email is invalid', "ticktify"));
                    }
                    if (email_exists(sanitize_email($_POST['email']))) {
                        $form_error->add('email_field', __('That E-mail is already registered', "ticktify"));
                    }
                    // process form data
                    $email_parts    = explode('@', sanitize_email($_POST['email']));
                    $email_username = sanitize_user($email_parts[0]);
                    if (username_exists($email_username)) {
                        $form_error->add('username_field', __('That Username is already registered', "ticktify"));
                    }
                    if (!$form_error->has_errors()) {
                        $user_id = wp_create_user($email_username, sanitize_text_field($_POST['password']), sanitize_email($_POST['email']));
                        if (is_wp_error($user_id)) {
                            $form_error->add('login_invalid', $user_id->get_error_message());
                        } else {
                            update_user_meta(sanitize_text_field($user_id), sanitize_key('first_name'), sanitize_text_field($_POST['first']));
                            update_user_meta(sanitize_text_field($user_id), sanitize_key('last_name'), sanitize_text_field($_POST['last']));

                            // instantiate the class
                            $form_messages = new WP_Error();
                            $form_messages->add('login_invalid', __('You are registered successfully!', "ticktify"));
                            ticktify_set_transient('ticktify_register_messages', $form_messages->get_error_messages());
                            // send email notification
                            $register_user_data = array(
                                'first_name' => sanitize_text_field($_POST['first']),
                                'last_name' => sanitize_text_field($_POST['last']),
                                'email' => sanitize_email($_POST['email']),
                            );
                            do_action('ticktify_after_registeration_successfully', $register_user_data);
                            wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_login']));
                            exit();
                        }
                    }
                }
                if (is_wp_error($form_error)) {
                    ticktify_set_transient('ticktify_register_errors', $form_error->get_error_messages());
                    wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_register']) . '?er=true');
                    exit();
                }
            }
            if (is_wp_error($form_error)) {
                ticktify_set_transient('ticktify_register_errors', $form_error->get_error_messages());
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_register']) . '?er=true');
                exit();
            }
        }
        /**
         * Lost Password url filter callback
         *  
         * Hook: lostpassword_url
         *
         * @return string URL to redirect
         */
        public function ticktify_lostpassword_url($default_url = '')
        {
            $ticktify_settings = get_option(sanitize_key('ticktify_settings'));

            $ticktify_lostpassword_page_url = get_permalink($ticktify_settings['pages']['ticktify_lostpassword']);

            if (!empty($ticktify_lostpassword_page_url)) {
                return $ticktify_lostpassword_page_url;
            } else {
                return $default_url;
            }
        }
        /**
         * Redirect after logout action hook callback
         *  
         * Hook: wp_logout
         *
         */
        public function ticktify_wp_logout_callback()
        {
            wp_redirect(site_url());
            exit();
        }
        /**
         * Varify password reset key, if not varified then redirect user with errors
         *  
         * @return string rp_key 
         * @return string rp_login
         */
        public static function ticktify_redirect_resetpassword_link()
        {
            $ticktify_settings = get_option(sanitize_key('ticktify_settings'));

            if (isset($_GET['key'], $_GET['id'])) {
                $user_id = absint(sanitize_text_field($_GET['id']));
                $rp_key = sanitize_text_field($_GET['key']);

                $userdata               = get_userdata(absint($user_id));
                $rp_login               = $userdata ? $userdata->user_login : '';
                $user                   = Ticktify_Event_Auth::ticktify_check_password_reset_key($rp_key, $rp_login);
            
             
                if ($user instanceof WP_Error) {
                    $form_errors = new WP_Error();
                    $datasend = Array('data' => 'logininvalid');
                    return $datasend;
        
                    // $form_errors->add('login_invalid', __('This key is invalid or has already been used. Please reset your password again if needed.', "ticktify"));
                    // ticktify_set_transient('ticktify_lostpassword_errors', $form_errors->get_error_messages());
                    // wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_lostpassword']) . '?reset-form=false');
                    // exit;
                }
                return [$rp_key, $rp_login];
            }
            $form_errors = new WP_Error();
            $form_errors->add('login_invalid', __('This key is invalid or has already been used. Please reset your password again if needed.', "ticktify"));
            ticktify_set_transient('ticktify_lostpassword_errors', $form_errors->get_error_messages());
            wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_lostpassword']) . '?reset-form=false');
            exit;
        }

        public function ticktify_save_reset_password_cookie($value = '')
        {
            $rp_cookie = 'wp-resetpass-' . COOKIEHASH;
            if(isset($_SERVER['REQUEST_URI'])){
                $request_url = sanitize_url($_SERVER['REQUEST_URI']);
                $rp_path   = current(explode('?', wp_unslash($request_url)));
            }else{
                $rp_path   ='';
            }
            if ($value) {
                setcookie($rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
            } else {
                setcookie($rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
            }
        }

        /**
         * Verify password reset key
         *  
         * @return Object user User Object
         */
        public static function ticktify_check_password_reset_key($key, $login)
        {
            // Check for the password reset key.
            // Get user data or an error message in case of invalid or expired key.
            $user = check_password_reset_key($key, $login);

            return $user;
        }
    }

    new Ticktify_Event_Auth();

// EOF
endif;
