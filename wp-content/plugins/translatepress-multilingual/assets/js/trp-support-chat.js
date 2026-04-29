/**
 * Support Chat Widget
 *
 * @package TranslatePress
 * @since 3.0.9
 */

(function() {
    'use strict';

    // Wait for DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        var widget = document.getElementById('trp-support-chat-widget');
        if (!widget || typeof trpSupportChat === 'undefined') {
            return;
        }

        var config = trpSupportChat;
        var strings = config.strings;

        // Elements
        var toggle = widget.querySelector('.trp-support-chat__toggle');
        var chatWindow = widget.querySelector('.trp-support-chat__window');
        var closeBtn = widget.querySelector('.trp-support-chat__close');
        var postsContainer = widget.querySelector('.trp-support-chat__posts');
        var loadingContainer = widget.querySelector('.trp-support-chat__loading');
        var sectionLabel = widget.querySelector('.trp-support-chat__section-label');
        var badge = widget.querySelector('.trp-support-chat__badge');

        // Set text content from localized strings
        widget.querySelector('.trp-support-chat__title').textContent = strings.title;
        widget.querySelector('.trp-support-chat__subtitle').textContent = strings.subtitle;
        widget.querySelector('.trp-support-chat__loading span').textContent = strings.loading;
        widget.querySelector('.trp-support-chat__encourage-title').textContent = strings.encourageTitle;
        widget.querySelector('.trp-support-chat__encourage-text').textContent = strings.encourageText;
        widget.querySelector('.trp-support-chat__tip-title').textContent = strings.tipTitle;
        widget.querySelector('.trp-support-chat__tip-text').textContent = strings.tipText;
        widget.querySelector('.trp-support-chat__btn--primary span').textContent = strings.askQuestion;
        widget.querySelector('.trp-support-chat__btn--secondary span').textContent = strings.viewAll;

        // Set section label
        if (sectionLabel) {
            sectionLabel.textContent = strings.subtitle;
        }

        // State
        var isOpen = false;
        var postsLoaded = false;

        // Show widget
        widget.style.display = 'block';

        // Initialize badge based on server-computed new count
        var newCount = parseInt(config.newCount, 10) || 0;
        updateBadge(newCount);

        // Add attention animation after a delay only if there are new posts
        if (newCount > 0) {
            setTimeout(function() {
                toggle.classList.add('trp-support-chat__toggle--attention');
            }, 2000);
        }

        // Toggle chat window
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            isOpen = !isOpen;
            widget.classList.toggle('trp-support-chat--open', isOpen);
            toggle.classList.remove('trp-support-chat__toggle--attention');

            if (isOpen) {
                // Mark posts as read and hide badge
                markPostsAsRead();
                updateBadge(0);

                if (!postsLoaded) {
                    loadPosts();
                }
            }
        });

        // Close button
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            isOpen = false;
            widget.classList.remove('trp-support-chat--open');
        });

        // Close chat when clicking outside
        document.addEventListener('click', function(e) {
            if (isOpen && !widget.contains(e.target)) {
                isOpen = false;
                widget.classList.remove('trp-support-chat--open');
            }
        });

        // Prevent closing when clicking inside chat window
        chatWindow.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isOpen) {
                isOpen = false;
                widget.classList.remove('trp-support-chat--open');
                toggle.focus();
            }
        });

        function updateBadge(count) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = '';
                badge.classList.add('trp-support-chat__badge--pulse');
            } else {
                badge.textContent = '';
                badge.style.display = 'none';
                badge.classList.remove('trp-support-chat__badge--pulse');
            }
        }

        function markPostsAsRead() {
            var formData = new FormData();
            formData.append('action', 'trp_mark_forum_posts_read');
            formData.append('nonce', config.nonce);

            fetch(config.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            });
        }

        function loadPosts() {
            var formData = new FormData();
            formData.append('action', 'trp_get_forum_posts');
            formData.append('nonce', config.nonce);

            fetch(config.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success && data.data.posts) {
                    renderPosts(data.data.posts);
                    postsLoaded = true;
                } else {
                    showError(data.data ? data.data.message : strings.error);
                }
            })
            .catch(function(error) {
                console.error('Support Chat Error:', error);
                showError(strings.error);
            });
        }

        function renderPosts(posts) {
            if (!posts.length) {
                showError(strings.error);
                return;
            }

            var html = '';
            posts.forEach(function(post) {
                html += '<a href="' + escapeHtml(post.link) + '" target="_blank" rel="noopener" class="trp-support-chat__post">';
                html += '<h5 class="trp-support-chat__post-title">' + escapeHtml(post.title) + '</h5>';
                html += '<div class="trp-support-chat__post-meta">';
                if (post.author) {
                    html += '<span class="trp-support-chat__post-author">' + strings.postedBy + ' ' + escapeHtml(post.author) + '</span>';
                }
                if (post.date) {
                    html += '<span class="trp-support-chat__post-date">' + escapeHtml(post.date) + '</span>';
                }
                html += '</div>';
                html += '</a>';
            });

            postsContainer.innerHTML = html;
            postsContainer.classList.add('trp-support-chat__posts--loaded');
            loadingContainer.classList.add('trp-support-chat__loading--hidden');
        }

        function showError(message) {
            loadingContainer.innerHTML = '<div class="trp-support-chat__error">' +
                '<div class="trp-support-chat__error-icon">!</div>' +
                '<p>' + escapeHtml(message) + '</p>' +
                '</div>';
        }

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }
})();
