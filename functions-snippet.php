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
