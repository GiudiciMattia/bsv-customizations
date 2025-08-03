<?php
// File shortcode placeholder

function immich_album_list_shortcode($atts) {
    $json_url = 'https://www.bresciavolley.it/IMMICH-ALBUMS/immich_albums.json';
    $base_url = 'https://cloud-photos.mattiagiudici.eu/share/';
    $fallback_cover = 'https://www.bresciavolley.it/wp-content/uploads/2025/07/Immagine1-2.png';

    $response = wp_remote_get($json_url);
    $albums = [];

    if (is_array($response) && !is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        if (!empty($body)) {
            $parsed = json_decode($body, true);
            if (is_array($parsed)) {
                $albums = $parsed;
            }
        }
    }

    if (empty($albums)) {
        return '<p>Impossibile caricare gli album. Riprova più tardi.</p>';
    }

    $atts = shortcode_atts([
        'per_page' => 9,
        'page' => isset($_GET['album_page']) ? max(1, intval($_GET['album_page'])) : 1,
    ], $atts);

    $per_page = min((int) $atts['per_page'], 9);
    $total_albums = count($albums);
    $total_pages = max(1, ceil($total_albums / $per_page));
    $page = max(1, min($atts['page'], $total_pages));
    $start = ($page - 1) * $per_page;

    $output = '<div class="yuki-posts-container yuki-container lg:flex flex-grow container mx-auto px-gutter yuki-no-sidebar">';
    $output .= '<div id="content" class="yuki-posts flex-grow max-w-full">';

    // CSS responsive e uniforme
    $output .= '<style>
        .album-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 16px;
            max-width: 100%;
        }
        @media (min-width: 640px) {
            .album-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (min-width: 1024px) {
            .album-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        .album-cover-wrapper {
            width: 100%;
            height: 350px;
            overflow: hidden;
            border-radius: 6px;
            position: relative;
        }
        .album-cover-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .album-meta {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 6px;
            text-align: center;
        }
        .title-top-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.6);
            color: #fff;
            padding: 8px 10px;
            font-size: 1.1rem;
            font-weight: 600;
            text-align: center;
            z-index: 1;
        }
    </style>';

    
    
    $output .= '<div class="card-list album-grid">';

    foreach (array_slice($albums, $start, $per_page) as $album) {
        $code = esc_attr($album['code']);
        $raw_title = $album['title'];
        $parts = explode(' - ', $raw_title, 3);

        if (count($parts) === 3) {
            $stagione = esc_html(trim($parts[0]));
            $data_raw = trim($parts[1]);
            $testo = esc_html(trim($parts[2]));

            $data_formattata = DateTime::createFromFormat('Ymd', $data_raw);
            $data_ita = $data_formattata ? $data_formattata->format('d/m/Y') : esc_html($data_raw);
        } else {
            $stagione = '';
            $data_ita = '';
            $testo = esc_html($raw_title);
        }

        $cover_id = !empty($album['cover']) ? $album['cover'] : null;
        $cover = $cover_id
            ? 'https://cloud-photos.mattiagiudici.eu/api/assets/' . $cover_id . '/thumbnail?size=preview&key=' . $code
            : $fallback_cover;
        $url = esc_url($base_url . $code);

        $output .= '<div class="card-wrapper">';
        $output .= '<article class="card-thumb-motion yuki-scroll-reveal card overflow-hidden h-full" data-card-layout="archive-grid">';
        $output .= '<div class="card-content">';
        $output .= '<p class="album-meta"><strong>' . $stagione . ' – ' . $data_ita . '</strong></p>';
        $output .= '</div>';
        $output .= '<div class="card-thumbnail entry-thumbnail last:mb-0" onclick="openAlbumModal(\'' . $url . '\')" style="cursor:pointer;">';
        $output .= '<div class="album-cover-wrapper">';
        $output .= '<div class="title-top-overlay">' . $testo . '</div>';
        $output .= '<img class="album-cover-img" src="' . esc_url($cover) . '" alt="Copertina di ' . esc_attr($testo) . '" title="' . esc_attr($testo) . '" onerror="this.onerror=null;this.src=\'' . $fallback_cover . '\';">';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</article>';
        $output .= '</div>';
    }

    $output .= '</div>'; // .album-grid

    // Paginazione
    if ($total_pages > 1) {
        $output .= '<nav class="yuki-pagination yuki-scroll-reveal yuki-archive-pagination" data-pagination-type="numbered" style="margin-top: 20px; text-align: center;">';
        for ($i = 1; $i <= $total_pages; $i++) {
            $query_args = ['album_page' => $i];
            $page_url = add_query_arg($query_args, get_permalink());
            $class = ($i == $page) ? 'yuki-btn yuki-btn-active' : 'yuki-btn';
            $output .= '<a class="' . $class . '" href="' . esc_url($page_url) . '">' . $i . '</a> ';
        }
        $output .= '</nav>';
    }

    $output .= '</div>'; // #content
    $output .= '</div>'; // .container

    // Modal con iframe
    $output .= '
    <div id="immich-modal" style="display:none; position:fixed; top:5%; left:5%; width:90%; height:90%; background:#fff; z-index:9999; box-shadow:0 0 20px rgba(0,0,0,0.8);">
        <div style="text-align:right; padding:8px;">
            <button onclick="closeAlbumModal()" style="font-size:20px; background:red; color:white; border:none; padding:6px 12px; cursor:pointer;">✕</button>
        </div>
        <iframe id="immich-iframe" src="" style="width:100%; height:calc(100% - 50px); border:none;"></iframe>
    </div>
    <script>
    function openAlbumModal(url) {
        document.getElementById("immich-iframe").src = url;
        document.getElementById("immich-modal").style.display = "block";
        document.body.style.overflow = "hidden";
    }
    function closeAlbumModal() {
        document.getElementById("immich-modal").style.display = "none";
        document.getElementById("immich-iframe").src = "";
        document.body.style.overflow = "";
    }
    </script>';

    return $output;
}
add_shortcode('immich_album_list', 'immich_album_list_shortcode');


