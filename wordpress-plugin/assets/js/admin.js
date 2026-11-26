jQuery(document).ready(function($) {
    var mediaUploader;
    
    $('.vs-upload-btn').on('click', function(e) {
        e.preventDefault();
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        mediaUploader = wp.media({
            title: 'Seleccionar imagen',
            button: {
                text: 'Usar esta imagen'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#photo_url').val(attachment.url);
            $('#vs-photo-preview').html('<img src="' + attachment.url + '" style="max-width:150px; max-height:150px; border-radius:8px;">');
        });
        
        mediaUploader.open();
    });
    
    // Normalizar categoría y tipo
    $('#category, #type').on('blur', function() {
        var val = $(this).val().trim();
        $(this).val(val);
    });
    
    // Copiar shortcodes
    $('.vs-copy-btn').on('click', function() {
        var shortcode = $(this).data('shortcode');
        var temp = $('<input>');
        $('body').append(temp);
        temp.val(shortcode).select();
        document.execCommand('copy');
        temp.remove();
        $(this).text('Copiado!');
        setTimeout(() => $(this).text('Copiar'), 2000);
    });

    // Switch Modo Simple / Avanzado
    $('input[name="vs_voting_mode"]').on('change', function() {
        var mode = $(this).val();
        $('.vs-mode-card').removeClass('active');
        $(this).closest('.vs-mode-card').addClass('active');
        
        if (mode === 'simple') {
            $('.vs-advanced-field').slideUp(200);
            $('.vs-simple-only-notice').slideDown(200);
        } else {
            $('.vs-advanced-field').slideDown(200);
            $('.vs-simple-only-notice').slideUp(200);
        }
    });
});