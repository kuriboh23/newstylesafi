/* ==================================================================
   Newstylesafi — app.js
   Vanilla JS only. Loaded with `defer`, no inline scripts (strict CSP).
   ================================================================== */
(function () {
    'use strict';

    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------------------------------------------------------------
       1. Left drawer
       --------------------------------------------------------------- */
    var drawer = document.getElementById('drawer');
    var overlay = document.getElementById('drawerOverlay');
    var toggle = document.getElementById('drawerToggle');
    var closeBtn = document.getElementById('drawerClose');

    if (drawer && overlay && toggle) {
        var isOpen = false;
        var lastFocused = null;
        var overlayTimer = null;

        var focusableSelector = [
            'a[href]',
            'button:not([disabled])',
            'input:not([disabled])',
            'select:not([disabled])',
            'textarea:not([disabled])',
            '[tabindex]:not([tabindex="-1"])'
        ].join(',');

        function getFocusable() {
            return Array.prototype.filter.call(
                drawer.querySelectorAll(focusableSelector),
                function (el) {
                    return el.offsetParent !== null || el === drawer;
                }
            );
        }

        function openDrawer() {
            if (isOpen) {
                return;
            }

            isOpen = true;
            lastFocused = document.activeElement;

            if (overlayTimer) {
                window.clearTimeout(overlayTimer);
                overlayTimer = null;
            }

            drawer.classList.add('is-open');
            drawer.setAttribute('aria-hidden', 'false');
            toggle.setAttribute('aria-expanded', 'true');
            document.body.classList.add('drawer-open');

            overlay.hidden = false;
            window.requestAnimationFrame(function () {
                overlay.classList.add('is-open');
            });

            // Focus once the drawer is visible (two frames also covers
            // browsers that apply the visibility change on the next paint).
            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    if (closeBtn) {
                        closeBtn.focus({ preventScroll: true });
                    }
                });
            });
        }

        function closeDrawer(restoreFocus) {
            if (!isOpen) {
                return;
            }

            isOpen = false;
            drawer.classList.remove('is-open');
            drawer.setAttribute('aria-hidden', 'true');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('drawer-open');
            overlay.classList.remove('is-open');

            overlayTimer = window.setTimeout(function () {
                if (!isOpen) {
                    overlay.hidden = true;
                }
            }, reducedMotion ? 0 : 260);

            if (restoreFocus !== false) {
                if (lastFocused && typeof lastFocused.focus === 'function') {
                    lastFocused.focus({ preventScroll: true });
                } else {
                    toggle.focus({ preventScroll: true });
                }
            }
        }

        toggle.addEventListener('click', openDrawer);

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                closeDrawer();
            });
        }

        overlay.addEventListener('click', function () {
            closeDrawer();
        });

        // Close after following an in-drawer link.
        drawer.addEventListener('click', function (event) {
            var link = event.target.closest ? event.target.closest('a[href]') : null;

            if (link) {
                closeDrawer(false);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (!isOpen) {
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closeDrawer();
                return;
            }

            if (event.key !== 'Tab') {
                return;
            }

            var focusable = getFocusable();

            if (focusable.length === 0) {
                event.preventDefault();
                drawer.focus();
                return;
            }

            var first = focusable[0];
            var last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });

        // Desktop: never leave the drawer stuck open after a resize.
        var desktopQuery = window.matchMedia('(min-width: 768px)');

        function handleDesktopChange(event) {
            if (event.matches) {
                closeDrawer(false);
            }
        }

        if (typeof desktopQuery.addEventListener === 'function') {
            desktopQuery.addEventListener('change', handleDesktopChange);
        } else if (typeof desktopQuery.addListener === 'function') {
            desktopQuery.addListener(handleDesktopChange);
        }
    }

    /* ---------------------------------------------------------------
       2. Hero slider (CSS scroll-snap + dots)
       --------------------------------------------------------------- */
    var track = document.getElementById('heroTrack');

    if (track) {
        var slides = Array.prototype.slice.call(track.children);
        var dots = Array.prototype.slice.call(document.querySelectorAll('.hero-dot'));
        var ticking = false;

        function setActive(index) {
            dots.forEach(function (dot, i) {
                var active = i === index;
                dot.classList.toggle('is-active', active);

                if (active) {
                    dot.setAttribute('aria-current', 'true');
                } else {
                    dot.removeAttribute('aria-current');
                }
            });
        }

        function activeIndex() {
            var center = track.scrollLeft + track.clientWidth / 2;
            var best = 0;
            var bestDistance = Infinity;

            slides.forEach(function (slide, i) {
                var slideCenter = (slide.offsetLeft - track.offsetLeft) + slide.clientWidth / 2;
                var distance = Math.abs(slideCenter - center);

                if (distance < bestDistance) {
                    bestDistance = distance;
                    best = i;
                }
            });

            return best;
        }

        track.addEventListener('scroll', function () {
            if (ticking) {
                return;
            }

            ticking = true;

            window.requestAnimationFrame(function () {
                setActive(activeIndex());
                ticking = false;
            });
        }, { passive: true });

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () {
                var slide = slides[i];

                if (!slide) {
                    return;
                }

                track.scrollTo({
                    left: slide.offsetLeft - track.offsetLeft,
                    behavior: reducedMotion ? 'auto' : 'smooth'
                });

                setActive(i);
            });
        });
    }

    /* ---------------------------------------------------------------
       3. Public helper: badge counters (used by cart/wishlist later)
       --------------------------------------------------------------- */
    function setBadge(name, count) {
        var value = Number(count) || 0;

        Array.prototype.forEach.call(
            document.querySelectorAll('[data-badge="' + name + '"]'),
            function (badge) {
                // The wishlist badge is a dot: keep it empty, only toggle it.
                if (!badge.classList.contains('badge--dot')) {
                    badge.textContent = String(value);
                }

                badge.hidden = value <= 0;
            }
        );
    }

    window.SM = window.SM || {};
    window.SM.setBadge = setBadge;
})();
/* ==================================================================
   Newstylesafi — storefront interactions
   Product cards, wishlist, gallery, options, cart, countdown.
   ================================================================== */
