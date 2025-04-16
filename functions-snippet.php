
function mostrar_imoveis_filtrados_com_template() {
    ob_start();

    $search  = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $tipo    = isset($_GET['oftipo_imovel']) ? intval($_GET['oftipo_imovel']) : 0;
    $quartos = isset($_GET['ofquartos']) ? intval($_GET['ofquartos']) : 0;
    $status  = isset($_GET['ofstatus_do_imovel']) ? intval($_GET['ofstatus_do_imovel']) : 0;

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $posts_per_page = 6;

    $args = [
        'post_type' => 'imovel',
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
    ];

    if ($search || $tipo || $quartos || $status) {
        $args['s'] = $search;
        $args['posts_per_page'] = -1;
        $args['tax_query'] = ['relation' => 'AND'];

        if ($tipo) {
            $args['tax_query'][] = [
                'taxonomy' => 'tipo_imovel',
                'field'    => 'term_id',
                'terms'    => $tipo
            ];
        }

        if ($quartos) {
            $args['tax_query'][] = [
                'taxonomy' => 'quartos',
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

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        echo '<div class="grid-cards-imoveis">';
        while ($query->have_posts()) : $query->the_post();
            echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display(142);
        endwhile;
        echo '</div>';

        if (!$search && !$tipo && !$quartos && !$status) {
            echo '<div class="paginacao-imoveis">';
            echo paginate_links([
                'total' => $query->max_num_pages,
                'current' => $paged,
                'format' => '?paged=%#%',
                'prev_text' => __('&laquo; Anterior'),
                'next_text' => __('Próximo &raquo;')
            ]);
            echo '</div>';
        }

        wp_reset_postdata();
    else :
        echo '<p>Nenhum imóvel encontrado.</p>';
    endif;

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
