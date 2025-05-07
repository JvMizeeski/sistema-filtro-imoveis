<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

use Elementor\WPNotificationsPackage\V110\Notifications as ThemeNotifications;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.3.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}
		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}
		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}
		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support('html5', [ 'search-form','comment-form','comment-list','gallery','caption','script','style' ]);
			add_theme_support('custom-logo',[ 'height'=>100, 'width'=>350, 'flex-height'=>true, 'flex-width'=>true ]);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );
			add_theme_support( 'editor-styles' );
			add_editor_style( 'editor-styles.css' );
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				add_theme_support( 'woocommerce' );
				add_theme_support( 'wc-product-gallery-zoom' );
				add_theme_support( 'wc-product-gallery-lightbox' );
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	$hello_theme_db_version = get_option( $theme_version_option_name );
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	function hello_elementor_display_header_footer() {
		return apply_filters( 'hello_elementor_header_footer', true );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	function hello_elementor_scripts_styles() {
		$min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style( 'hello-elementor', get_template_directory_uri() . '/style' . $min_suffix . '.css', [], HELLO_ELEMENTOR_VERSION );
		}
		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style( 'hello-elementor-theme-style', get_template_directory_uri() . '/theme' . $min_suffix . '.css', [], HELLO_ELEMENTOR_VERSION );
		}
		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style( 'hello-elementor-header-footer', get_template_directory_uri() . '/header-footer' . $min_suffix . '.css', [], HELLO_ELEMENTOR_VERSION );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) return;
		if ( ! is_singular() ) return;
		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) return;
		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">\n';
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

require get_template_directory() . '/includes/admin-functions.php';
require get_template_directory() . '/includes/settings-functions.php';
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() || ! hello_elementor_display_header_footer() ) return;
		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

function hello_elementor_get_theme_notifications(): ThemeNotifications {
	static $notifications = null;
	if ( null === $notifications ) {
		require get_template_directory() . '/vendor/autoload.php';
		$notifications = new ThemeNotifications( 'hello-elementor', HELLO_ELEMENTOR_VERSION, 'theme' );
	}
	return $notifications;
}

function sf_disable_term_redirect() {
	if ( is_tax() && isset($_GET['searchandfilter']) ) {
		remove_action('template_redirect', 'redirect_canonical');
	}
}
add_action('template_redirect', 'sf_disable_term_redirect', 1);

function mostrar_imoveis_filtrados_com_template() {
    ob_start();

    $search  = isset($_GET['ofsearch']) ? sanitize_text_field($_GET['ofsearch']) : '';
    $tipo    = isset($_GET['oftipo_imovel']) ? intval($_GET['oftipo_imovel']) : 0;
    $quartos = isset($_GET['ofquartos_padrao']) ? intval($_GET['ofquartos_padrao']) : 0;
    $status  = isset($_GET['ofstatus_do_imovel']) ? intval($_GET['ofstatus_do_imovel']) : 0;

    $paged = max(1, get_query_var('paged') ? get_query_var('paged') : get_query_var('page'));
    $posts_per_page = 6;

    // Verifica bairros que combinam com o texto digitado
    $bairros = [];
    if ($search) {
        $bairros_encontrados = get_terms([
            'taxonomy'   => 'bairro',
            'name__like' => $search,
            'hide_empty' => false,
        ]);

        if (!empty($bairros_encontrados) && !is_wp_error($bairros_encontrados)) {
            $bairros = wp_list_pluck($bairros_encontrados, 'term_id');
        }
    }

    $args = [
        'post_type'      => 'imovel',
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
    ];

    $filters_applied = false;

    if ($search || $tipo || $quartos || $status) {
        $filters_applied = true;
        $args['s'] = $search;
        $args['posts_per_page'] = -1;
        $args['tax_query'] = ['relation' => 'OR'];

        // Filtro por bairro via texto
        if (!empty($bairros)) {
            $args['tax_query'][] = [
                'taxonomy' => 'bairro',
                'field'    => 'term_id',
                'terms'    => $bairros
            ];
        }

        // Filtros por selects
        if ($tipo) {
            $args['tax_query'][] = [
                'taxonomy' => 'tipo_imovel',
                'field'    => 'term_id',
                'terms'    => $tipo
            ];
        }

        if ($quartos) {
            $args['tax_query'][] = [
                'taxonomy' => 'quartos_padrao',
                'field'    => 'term_id',
                'terms'    => $quartos
            ];
        }

        if ($status) {
            $args['tax_query'][] = [
                'taxonomy' => 'status_do_imovel',
                'field'    => 'term_id',
                'terms'    => $status
            ];
        }
    }

    if ($filters_applied) {
        echo '<div style="margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #0073aa;">';
        echo '<strong>🔎 Resultados filtrados por:</strong><ul style="margin:10px 0 0 20px;">';

        if ($search) {
            echo '<li><strong>Busca:</strong> ' . esc_html($search) . '</li>';
        }

        if ($tipo) {
            $term = get_term($tipo, 'tipo_imovel');
            if ($term && !is_wp_error($term)) {
                echo '<li><strong>Tipo de Imóvel:</strong> ' . esc_html($term->name) . '</li>';
            }
        }

        if ($quartos) {
            $term = get_term($quartos, 'quartos_padrao');
            if ($term && !is_wp_error($term)) {
                echo '<li><strong>Quartos:</strong> ' . esc_html($term->name) . '</li>';
            }
        }

        if ($status) {
            $term = get_term($status, 'status_do_imovel');
            if ($term && !is_wp_error($term)) {
                echo '<li><strong>Status:</strong> ' . esc_html($term->name) . '</li>';
            }
        }

        echo '</ul></div>';
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="grid-cards-imoveis">';
        while ($query->have_posts()) {
            $query->the_post();
            echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display(142);
        }
        echo '</div>';

        if (!$filters_applied) {
            echo '<div class="paginacao-imoveis">';
            echo paginate_links([
                'total' => $query->max_num_pages,
                'current' => $paged,
                'format' => '?paged=%#%',
                'prev_text' => __('« Anterior'),
                'next_text' => __('Próximo »')
            ]);
            echo '</div>';
        }

        wp_reset_postdata();
    } else {
        echo '<p>Nenhum imóvel encontrado.</p>';
    }

    echo '<style>
        .grid-cards-imoveis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        .paginacao-imoveis {
            text-align: center;
            margin-top: 40px;
        }
        .paginacao-imoveis .page-numbers {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 15px;
            background: #f1f1f1;
            color: #000;
            text-decoration: none;
        }
        .paginacao-imoveis .current {
            background: #0073aa;
            color: #fff;
        }
    </style>';

    return ob_get_clean();
}
add_shortcode('mostrar_imoveis_loop', 'mostrar_imoveis_filtrados_com_template');