(function () {
    'use strict';

    function postJson(url, data) {
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');

        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': csrfMeta ? csrfMeta.getAttribute('content') : ''
            },
            credentials: 'same-origin',
            body: JSON.stringify(data)
        }).then(function (response) {
            return response.json().catch(function () {
                return {};
            });
        });
    }

    /* ---------- Wishlist toggle (cards + product page) ---------- */
    Array.prototype.forEach.call(document.querySelectorAll('[data-wish-slug]'), function (button) {
        button.addEventListener('click', function () {
            var slug = button.getAttribute('data-wish-slug');
            var url = button.getAttribute('data-wishlist-url');

            if (!slug || !url) {
                return;
            }

            button.disabled = true;

            postJson(url, { slug: slug }).then(function (data) {
                if (!data || data.ok !== true) {
                    return;
                }

                button.classList.toggle('is-active', !!data.active);
                button.setAttribute('aria-pressed', data.active ? 'true' : 'false');

                var label = button.querySelector('span');
                if (label) {
                    label.textContent = data.active ? 'Dans mes favoris' : 'Ajouter aux favoris';
                }

                button.setAttribute(
                    'aria-label',
                    data.active ? 'Retirer des favoris' : 'Ajouter aux favoris'
                );

                if (window.SM && window.SM.setBadge) {
                    window.SM.setBadge('wishlist', data.wishCount || 0);
                }
            }).finally(function () {
                button.disabled = false;
            });
        });
    });

    /* ---------- Product gallery thumbnails + lightbox ------------ */
    var mainImage = document.getElementById('galleryMainImage');
    var thumbs = document.querySelectorAll('.gallery-thumb');
    var galleryLightbox = document.getElementById('productLightbox');

    if (mainImage) {
        var gallerySources = [];
        var activeGalleryIndex = 0;
        var initialSource = mainImage.getAttribute('src');

        if (initialSource) {
            gallerySources.push(initialSource);
        }

        Array.prototype.forEach.call(thumbs, function (thumb) {
            var source = thumb.getAttribute('data-img');

            if (source && gallerySources.indexOf(source) === -1) {
                gallerySources.push(source);
            }
        });

        var showGalleryIndex = function (index) {
            if (!gallerySources.length) {
                return;
            }

            activeGalleryIndex = (index + gallerySources.length) % gallerySources.length;
            mainImage.src = gallerySources[activeGalleryIndex];

            Array.prototype.forEach.call(thumbs, function (item) {
                item.classList.toggle(
                    'is-active',
                    item.getAttribute('data-img') === gallerySources[activeGalleryIndex]
                );
            });
        };

        Array.prototype.forEach.call(thumbs, function (thumb) {
            thumb.addEventListener('click', function () {
                var source = thumb.getAttribute('data-img');
                var index = source ? gallerySources.indexOf(source) : -1;

                if (index !== -1) {
                    showGalleryIndex(index);
                }
            });
        });

        if (galleryLightbox) {
            var lightboxImage = galleryLightbox.querySelector('[data-lightbox-image]');
            var lightboxClose = galleryLightbox.querySelector('[data-lightbox-close]');
            var lightboxPrev = galleryLightbox.querySelector('[data-lightbox-prev]');
            var lightboxNext = galleryLightbox.querySelector('[data-lightbox-next]');
            var lightboxOpen = document.querySelector('[data-lightbox-open]');

            var renderLightbox = function () {
                if (lightboxImage) {
                    lightboxImage.src = gallerySources[activeGalleryIndex];
                    lightboxImage.alt = mainImage.alt;
                }

                if (lightboxPrev) {
                    lightboxPrev.hidden = gallerySources.length < 2;
                }

                if (lightboxNext) {
                    lightboxNext.hidden = gallerySources.length < 2;
                }
            };

            var closeLightbox = function () {
                galleryLightbox.hidden = true;
                document.body.classList.remove('lightbox-open');

                if (lightboxOpen) {
                    lightboxOpen.focus();
                }
            };

            var openLightbox = function () {
                renderLightbox();
                galleryLightbox.hidden = false;
                document.body.classList.add('lightbox-open');

                if (lightboxClose) {
                    lightboxClose.focus();
                }
            };

            var stepLightbox = function (direction) {
                showGalleryIndex(activeGalleryIndex + direction);
                renderLightbox();
            };

            if (lightboxOpen) {
                lightboxOpen.addEventListener('click', openLightbox);
            }

            if (lightboxClose) {
                lightboxClose.addEventListener('click', closeLightbox);
            }

            if (lightboxPrev) {
                lightboxPrev.addEventListener('click', function () {
                    stepLightbox(-1);
                });
            }

            if (lightboxNext) {
                lightboxNext.addEventListener('click', function () {
                    stepLightbox(1);
                });
            }

            galleryLightbox.addEventListener('click', function (event) {
                if (event.target === galleryLightbox) {
                    closeLightbox();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (galleryLightbox.hidden) {
                    return;
                }

                if (event.key === 'Escape') {
                    closeLightbox();
                } else if (event.key === 'ArrowLeft') {
                    stepLightbox(-1);
                } else if (event.key === 'ArrowRight') {
                    stepLightbox(1);
                }
            });
        }
    }

    /* ---------- Size / colour selection ------------------------- */
    function bindOptions(buttons, hiddenId, attribute) {
        Array.prototype.forEach.call(buttons, function (button) {
            button.addEventListener('click', function () {
                Array.prototype.forEach.call(buttons, function (item) {
                    item.setAttribute('aria-checked', item === button ? 'true' : 'false');
                });

                var hidden = document.getElementById(hiddenId);
                if (hidden) {
                    hidden.value = button.getAttribute(attribute) || '';
                }
            });
        });
    }

    bindOptions(document.querySelectorAll('.size-btn'), 'selectedSize', 'data-size');
    bindOptions(document.querySelectorAll('.color-btn'), 'selectedColor', 'data-color');

    /* ---------- Quantity steppers ------------------------------- */
    Array.prototype.forEach.call(document.querySelectorAll('.qty-selector'), function (selector) {
        var input = selector.querySelector('.qty-input');

        if (!input) {
            return;
        }

        var minus = selector.querySelector('.qty-btn--minus');
        var plus = selector.querySelector('.qty-btn--plus');

        function apply(delta) {
            var value = parseInt(input.value, 10) || 1;
            value = Math.min(99, Math.max(1, value + delta));
            input.value = String(value);

            if (minus) {
                minus.disabled = value <= 1;
            }
        }

        if (minus) {
            minus.addEventListener('click', function () {
                apply(-1);
            });
        }

        if (plus) {
            plus.addEventListener('click', function () {
                apply(1);
            });
        }

        input.addEventListener('change', function () {
            var value = parseInt(input.value, 10) || 1;
            input.value = String(Math.min(99, Math.max(1, value)));
        });
    });

    /* ---------- Cart actions ------------------------------------ */
    var cartPage = document.querySelector('.cart-page');

    if (cartPage && cartPage.getAttribute('data-cart-update')) {
        cartPage.addEventListener('click', function (event) {
            var trigger = event.target.closest ? event.target.closest('[data-action]') : null;

            if (!trigger || !cartPage.contains(trigger)) {
                return;
            }

            var action = trigger.getAttribute('data-action');
            var item = trigger.closest('.cart-item');
            var slug = item ? item.getAttribute('data-slug') : '';
            var request = null;

            if (action === 'update' && slug) {
                request = postJson(cartPage.getAttribute('data-cart-update'), {
                    slug: slug,
                    quantity: parseInt(trigger.getAttribute('data-qty'), 10) || 1
                });
            } else if (action === 'remove' && slug) {
                request = postJson(cartPage.getAttribute('data-cart-remove'), { slug: slug });
            } else if (action === 'empty') {
                request = postJson(cartPage.getAttribute('data-cart-empty'), {});
            }

            if (!request) {
                return;
            }

            trigger.disabled = true;

            request.then(function (data) {
                if (data && data.ok) {
                    window.location.reload();
                }
            }).finally(function () {
                trigger.disabled = false;
            });
        });
    }

    /* ---------- Flash sale countdown ---------------------------- */
    var timer = document.getElementById('countdownTimer');

    if (timer) {
        var end = Date.parse(String(timer.getAttribute('data-end') || '').replace(' ', 'T'));
        var daysEl = timer.querySelector('[data-count="days"]');
        var hoursEl = timer.querySelector('[data-count="hours"]');
        var minutesEl = timer.querySelector('[data-count="minutes"]');
        var secondsEl = timer.querySelector('[data-count="seconds"]');

        function pad(value) {
            return value < 10 ? '0' + value : String(value);
        }

        function tick() {
            var remaining = Math.max(0, Math.floor((end - Date.now()) / 1000));
            var days = Math.floor(remaining / 86400);
            var hours = Math.floor((remaining % 86400) / 3600);
            var minutes = Math.floor((remaining % 3600) / 60);
            var seconds = remaining % 60;

            if (daysEl) {
                daysEl.textContent = pad(days);
            }

            if (hoursEl) {
                hoursEl.textContent = pad(hours);
            }

            if (minutesEl) {
                minutesEl.textContent = pad(minutes);
            }

            if (secondsEl) {
                secondsEl.textContent = pad(seconds);
            }

            if (remaining > 0) {
                window.setTimeout(tick, 1000);
            }
        }

        if (!isNaN(end)) {
            tick();
        }
    }
})();
/* ==================================================================
   Newstylesafi — refresh interactions
   Add to cart without leaving the page, toast, catalogue sorting.
   ================================================================== */
