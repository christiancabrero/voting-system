/**
 * Reactive Voting System Client - Pure PHP Edition
 */

(function() {
    'use strict';

    let state = {
        csrfToken: '',
        isOpen: true,
        votingMode: 'simple',
        closedShowParticipants: true,
        remainingVotes: 1,
        effectiveLimit: 1,
        votedCount: 0,
        votedOptionIds: [],
        i18n: {}
    };

    function showToast(message, type = 'info') {
        let toast = document.getElementById('vs-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'vs-toast';
            toast.className = 'vs-toast';
            document.body.appendChild(toast);
        }
        toast.className = 'vs-toast ' + type;
        toast.textContent = message;
        toast.style.display = 'block';
        toast.style.opacity = '1';

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 3500);
    }

    async function hydrateState() {
        try {
            const res = await fetch('api/state.php');
            if (!res.ok) return;
            const data = await res.json();
            if (!data.success) return;

            const d = data.data;
            state.csrfToken = d.csrf_token || state.csrfToken;
            state.isOpen = (d.is_open === true || d.is_open === 1 || d.is_open === '1');
            state.votingMode = d.voting_mode || 'simple';
            state.remainingVotes = parseInt(d.remaining_votes, 10);
            state.effectiveLimit = parseInt(d.effective_limit, 10);
            state.votedCount = parseInt(d.voted_count, 10);
            state.votedOptionIds = Array.isArray(d.voted_option_ids) ? d.voted_option_ids.map(Number) : [];
            state.i18n = d.i18n || {};

            applyStateToDOM();
        } catch (e) {
            console.error('Failed to hydrate voter state:', e);
        }
    }

    function applyStateToDOM() {
        const i18n = state.i18n;

        // 1. Check if voting is closed
        if (!state.isOpen) {
            const banner = document.getElementById('vs-status-banner');
            if (banner) {
                banner.className = 'vs-banner vs-banner-closed';
                banner.textContent = i18n.closed_notice || 'Voting is currently closed. You can view the participants below.';
                banner.style.display = 'block';
            }

            document.querySelectorAll('.vs-btn-vote').forEach(btn => {
                btn.disabled = true;
                btn.classList.add('vs-btn-closed');
                btn.textContent = i18n.closed_btn || 'Voting closed';
            });

            const remainingBox = document.getElementById('vs-remaining-box');
            if (remainingBox) {
                remainingBox.innerHTML = '<span class="vs-remaining-count" style="color: #6b7280;">' + (i18n.voting_closed || 'Voting is closed') + '</span>';
            }
            return;
        }

        // 2. Update remaining votes box
        const remainingBox = document.getElementById('vs-remaining-box');
        if (remainingBox) {
            if (state.remainingVotes > 0) {
                remainingBox.innerHTML = `
                    <p>${(i18n.votes_available || 'You have cast %1$s of %2$s available votes').replace('%1$s', '<strong>' + state.votedCount + '</strong>').replace('%2$s', '<strong>' + state.effectiveLimit + '</strong>')}</p>
                    <p class="vs-remaining-count">${(i18n.votes_remaining || '%s votes remaining').replace('%s', state.remainingVotes)}</p>
                `;
            } else {
                remainingBox.innerHTML = `
                    <p>${(i18n.votes_available || 'You have cast %1$s of %2$s available votes').replace('%1$s', '<strong>' + state.votedCount + '</strong>').replace('%2$s', '<strong>' + state.effectiveLimit + '</strong>')}</p>
                    <p class="vs-remaining-count" style="color: #dc3545;">${i18n.votes_exhausted || 'You have used all your available votes.'}</p>
                `;
            }
        }

        // 3. Mark voted contestants
        if (state.votedOptionIds && state.votedOptionIds.length > 0) {
            state.votedOptionIds.forEach(id => {
                const card = document.querySelector(`.vs-card[data-id="${id}"]`);
                if (card) {
                    const btn = card.querySelector('.vs-btn-vote');
                    if (btn) {
                        btn.textContent = i18n.voted_btn || '✓ Voted';
                        btn.classList.add('vs-btn-voted');
                        btn.disabled = true;
                    }
                    if (!card.querySelector('.vs-badge-voted')) {
                        const badge = document.createElement('span');
                        badge.className = 'vs-badge-voted';
                        badge.textContent = i18n.voted_btn || '✓ Voted';
                        card.appendChild(badge);
                    }
                }
            });
        }

        // 4. If all votes are exhausted, disable all other buttons and show alert
        if (state.remainingVotes <= 0) {
            document.querySelectorAll('.vs-btn-vote').forEach(btn => {
                btn.disabled = true;
            });

            const banner = document.getElementById('vs-status-banner');
            if (banner) {
                banner.className = 'vs-banner vs-banner-exhausted';
                banner.textContent = i18n.already_voted_alert || 'You have already voted. Results will be available soon.';
                banner.style.display = 'block';
            }
        }
    }

    async function handleVoteClick(e) {
        const btn = e.target.closest('.vs-btn-vote');
        if (!btn || btn.disabled) return;

        const optionId = parseInt(btn.dataset.id, 10);
        if (!optionId) return;

        if (!state.csrfToken) {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta && meta.content) {
                state.csrfToken = meta.content;
            } else {
                await hydrateState();
            }
        }

        btn.disabled = true;
        const originalText = btn.textContent;
        btn.textContent = state.i18n.sending_vote || 'Submitting...';

        try {
            const res = await fetch('api/vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': state.csrfToken
                },
                body: JSON.stringify({
                    option_id: optionId,
                    csrf_token: state.csrfToken
                })
            });

            const result = await res.json();

            if (result.success) {
                showToast(result.message || (state.i18n.vote_success || 'Vote recorded!'), 'success');
                btn.textContent = state.i18n.voted_btn || '✓ Voted';
                btn.classList.add('vs-btn-voted');

                // Update state
                state.votedCount = result.voted_count;
                state.remainingVotes = result.remaining_votes;
                if (!state.votedOptionIds.includes(optionId)) {
                    state.votedOptionIds.push(optionId);
                }

                applyStateToDOM();
            } else {
                showToast(result.error || (state.i18n.vote_rejected_duplicate || 'Could not register vote.'), 'error');
                btn.disabled = false;
                btn.textContent = originalText;
                // Re-hydrate to ensure UI matches reality
                hydrateState();
            }
        } catch (err) {
            console.error('Vote submission error:', err);
            showToast('Network error submitting vote. Please try again.', 'error');
            btn.disabled = false;
            btn.textContent = originalText;
        }
    }

    function init() {
        hydrateState();

        document.addEventListener('click', (e) => {
            if (e.target.matches('.vs-btn-vote') || e.target.closest('.vs-btn-vote')) {
                handleVoteClick(e);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
