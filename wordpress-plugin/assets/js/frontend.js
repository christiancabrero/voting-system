jQuery(document).ready(function($) {
    $('.vs-container').each(function() {
        var $container = $(this);
        var remainingVotes = parseInt($container.data('limit')) || 0;
        var maxVotes = parseInt($container.data('max')) || 0;
        var messageId = $container.data('message-id');
        var $message = $('#' + messageId);
        var refreshAfterVote = vs_ajax.refresh || true;

        // ============================================
        // HIDRATACIÓN DINÁMICA (COMPATIBILIDAD CON CACHÉ)
        // ============================================
        // Si la página se sirvió desde una caché HTML estática (WP Rocket, WP Super Cache, LiteSpeed, etc.),
        // esta llamada AJAX asíncrona actualiza en tiempo real el nonce fresco y el estado real del votante.
        $.post(vs_ajax.ajax_url, {
            action: 'vs_get_voter_state'
        }).done(function(res) {
            if (res && res.success && res.data) {
                var d = res.data;

                // 1. Renovar el Nonce dinámicamente para que nunca expire en páginas cacheadas
                if (d.fresh_nonce) {
                    vs_ajax.nonce = d.fresh_nonce;
                }

                // 2. Comprobar si las votaciones están cerradas
                var isOpen = (d.is_open === true || d.is_open === 1 || d.is_open === '1');
                if (!isOpen) {
                    if (d.closed_show_participants) {
                        if ($container.find('.vs-closed-banner').length === 0) {
                            $container.prepend('<div class="vs-closed vs-closed-banner" style="margin-bottom: 20px;"><strong>' + (d.closed_status_message || 'Votaciones cerradas') + '</strong></div>');
                        }
                        $container.find('.vs-vote-btn')
                            .prop('disabled', true)
                            .addClass('vs-btn-closed')
                            .text(d.closed_btn_text || 'Votaciones cerradas');
                        $container.find('.vs-remaining-count').html('<span style="color: #6c757d;">' + (d.closed_btn_text || 'Votaciones cerradas') + '</span>');
                    } else {
                        $container.html('<div class="vs-closed">' + (d.closed_status_message || 'Las votaciones están cerradas') + '</div>');
                    }
                    return;
                }

                // 3. Actualizar contadores y mensajes dinámicos
                remainingVotes = parseInt(d.remaining_votes) || 0;
                maxVotes = parseInt(d.effective_limit) || 1;
                $container.data('limit', remainingVotes);
                $container.data('max', maxVotes);

                if (d.header_message && d.remaining_message) {
                    $container.find('.vs-remaining').html(d.header_message + d.remaining_message);
                }

                // 4. Marcar opciones ya votadas por este dispositivo/usuario
                if (d.voted_option_ids && d.voted_option_ids.length > 0) {
                    d.voted_option_ids.forEach(function(optId) {
                        var $item = $container.find('.vs-item[data-id="' + optId + '"]');
                        var $btn = $item.find('.vs-vote-btn');
                        if (!d.allow_multiple) {
                            $btn.text(d.voted_btn_text || '✓ Votado').addClass('vs-btn-voted').prop('disabled', true);
                            if ($item.find('.vs-voted-badge').length === 0) {
                                $item.append('<span class="vs-voted-badge">' + (d.vote_badge_text || '1 voto') + '</span>');
                            }
                        } else {
                            var count = d.votes_by_option && d.votes_by_option[optId] ? d.votes_by_option[optId] : 1;
                            var $badge = $item.find('.vs-voted-badge');
                            if ($badge.length === 0) {
                                $item.append('<span class="vs-voted-badge">' + count + ' voto(s)</span>');
                            } else {
                                $badge.text(count + ' voto(s)');
                            }
                        }
                    });
                }

                // 5. Si ha agotado todos los votos permitidos
                if (remainingVotes <= 0) {
                    $container.find('.vs-vote-btn').prop('disabled', true);
                    if ($container.find('.vs-user-exhausted-banner').length === 0 && d.already_voted_message) {
                        $container.find('.vs-header').prepend('<div class="vs-closed vs-user-exhausted-banner" style="margin-bottom: 15px;">' + d.already_voted_message + '</div>');
                    }
                }
            }
        });

        // ============================================
        // EVENTO CLICK DE VOTAR
        // ============================================
        $container.find('.vs-vote-btn').on('click', function() {
            var $btn = $(this);
            var optionId = $btn.data('id');

            if ($btn.prop('disabled')) {
                return;
            }

            var sendingText = (vs_ajax.i18n && vs_ajax.i18n.sending_vote) || 'Sending vote...';
            var successDefault = (vs_ajax.i18n && vs_ajax.i18n.vote_success) || 'Vote registered successfully!';
            var votedBtnText = (vs_ajax.i18n && vs_ajax.i18n.voted) || '✓ Voted';

            $btn.prop('disabled', true);
            $message.removeClass('success error').text(sendingText);

            $.post(vs_ajax.ajax_url, {
                action: 'vs_vote',
                option_id: optionId,
                nonce: vs_ajax.nonce
            })
            .done(function(response) {
                if (response.success) {
                    var message = response.data.message || response.data || successDefault;
                    if (typeof message === 'object') {
                        message = message.message || successDefault;
                    }
                    $message.addClass('success').text(message);
                    
                    $btn.text(votedBtnText).addClass('vs-btn-voted');
                    $btn.prop('disabled', true);
                    
                    remainingVotes--;
                    var votedCount = maxVotes - remainingVotes;
                    
                    $container.find('.vs-remaining strong').each(function(index) {
                        if (index === 0) {
                            $(this).text(votedCount);
                        } else if (index === 1) {
                            $(this).text(maxVotes);
                        }
                    });
                    
                    if (remainingVotes > 0) {
                        $container.find('.vs-remaining-count').html('Te quedan <strong>' + remainingVotes + '</strong> votos');
                    } else {
                        $container.find('.vs-remaining-count').html('Has agotado todos tus votos.').css('color', '#dc3545');
                        $container.find('.vs-vote-btn').prop('disabled', true);
                    }
                    
                    // Actualizar badges
                    var $badge = $btn.closest('.vs-item').find('.vs-voted-badge');
                    if ($badge.length > 0) {
                        var currentText = $badge.text().trim();
                        var currentCount = 0;
                        if (currentText.indexOf('✓') !== -1 || currentText.indexOf('voto') !== -1) {
                            var parts = currentText.split(' ');
                            if (parts.length > 0 && !isNaN(parseInt(parts[0]))) {
                                currentCount = parseInt(parts[0]) || 0;
                            } else {
                                currentCount = 1;
                            }
                        }
                        $badge.text((currentCount + 1) + ' voto(s)');
                    } else {
                        $btn.closest('.vs-item').append('<span class="vs-voted-badge">1 voto</span>');
                    }
                    
                    // Si hay nuevo token, actualizar la URL sin recargar
                    if (response.data.new_token && response.data.token_param) {
                        var url = new URL(window.location.href);
                        url.searchParams.set(response.data.token_param, response.data.new_token);
                        window.history.replaceState({}, '', url);
                    }
                    
                    var shouldRefresh = response.data.refresh !== undefined ? response.data.refresh : refreshAfterVote;
                    if (shouldRefresh) {
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                } else {
                    var errorMessage = response.data || 'Error al procesar el voto';
                    if (typeof errorMessage === 'object') {
                        errorMessage = errorMessage.message || 'Error al procesar el voto';
                    }
                    $message.addClass('error').text(errorMessage);
                    $btn.prop('disabled', false);
                }
            })
            .fail(function() {
                $message.addClass('error').text('Error de conexión. Intenta de nuevo.');
                $btn.prop('disabled', false);
            });
        });
    });
});
