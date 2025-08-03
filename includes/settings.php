<?php
if (!defined('ABSPATH')) exit;

// Registra l'opzione per il fallback
add_action('admin_init', 'bsv_immich_register_settings');
function bsv_immich_register_settings() {
    register_setting('bsv_immich_settings_group', 'bsv_immich_fallback_cover');
}

// Funzione per renderizzare la pagina (usata dal menu BSV)
function bsv_immich_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Impostazioni Immich Connector</h1>
        <form method="post" action="options.php">
            <?php settings_fields('bsv_immich_settings_group'); ?>
            <?php do_settings_sections('bsv_immich_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Immagine Fallback</th>
                    <td>
                        <input type="text" id="bsv_immich_fallback_cover" name="bsv_immich_fallback_cover" value="<?php echo esc_attr(get_option('bsv_immich_fallback_cover')); ?>" style="width:60%;" />
                        <input type="button" class="button" value="Scegli immagine" id="upload_fallback_cover_btn" />
                        <div><img id="fallback_cover_preview" src="<?php echo esc_url(get_option('bsv_immich_fallback_cover')); ?>" style="max-width:200px; margin-top:10px;"></div>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($){
        $('#upload_fallback_cover_btn').click(function(e) {
            e.preventDefault();
            var image = wp.media({ title: 'Seleziona Immagine', multiple: false }).open()
            .on('select', function(){
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#bsv_immich_fallback_cover').val(image_url);
                $('#fallback_cover_preview').attr('src', image_url);
            });
        });
    });
    </script>
    <?php
}
