<?php

    // HOOKS
    add_action('wp_enqueue_scripts', 'wwgcbar_scripts');
    add_action('admin_init', 'wwgcbar_admin_scripts');
    add_action('init', 'wwgcbar_register_shortcode');
    add_action('wp_footer', 'wwgcbar_footer_code');
    add_action('wp_head', 'wwgcbar_header_code');

    function wwgcbar_scripts()
    {
    global $pluginName;

    // Use plugin version for cache busting instead of rand()
    $plugin_version = get_file_data(__FILE__, ['Version' => 'Version'])['Version'] ?? '1.0.0';

    wp_register_style('wwgcbar_main_style', plugins_url() . '/' . $pluginName . '/includes/stylesheets/ww-gdpr-screen.css', [], $plugin_version, 'all');
    wp_enqueue_style('wwgcbar_main_style');

    wp_register_script('wwgcbar_main_script', plugins_url() . '/' . $pluginName . '/includes/js/ww-gdpr-scripts.js', ['jquery'], $plugin_version, true);
    wp_enqueue_script('wwgcbar_main_script');
    }

    if (is_admin()) {
    function wwgcbar_admin_scripts()
    {
        global $pluginName;

        $plugin_version = get_file_data(__FILE__, ['Version' => 'Version'])['Version'] ?? '1.0.0';

        wp_register_style('wwgcbar_admin_style', plugins_url() . '/' . $pluginName . '/includes/stylesheets/ww-gdpr-admin-screen.css', [], $plugin_version, 'all');
        wp_enqueue_style('wwgcbar_admin_style');

        wp_register_script('wwgcbar_admin_scripts', plugins_url() . '/' . $pluginName . '/includes/js/ww-gdpr-admin-scripts.js', ['jquery'], $plugin_version, true);
        wp_enqueue_script('wwgcbar_admin_scripts');
    }
    }

    function wwgcbar_header_code()
    {
    global $wwgcbar_options;

    if (isset($wwgcbar_options['content_tracking_code']) && $wwgcbar_options['content_tracking_code']) {
        $ga_code = $wwgcbar_options['content_tracking_code'];
    }

    if (empty($ga_code)) {
        return;
    }

    echo "<script>
  // --- Google Consent Mode v2 (DEFAULT: DENIED) ---
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('consent', 'default', {
    analytics_storage: 'denied',
    ad_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied'
  });
  // --- GA loader (guarded against double load) ---
  window.__loadGA = function () {
    if (window.__gaLoaded) return;
    window.__gaLoaded = true;
    const s = document.createElement('script');
    s.async = true;
    s.src = 'https://www.googletagmanager.com/gtag/js?id=" . $ga_code . "';
    document.head.appendChild(s);
    s.onload = function () {
      gtag('js', new Date());
      gtag('config', '" . $ga_code . "', {
        anonymize_ip: true
      });
    };
  };
  // --- Load GA immediately if consent already exists ---
  if (localStorage.getItem('non_essential_cookies') === 'true') {
    gtag('consent', 'update', { analytics_storage: 'granted' });
    window.__loadGA();
  }
</script>";
    }

    // BAR CONTENT
    function wwgcbar_footer_code()
    {
    global $wwgcbar_options;

    if (isset($wwgcbar_options['content_tracking_code']) && $wwgcbar_options['content_tracking_code']) {
        $ga_code = $wwgcbar_options['content_tracking_code'];
    }

    if (empty($ga_code)) {
        return;
    }

    // Check if plugin is enabled
    if (! isset($wwgcbar_options['enable']) || ! $wwgcbar_options['enable']) {
        return;
    }

    ob_start();
    ?>
    <style>
        .wwgcbar-content {
            <?php if (! empty($wwgcbar_options['position'])): ?>
            top: -150%;
            <?php else: ?>
            bottom: -150%;
            <?php endif; ?>
        }
        .wwgcbar-content.active {
            <?php if (! empty($wwgcbar_options['position'])): ?>
            top: 0;
            <?php else: ?>
            bottom: 0;
            <?php endif; ?>
        }
        .wwgcbar-content p {
            color: <?php echo ! empty($wwgcbar_options['content_col']) ? esc_attr($wwgcbar_options['content_col']) : '#FFFFFF'; ?>;
        }
        .wwgcbar-content a {
            color: <?php echo ! empty($wwgcbar_options['content_col_link']) ? esc_attr($wwgcbar_options['content_col_link']) : '#FFFFFF'; ?>;
        }
        .wwgcbar-content .wwgcbar-btn1 {
            color: <?php echo ! empty($wwgcbar_options['button_1_col']) ? esc_attr($wwgcbar_options['button_1_col']) : '#FFFFFF'; ?>;
            background-color: <?php echo ! empty($wwgcbar_options['button_1_bg']) ? esc_attr($wwgcbar_options['button_1_bg']) : '#45af0c'; ?>;
        }
        .wwgcbar-content .wwgcbar-btn2 {
            color: <?php echo ! empty($wwgcbar_options['button_2_col']) ? esc_attr($wwgcbar_options['button_2_col']) : '#FFFFFF'; ?>;
            background-color: <?php echo ! empty($wwgcbar_options['button_2_bg']) ? esc_attr($wwgcbar_options['button_2_bg']) : '#e27a18'; ?>;
        }
    </style>
    <div id="wwgcbar" class="wwgcbar-content" style="background:<?php echo ! empty($wwgcbar_options['content_bg']) ? esc_attr($wwgcbar_options['content_bg']) : '#000000'; ?>;">
        <div class="container">
            <div class="left">
                <p>
                <?php if (! empty($wwgcbar_options['content'])): ?>
                    <?php echo wp_kses_post($wwgcbar_options['content']); ?>
                <?php else: ?>
                    We use cookies to ensure that we give you the best possible experience on our website. By using this site you agree to our <a href="<?php echo isset($wwgcbar_options['pp_link']) ? esc_url($wwgcbar_options['pp_link']) : ''; ?>"<?php echo isset($wwgcbar_options['pp_target']) ? ' target="_blank" rel="noopener"' : ''; ?>>Privacy Policy</a>
                <?php endif; ?>
                </p>
            </div>
            <div class="right">
                <?php if (! isset($wwgcbar_options['buttons_swap']) || ! $wwgcbar_options['buttons_swap']): ?>
                <span class="wwgcbar-btn wwgcbar-btn1" id="wwgcbarAcceptBtn">
                    <?php if (isset($wwgcbar_options['button_1_text']) && $wwgcbar_options['button_1_text']): ?>
                        <?php echo esc_html($wwgcbar_options['button_1_text']); ?>
                    <?php else: ?>
                        Accept
                    <?php endif; ?>
                </span>
                <span class="wwgcbar-btn wwgcbar-btn2" id="settingsBtn">
                    <?php if (isset($wwgcbar_options['button_2_text']) && $wwgcbar_options['button_2_text']): ?>
                        <?php echo esc_html($wwgcbar_options['button_2_text']); ?>
                    <?php else: ?>
                        Cookie settings
                    <?php endif; ?>
                </span>
                <?php else: ?>
                <span class="wwgcbar-btn wwgcbar-btn2" id="settingsBtn">
                    <?php if (isset($wwgcbar_options['button_2_text']) && $wwgcbar_options['button_2_text']): ?>
                        <?php echo esc_html($wwgcbar_options['button_2_text']); ?>
                    <?php else: ?>
                        Cookie settings
                    <?php endif; ?>
                </span>
                <span class="wwgcbar-btn wwgcbar-btn1" id="acceptBtn">
                    <?php if (isset($wwgcbar_options['button_1_text']) && $wwgcbar_options['button_1_text']): ?>
                        <?php echo esc_html($wwgcbar_options['button_1_text']); ?>
                    <?php else: ?>
                        Accept
                    <?php endif; ?>
                </span>
                <?php endif; ?>
            </div>
            <div class="wwgcbar-clear"></div>
        </div>
    </div>

    <!-- SETTINGS MODAL -->
    <div class="wwgcbar-modal-wrapper hidden" id="wwgcbar-modal">
        <div class="wwgcbar-modal">
            <span id="wwgcbar-modal-close" aria-label="Close cookie settings">&times;</span>
            <div class="wwgcbar-policy-overview">
                <h2>Cookie settings</h2>
                <p>
                    <?php if (isset($wwgcbar_options['content1']) && $wwgcbar_options['content1']): ?>
                        <?php echo wp_kses_post($wwgcbar_options['content1']); ?>
                    <?php else: ?>
                        This website uses cookies to improve your online experience. Some of the cookies (categorised as 'necessary') are stored on your browser as they are essential for the basic functionality of the website.
                    <?php endif; ?>
                </p>

                <p class="wwgcbar-description hidden">
                    <?php if (isset($wwgcbar_options['content2']) && $wwgcbar_options['content2']): ?>
                        <?php echo wp_kses_post($wwgcbar_options['content2']); ?>
                    <?php else: ?>
                        Other third-party cookies (categorised as 'non-necessary') help us analyse and understand how you use the website. These cookies will be stored in your browser only with your consent. You have the option to disable (opt-out) of these cookies, but it may affect your browsing experience.
                    <?php endif; ?>
                </p>
                <p class="wwgcbar-show-description">Show more</p>
            </div>
            <div class="wwgcbar-row">
                <div class="wwgcbar-modal-left">
                    <h3>Necessary</h3>

                    <p class="wwgcbar-description hidden">
                        <?php if (isset($wwgcbar_options['content3']) && $wwgcbar_options['content3']): ?>
                            <?php echo wp_kses_post($wwgcbar_options['content3']); ?>
                        <?php else: ?>
                            Necessary cookies enable the website to function properly. They also include essential security features to protect the website. These cookies do not store any personal information.
                        <?php endif; ?>
                    </p>
                    <span class="wwgcbar-show-description">Show more</span>
                </div>
                <div class="wwgcbar-modal-right">
                    <p>Always Enabled</p>
                </div>
                <div class="wwgcbar-clear"></div>
            </div>

            <div class="wwgcbar-row">
                <div class="wwgcbar-modal-left">
                    <h3>Non-Necessary</h3>

                    <p class="wwgcbar-description hidden">
                    <?php if (isset($wwgcbar_options['content4']) && $wwgcbar_options['content4']): ?>
                        <?php echo wp_kses_post($wwgcbar_options['content4']); ?>
                    <?php else: ?>
                        Termed 'non-necessary' cookies, these are used specifically to collect personal user data via analytics, ads, and other embedded content. It is mandatory to procure user consent prior to running these cookies on the website. Please choose to enable or disable our website cookies using the selection switch.
                    <?php endif; ?>
                    </p>
                    <span class="wwgcbar-show-description">Show more</span>
                </div>

                <div class="wwgcbar-modal-right">
                    <label class="switch">
                        <input
                            name="wwgcbar_settings[cookies_non_essential]"
                            type="checkbox"
                            id="wwgcbarCookiesNonEssential"
                            value="1"
                            <?php checked(1, isset($wwgcbar_options['cookies_non_essential']) ? $wwgcbar_options['cookies_non_essential'] : 1); ?>
                        >
                        <span class="switch-slider round"></span>
                    </label>
                </div>

                <div class="wwgcbar-clear"></div>
            </div>
        </div>
    </div>

	<script>
	  const cookieBar = document.getElementById('wwgcbar');
	  const cookiesNonEssential = document.getElementById('wwgcbarCookiesNonEssential');
	  const acceptBtn = document.getElementById('wwgcbarAcceptBtn');

	  // Show banner only if no decision yet
	  if (!localStorage.getItem('non_essential_cookies')) {
	  	setTimeout(()=>{ cookieBar.classList.add('active'); },5000);
	  }

	  acceptBtn.addEventListener('click', () => {
	    const consentGranted = cookiesNonEssential.checked;
	    const consentValue = consentGranted ? 'true' : 'false';

	    localStorage.setItem('non_essential_cookies', consentValue);
	    cookieBar.classList.remove('active');

	    gtag('consent', 'update', {
	      analytics_storage: consentGranted ? 'granted' : 'denied'
	    });

	    if (consentGranted) {
	      window.__loadGA(); // start GA immediately
	    }
	  });
	</script>


<?php
    echo ob_get_clean();
    }

    function wwgcbar_register_shortcode()
    {
    add_shortcode('wwgcbar', 'wwgcbar_shortcode');
    }

    function wwgcbar_shortcode($args)
    {
    global $wwgcbar_options;

    $args = shortcode_atts([], $args, 'wwgcbar');

    ob_start();

    if (isset($wwgcbar_options['cookie_shortcode']) && $wwgcbar_options['cookie_shortcode']) {
        // Allow basic HTML for custom shortcode but sanitize it
        $allowed_shortcode_html = [
            'a'      => [
                'href'  => [],
                'id'    => [],
                'class' => [],
                'style' => [],
                'title' => [],
            ],
            'span'   => [
                'class' => [],
                'style' => [],
            ],
            'div'    => [
                'class' => [],
                'style' => [],
            ],
            'button' => [
                'type'  => [],
                'class' => [],
                'style' => [],
                'id'    => [],
            ],
        ];
        echo wp_kses($wwgcbar_options['cookie_shortcode'], $allowed_shortcode_html);
    } else {
        echo '<a id="wwgcbar-collapsed" href="#">Cookies</a>';
    }

    return ob_get_clean();
}