(function () {
    'use strict';

    function postJson(url, data) {
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');

        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': csrfMeta ? csrfMeta.getAttribute('content') : ''
            },
            credentials: 'same-origin',
            body: JSON.stringify(data)
        }).then(function (response) {
            return response.json().catch(function () {
                return {};
            });
        });
    }

    var toast = document.getElementById('smToast');
    var toastTimer = null;

    function hideToast() {
        if (toast) {
            toast.hidden = true;
        }
    }

    function showToast(message) {
        if (!toast) {
            return;
        }

        var text = toast.querySelector('[data-toast-text]');

        if (text) {
            text.textContent = message;
        }

        toast.hidden = false;

        if (toastTimer) {
            window.clearTimeout(toastTimer);
        }

        toastTimer = window.setTimeout(hideToast, 6500);
    }

    if (toast) {
        var toastClose = toast.querySelector('[data-toast-close]');

        if (toastClose) {
            toastClose.addEventListener('click', hideToast);
        }
    }

    /* ---------- Add to cart (stay on the page) ------------------- */
    var cartForm = document.getElementById('cartForm');

    if (cartForm && window.fetch) {
        cartForm.addEventListener('submit', function (event) {
            event.preventDefault();

            var button = event.submitter || document.querySelector('.cart-actions .btn--add-cart');
            var payload = {};

            new FormData(cartForm).forEach(function (value, key) {
                payload[key] = value;
            });

            // "Commander par WhatsApp" asks for the delivery details first.
            if (event.submitter && event.submitter.name) {
                payload[event.submitter.name] = event.submitter.value;
            }

            var quantity = document.querySelector('.cart-actions .qty-input');

            if (quantity) {
                payload.quantity = quantity.value;
            }

            if (button) {
                button.disabled = true;
            }

            postJson(cartForm.action, payload).then(function (data) {
                if (!data || data.ok !== true) {
                    showToast((data && data.error) || 'Ajout impossible, réessayez.');
                    return;
                }

                if (payload.after === 'checkout') {
                    window.location.href = data.redirect
                        || cartForm.getAttribute('data-checkout-url')
                        || '/commande';
                    return;
                }

                if (window.SM && window.SM.setBadge) {
                    window.SM.setBadge('cart', data.cartCount || 0);
                }

                showToast(data.message || 'Article ajouté au panier');
            }).catch(function () {
                // No JS/AJAX available: fall back to a normal form post.
                cartForm.submit();
            }).finally(function () {
                if (button) {
                    button.disabled = false;
                }
            });
        });
    }

    /* ---------- Catalogue: sort on change ----------------------- */
    Array.prototype.forEach.call(document.querySelectorAll('[data-auto-submit]'), function (field) {
        field.addEventListener('change', function () {
            if (field.form) {
                field.form.submit();
            }
        });
    });
})();

