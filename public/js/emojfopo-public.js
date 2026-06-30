/**
 * All of the JavaScript for your public-facing functionality should be
 * included in this file.
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/public/js
 * @since      1.0.0
 */
(function($) {
    'use strict';

    // Evaluate these lazily (at click time), not once at load — the localized
    // config object can be defined after this script first runs, which would
    // otherwise permanently capture EFFECTS_ON as false.
    function effectsOn() {
        return !!(window.emojfopo_reactions && parseInt(emojfopo_reactions.effects, 10) === 1);
    }
    function reducedMotion() {
        return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
    }
    function milestones() {
        return (window.emojfopo_reactions && emojfopo_reactions.milestones) || [10, 25, 50, 100, 250, 500, 1000];
    }

    /**
     * Handle emoji reactions
     */
    $(document).ready(function() {
        // Handle reaction button click
        $(document).on('click', '.emojfopo-reaction-button', function() {
            const button = $(this);
            const container = button.closest('.emojfopo-container');
            const postId = container.data('post-id');
            const reaction = button.data('reaction');

            // Don't do anything if already processing
            if (container.hasClass('processing')) {
                return;
            }

            // Add processing class to prevent multiple clicks
            container.addClass('processing');

            // Add animation class
            button.addClass('reacting');

            // Instant, optimistic delight — fire before the network round-trip
            if (effectsOn() && !reducedMotion()) {
                burstEmoji(button);
                floatEmoji(button);
            }
            haptic(15);

            // Send AJAX request
            $.ajax({
                url: emojfopo_reactions.ajax_url,
                type: 'POST',
                data: {
                    action: 'emojfopo_reaction',
                    post_id: postId,
                    reaction: reaction,
                    nonce: emojfopo_reactions.nonce
                },
                success: function(response) {
                    if (response.success) {
                        updateReactionCounts(container, response.data.counts, response.data.user_reaction);

                        // Show success message
                        showMessage(container, emojfopo_reactions.reaction_added, 'success');

                        // Celebrate milestones
                        if (effectsOn()) {
                            checkMilestone(container, response.data.counts);
                        }
                    } else {
                        // Show error message
                        showMessage(container, response.data.message || emojfopo_reactions.error, 'error');
                    }
                },
                error: function() {
                    // Show error message
                    showMessage(container, emojfopo_reactions.error, 'error');
                },
                complete: function() {
                    // Remove processing class
                    container.removeClass('processing');

                    // Remove animation class after a delay
                    setTimeout(function() {
                        button.removeClass('reacting');
                    }, 300);
                }
            });
        });

        // Handle share clicks
        $(document).on('click', '.emojfopo-share-btn', function(e) {
            e.preventDefault();
            const btn = $(this);
            const container = btn.closest('.emojfopo-container');
            const network = btn.data('network');
            const url = container.data('url') || window.location.href;
            const title = container.data('title') || document.title;
            shareTo(network, url, title, btn);
        });

        /**
         * Update reaction counts and active state
         */
        function updateReactionCounts(container, counts, userReaction) {
            // Update counts for all reactions
            container.find('.emojfopo-reaction-button').each(function() {
                const button = $(this);
                const reaction = button.data('reaction');
                const countEl = button.find('.count');
                const newCount = counts[reaction] || 0;
                const oldCount = parseInt(countEl.text(), 10) || 0;

                // Update count
                countEl.text(newCount);

                // Pop the count when it grows
                if (effectsOn() && newCount > oldCount) {
                    countEl.removeClass('emojfopo-count-pop');
                    // Force reflow so the animation can re-trigger
                    void countEl[0].offsetWidth;
                    countEl.addClass('emojfopo-count-pop');
                }

                // Update active state
                if (reaction === userReaction) {
                    button.addClass('active');
                } else {
                    button.removeClass('active');
                }
            });

            // Update user reaction message
            const messageEl = container.find('.emojfopo-message');
            if (messageEl.length === 0) {
                // Create message element if it doesn't exist
                const message = $('<div class="emojfopo-message"></div>');
                container.append(message);
            }

            // Get emoji for user reaction
            let emojiHtml = '';
            if (userReaction) {
                const emojiEl = container.find('.emojfopo-reaction-button[data-reaction="' + userReaction + '"] .emoji');
                if (emojiEl.length > 0) {
                    emojiHtml = emojiEl.html();
                }
            }

            // Update message
            if (userReaction) {
                container.find('.emojfopo-message').html(emojfopo_reactions.you_reacted_with + ' ' + emojiHtml);
            } else {
                container.find('.emojfopo-message').html('');
            }

            // If there are multiple containers for the same post (e.g., floating and inline),
            // update them all
            const postId = container.data('post-id');
            $('.emojfopo-container[data-post-id="' + postId + '"]').not(container).each(function() {
                updateReactionCounts($(this), counts, userReaction);
            });
        }

        /**
         * Show message
         */
        function showMessage(container, message, type) {
            // Create message element if it doesn't exist
            let messageEl = container.find('.emojfopo-message');
            if (messageEl.length === 0) {
                messageEl = $('<div class="emojfopo-message"></div>');
                container.append(messageEl);
            }

            // Add class based on message type
            messageEl.removeClass('success error').addClass(type);

            // Set message text
            messageEl.text(message);

            // Hide message after a delay
            setTimeout(function() {
                messageEl.fadeOut(function() {
                    // If user has reacted, show the permanent message
                    const userReaction = container.find('.emojfopo-reaction-button.active').data('reaction');
                    if (userReaction) {
                        const emojiEl = container.find('.emojfopo-reaction-button[data-reaction="' + userReaction + '"] .emoji');
                        if (emojiEl.length > 0) {
                            const emojiHtml = emojiEl.html();
                            messageEl.html(emojfopo_reactions.you_reacted_with + ' ' + emojiHtml);
                            messageEl.removeClass('success error');
                            messageEl.fadeIn();
                        }
                    }
                });
            }, 3000);
        }

        /**
         * Load reaction counts on page load
         */
        function loadReactionCounts() {
            $('.emojfopo-container').each(function() {
                const container = $(this);
                const postId = container.data('post-id');

                if (!postId) {
                    return;
                }

                $.ajax({
                    url: emojfopo_reactions.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_emojfopo_reactions',
                        post_id: postId,
                        nonce: emojfopo_reactions.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            updateReactionCounts(container, response.data.counts, response.data.user_reaction);
                        }
                    }
                });
            });
        }

        // Load reaction counts on page load
        loadReactionCounts();
    });

    /* ------------------------------------------------------------------ *
     * Engagement effects
     * ------------------------------------------------------------------ */

    /**
     * Light haptic feedback on supported (mostly mobile) devices.
     */
    function haptic(ms) {
        if (effectsOn() && navigator.vibrate) {
            try { navigator.vibrate(ms); } catch (e) {}
        }
    }

    /**
     * Read the emoji character from a reaction button.
     *
     * WordPress's wp-emoji script can replace the emoji text node with an
     * <img> (Twemoji), in which case .text() is empty — fall back to the
     * image's alt attribute so effects still work.
     */
    function getButtonEmoji(button) {
        const span = button.find('.emoji').first();
        const text = span.text().trim();
        if (text) { return text; }
        let img = span.find('img');
        if (!img.length) { img = button.find('img'); }
        return img.attr('alt') || '🎉';
    }

    /**
     * Burst a handful of mini-emojis outward from the clicked button.
     */
    function burstEmoji(button) {
        const emoji = getButtonEmoji(button);
        if (!emoji) { return; }

        const rect = button[0].getBoundingClientRect();
        const originX = rect.left + rect.width / 2;
        const originY = rect.top + rect.height / 2;
        const layer = getLayer();
        const particles = 7;

        for (let i = 0; i < particles; i++) {
            const p = document.createElement('span');
            p.className = 'emojfopo-particle';
            p.textContent = emoji;

            const angle = (Math.PI * 2 * i) / particles + (Math.random() - 0.5);
            const distance = 40 + Math.random() * 50;
            const dx = Math.cos(angle) * distance;
            const dy = Math.sin(angle) * distance - 20;

            p.style.left = originX + 'px';
            p.style.top = originY + 'px';
            p.style.setProperty('--dx', dx + 'px');
            p.style.setProperty('--dy', dy + 'px');
            p.style.fontSize = (16 + Math.random() * 12) + 'px';

            layer.appendChild(p);
            window.setTimeout((function(node) {
                return function() { node.remove(); };
            })(p), 900);
        }
    }

    /**
     * Float a single emoji upward (Facebook-Live style) from the button.
     */
    function floatEmoji(button) {
        const emoji = getButtonEmoji(button);
        if (!emoji) { return; }

        const rect = button[0].getBoundingClientRect();
        const layer = getLayer();
        const f = document.createElement('span');
        f.className = 'emojfopo-float';
        f.textContent = emoji;
        f.style.left = (rect.left + rect.width / 2) + 'px';
        f.style.top = rect.top + 'px';
        f.style.setProperty('--drift', ((Math.random() - 0.5) * 60) + 'px');
        layer.appendChild(f);
        window.setTimeout(function() { f.remove(); }, 1600);
    }

    /**
     * Fire confetti + a toast when a reaction crosses a milestone count.
     */
    function checkMilestone(container, counts) {
        let total = 0;
        for (const k in counts) {
            if (Object.prototype.hasOwnProperty.call(counts, k)) {
                total += parseInt(counts[k], 10) || 0;
            }
        }

        const prev = parseInt(container.attr('data-total'), 10) || 0;
        container.attr('data-total', total);

        const ms = milestones();
        for (let i = 0; i < ms.length; i++) {
            const m = ms[i];
            if (prev < m && total >= m) {
                if (!reducedMotion()) { confetti(); }
                toast(m + ' ' + (emojfopo_reactions.milestone_text || ''));
                haptic([30, 40, 30]);
                break;
            }
        }
    }

    /**
     * Lightweight confetti burst — no external library.
     */
    function confetti() {
        const layer = getLayer();
        const colors = ['#ff595e', '#ffca3a', '#8ac926', '#1982c4', '#6a4c93', '#ff70a6'];
        const count = 60;

        for (let i = 0; i < count; i++) {
            const c = document.createElement('span');
            c.className = 'emojfopo-confetti';
            c.style.left = (Math.random() * 100) + 'vw';
            c.style.background = colors[i % colors.length];
            c.style.setProperty('--fall', (1.6 + Math.random() * 1.4) + 's');
            c.style.setProperty('--delay', (Math.random() * 0.4) + 's');
            c.style.setProperty('--spin', (Math.random() * 720 - 360) + 'deg');
            layer.appendChild(c);
            window.setTimeout((function(node) {
                return function() { node.remove(); };
            })(c), 3500);
        }
    }

    /**
     * Transient toast notification.
     */
    function toast(text) {
        const layer = getLayer();
        const t = document.createElement('div');
        t.className = 'emojfopo-toast';
        t.textContent = text;
        layer.appendChild(t);
        // Trigger transition
        window.requestAnimationFrame(function() { t.classList.add('show'); });
        window.setTimeout(function() {
            t.classList.remove('show');
            window.setTimeout(function() { t.remove(); }, 400);
        }, 2600);
    }

    /**
     * Share to a network, or use the native share sheet / copy fallback.
     */
    function shareTo(network, url, title, btn) {
        const eu = encodeURIComponent(url);
        const et = encodeURIComponent(title);
        let target = '';

        switch (network) {
            case 'x':
                target = 'https://twitter.com/intent/tweet?text=' + et + '&url=' + eu;
                break;
            case 'facebook':
                target = 'https://www.facebook.com/sharer/sharer.php?u=' + eu;
                break;
            case 'whatsapp':
                target = 'https://api.whatsapp.com/send?text=' + et + '%20' + eu;
                break;
            case 'copy':
                copyLink(url, btn);
                return;
        }

        // Prefer the native share sheet on mobile where available
        if (navigator.share && /Mobi|Android/i.test(navigator.userAgent)) {
            navigator.share({ title: title, url: url }).catch(function() {
                openPopup(target);
            });
            return;
        }
        openPopup(target);
    }

    function copyLink(url, btn) {
        const done = function() {
            if (btn) {
                const original = btn.html();
                btn.addClass('copied').html('<span class="emojfopo-check">✓</span>');
                toast(emojfopo_reactions.copied_text || 'Link copied!');
                window.setTimeout(function() {
                    btn.removeClass('copied').html(original);
                }, 1500);
            }
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(done).catch(function() { legacyCopy(url, done); });
        } else {
            legacyCopy(url, done);
        }
    }

    function legacyCopy(url, done) {
        const ta = document.createElement('textarea');
        ta.value = url;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); done(); } catch (e) {}
        ta.remove();
    }

    function openPopup(target) {
        window.open(target, '_blank', 'noopener,noreferrer,width=600,height=520');
    }

    /**
     * Shared fixed-position overlay layer for all transient effects.
     */
    function getLayer() {
        let layer = document.getElementById('emojfopo-fx-layer');
        if (!layer) {
            layer = document.createElement('div');
            layer.id = 'emojfopo-fx-layer';
            document.body.appendChild(layer);
        }
        return layer;
    }

})(jQuery);