/* ==================================================================
   Newstylesafi — search suggestions, history, admin helpers
   ================================================================== */
(function () {
    'use strict';

    /* ---------- Admin: category picker with icons ---------------- */
    var categoryPicker = document.querySelector('[data-category-picker]');
    var categoryNative = document.querySelector('[data-category-native]');

    if (categoryPicker && categoryNative) {
        var categoryToggle = categoryPicker.querySelector('[data-category-toggle]');
        var categoryMenu = categoryPicker.querySelector('[data-category-menu]');
        var categoryValue = categoryPicker.querySelector('[data-category-value]');
        var categoryIcon = categoryPicker.querySelector('[data-category-icon]');

        var closeCategoryPicker = function () {
            if (!categoryMenu || !categoryToggle) {
                return;
            }

            categoryMenu.hidden = true;
            categoryToggle.setAttribute('aria-expanded', 'false');
            categoryPicker.classList.remove('is-open');
        };

        if (categoryToggle && categoryMenu && categoryValue && categoryIcon) {
            categoryNative.hidden = true;
            categoryPicker.hidden = false;

            categoryToggle.addEventListener('click', function () {
                var willOpen = categoryMenu.hidden;

                categoryMenu.hidden = !willOpen;
                categoryToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                categoryPicker.classList.toggle('is-open', willOpen);

                if (willOpen) {
                    var selected = categoryMenu.querySelector('.category-option.is-selected');

                    if (selected) {
                        selected.scrollIntoView({ block: 'nearest' });
                    }
                }
            });

            Array.prototype.forEach.call(
                categoryMenu.querySelectorAll('[data-category-option]'),
                function (option) {
                    option.addEventListener('click', function () {
                        var value = option.getAttribute('data-value') || '0';
                        var label = option.getAttribute('data-label') || '';
                        var optionIcon = option.querySelector('[data-category-option-icon]');

                        categoryNative.value = value;
                        categoryValue.textContent = label;

                        if (optionIcon) {
                            categoryIcon.innerHTML = optionIcon.innerHTML;
                        }

                        Array.prototype.forEach.call(
                            categoryMenu.querySelectorAll('.category-option'),
                            function (item) {
                                var isSelected = item === option;
                                item.classList.toggle('is-selected', isSelected);
                                item.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                            }
                        );

                        categoryNative.dispatchEvent(new Event('change', { bubbles: true }));
                        closeCategoryPicker();
                        categoryToggle.focus();
                    });
                }
            );

            document.addEventListener('click', function (event) {
                if (!categoryPicker.contains(event.target)) {
                    closeCategoryPicker();
                }
            });

            categoryPicker.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeCategoryPicker();
                    categoryToggle.focus();
                }
            });
        }
    }

    /* ---------- Search page: live suggestions ------------------- */
    var form = document.getElementById('siteSearchForm');

    if (form) {
        var input = document.getElementById('siteSearchInput');
        var box = document.getElementById('searchSuggestions');
        var suggestUrl = form.getAttribute('data-suggest-url') || '';
        var timer = null;
        var controller = null;
        var lastQuery = '';

        function closeSuggestions() {
            if (!box) {
                return;
            }

            box.textContent = '';
            box.hidden = true;
            input.setAttribute('aria-expanded', 'false');
        }

        function renderSuggestions(items, suggestion) {
            if (!box) {
                return;
            }

            box.textContent = '';

            if (!items.length) {
                if (!suggestion) {
                    closeSuggestions();

                    return;
                }

                // Misspelled query: offer the closest real word instead.
                var hint = document.createElement('button');
                hint.type = 'button';
                hint.className = 'suggestion suggestion--hint';
                hint.setAttribute('role', 'option');

                var hintMain = document.createElement('span');
                hintMain.className = 'suggestion-main';

                var hintTitle = document.createElement('span');
                hintTitle.className = 'suggestion-title';
                hintTitle.textContent = 'Voulez-vous dire « ' + suggestion + ' » ?';

                hintMain.appendChild(hintTitle);
                hint.appendChild(hintMain);

                hint.addEventListener('click', function () {
                    if (input) {
                        input.value = suggestion;
                    }

                    form.submit();
                });

                box.appendChild(hint);
                box.hidden = false;
                input.setAttribute('aria-expanded', 'true');

                return;
            }

            items.forEach(function (item) {
                var link = document.createElement('a');
                link.className = 'suggestion';
                link.href = item.url;
                link.setAttribute('role', 'option');

                if (item.image) {
                    var img = document.createElement('img');
                    img.src = item.image;
                    img.alt = '';
                    img.width = 40;
                    img.height = 50;
                    img.loading = 'lazy';
                    link.appendChild(img);
                }

                var main = document.createElement('span');
                main.className = 'suggestion-main';

                var title = document.createElement('span');
                title.className = 'suggestion-title';
                title.textContent = item.title;
                main.appendChild(title);

                if (item.category) {
                    var category = document.createElement('span');
                    category.className = 'suggestion-cat';
                    category.textContent = item.category;
                    main.appendChild(category);
                }

                link.appendChild(main);

                var price = document.createElement('span');
                price.className = 'suggestion-price';
                price.textContent = item.price;
                link.appendChild(price);

                box.appendChild(link);
            });

            box.hidden = false;
            input.setAttribute('aria-expanded', 'true');
        }

        function loadSuggestions(query) {
            if (!suggestUrl || query.length < 2) {
                closeSuggestions();

                return;
            }

            if (controller) {
                controller.abort();
            }

            controller = typeof AbortController === 'function' ? new AbortController() : null;

            var options = {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            };

            if (controller) {
                options.signal = controller.signal;
            }

            // suggestUrl may already contain a query string ("?p=/recherche").
            var separator = suggestUrl.indexOf('?') === -1 ? '?' : '&';

            window.fetch(suggestUrl + separator + 'q=' + encodeURIComponent(query), options)
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (lastQuery !== query) {
                        return;
                    }

                    renderSuggestions((data && data.items) || [], data && data.suggestion);
                })
                .catch(function () {
                    // Aborted or offline: keep the last visible state.
                });
        }

        if (input) {
            input.addEventListener('input', function () {
                lastQuery = input.value.trim();

                if (timer) {
                    window.clearTimeout(timer);
                }

                timer = window.setTimeout(function () {
                    loadSuggestions(lastQuery);
                }, 180);
            });

            input.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeSuggestions();
                }
            });
        }

        document.addEventListener('click', function (event) {
            if (!box || box.hidden) {
                return;
            }

            if (!form.contains(event.target) && !box.contains(event.target)) {
                closeSuggestions();
            }
        });
    }

    /* ---------- Admin: colour rows (simple add/remove) ---------- */
    var colorEditor = document.querySelector('[data-color-editor]');

    if (colorEditor) {
        var colorRows = colorEditor.querySelector('[data-color-rows]');
        var colorValue = colorEditor.querySelector('[data-color-value]');
        var colorTemplate = colorEditor.querySelector('[data-color-template]');
        var colorAdd = colorEditor.querySelector('[data-color-add]');

        var syncColors = function () {
            if (!colorRows || !colorValue) {
                return;
            }

            var colors = [];

            Array.prototype.forEach.call(colorRows.querySelectorAll('.color-row'), function (row) {
                var name = row.querySelector('[data-color-name]');
                var hex = row.querySelector('[data-color-hex]');

                if (!name || !hex || name.value.trim() === '' || !hex.value) {
                    return;
                }

                colors.push(name.value.trim() + ':' + hex.value);
            });

            colorValue.value = colors.join(', ');
        };

        var addColorRow = function (name, hex) {
            if (!colorRows || !colorTemplate) {
                return;
            }

            var row = colorTemplate.content.firstElementChild.cloneNode(true);
            var nameInput = row.querySelector('[data-color-name]');
            var hexInput = row.querySelector('[data-color-hex]');

            if (nameInput) {
                nameInput.value = name || '';
            }

            if (hexInput) {
                hexInput.value = hex || '#9C4A26';
            }

            colorRows.appendChild(row);

            if (nameInput) {
                nameInput.focus();
            }
        };

        // A re-rendered form may only carry the serialised value.
        if (colorRows && colorRows.children.length === 0 && colorValue && colorValue.value.trim() !== '') {
            colorValue.value.split(',').forEach(function (part) {
                part = part.trim();

                if (part === '') {
                    return;
                }

                var splitAt = part.lastIndexOf(':');
                var name = splitAt === -1 ? part : part.slice(0, splitAt).trim();
                var hex = splitAt === -1 ? '#CCCCCC' : part.slice(splitAt + 1).trim();

                addColorRow(name, /^#[0-9a-fA-F]{6}$/.test(hex) ? hex : '#CCCCCC');
            });
        }

        if (colorAdd) {
            colorAdd.addEventListener('click', function () {
                addColorRow('', '#9C4A26');
            });
        }

        if (colorRows) {
            colorRows.addEventListener('input', syncColors);
            colorRows.addEventListener('change', syncColors);
            colorRows.addEventListener('click', function (event) {
                var remove = event.target.closest('[data-color-remove]');

                if (!remove) {
                    return;
                }

                var row = remove.closest('.color-row');

                if (row) {
                    row.remove();
                    syncColors();
                }
            });
        }

        var colorForm = colorEditor.closest('form');

        if (colorForm) {
            colorForm.addEventListener('submit', syncColors);
        }

        syncColors();
    }

    /* ---------- Admin: preview of the pictures about to be added -- */
    var imagesInput = document.querySelector('[data-images-input]');
    var imagesPreview = document.querySelector('[data-images-preview]');
    var objectUrls = [];

    if (imagesInput && imagesPreview) {
        imagesInput.addEventListener('change', function () {
            objectUrls.forEach(function (url) {
                URL.revokeObjectURL(url);
            });
            objectUrls = [];
            imagesPreview.textContent = '';

            Array.prototype.forEach.call(imagesInput.files || [], function (file) {
                if (!/^image\//.test(file.type)) {
                    return;
                }

                var url = URL.createObjectURL(file);
                var img = document.createElement('img');

                objectUrls.push(url);
                img.src = url;
                img.alt = '';
                img.width = 70;
                img.height = 88;
                imagesPreview.appendChild(img);
            });
        });
    }

    Array.prototype.forEach.call(document.querySelectorAll('form[data-confirm]'), function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.getAttribute('data-confirm') || 'Confirmer cette action ?')) {
                event.preventDefault();
            }
        });
    });
})();
