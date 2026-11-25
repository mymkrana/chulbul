/**
 * Chulbul Design — Reusable "Work Worth Showing Off" Portfolio Component JS
 * Zero external dependencies · Supports multiple instances per page
 */
(function () {
    'use strict';

    var AUTO_ADVANCE_MS = 7500;
    var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function pad2(n) {
        return n < 10 ? '0' + n : String(n);
    }

    function computePan(imgEl, screenEl) {
        if (!imgEl || !screenEl) return;
        var apply = function () {
            var screenH = screenEl.clientHeight || 350;
            var imgH = imgEl.getBoundingClientRect().height || imgEl.naturalHeight || screenH;
            var distance = Math.max(0, Math.round(imgH - screenH));
            var durationSec = Math.max(9, Math.min(28, Math.round(distance / 85) + 6));
            imgEl.style.setProperty('--cw-pan-distance', distance + 'px');
            imgEl.style.setProperty('--cw-pan-duration', durationSec + 's');
        };
        if (imgEl.complete && imgEl.naturalHeight > 0) {
            requestAnimationFrame(apply);
        } else {
            imgEl.onload = function () { requestAnimationFrame(apply); };
        }
    }

    function initShowcase(root) {
        var dataEl = root.querySelector('[data-cbd-work-json]');
        if (!dataEl) return;

        var projects = [];
        try {
            projects = JSON.parse(dataEl.textContent || '[]');
        } catch (e) {
            return;
        }
        if (!projects.length) return;

        var startIdx = Number(root.getAttribute('data-start-index') || 0);
        if (isNaN(startIdx) || startIdx < 0 || startIdx >= projects.length) startIdx = 0;

        var state = {
            index: startIdx,
            device: 'desktop',
            pageIndex: 0,
            paused: false,
            timer: null,
            modalOpen: false
        };

        var stage = root.querySelector('[data-cw-stage]');
        var tabs = Array.prototype.slice.call(root.querySelectorAll('[data-cw-tab]'));
        var deviceCol = root.querySelector('[data-cw-device-col]');
        var desktopScreen = root.querySelector('[data-cw-desktop-screen]');
        var mobileScreen = root.querySelector('[data-cw-mobile-screen]');
        var desktopImg = root.querySelector('[data-cw-desktop-img]');
        var mobileImg = root.querySelector('[data-cw-mobile-img]');
        var addressText = root.querySelector('[data-cw-address]');

        var devBtns = Array.prototype.slice.call(root.querySelectorAll('[data-cw-device]'));
        var subpagesWrap = root.querySelector('[data-cw-subpages]');
        var subpageLabel = root.querySelector('[data-cw-subpage-label]');
        var subpageDots = root.querySelector('[data-cw-subpage-dots]');
        var subPrev = root.querySelector('[data-cw-sub-prev]');
        var subNext = root.querySelector('[data-cw-sub-next]');

        var catBadge = root.querySelector('[data-cw-cat]');
        var techLabel = root.querySelector('[data-cw-tech]');
        var projTitle = root.querySelector('[data-cw-title]');
        var projDesc = root.querySelector('[data-cw-desc]');
        var highlightsEl = root.querySelector('[data-cw-highlights]');
        var metricsEl = root.querySelector('[data-cw-metrics]');
        var watermarkEl = root.querySelector('[data-cw-watermark]');
        var viewBtn = root.querySelector('[data-cw-view-btn]');
        var similarBtn = root.querySelector('[data-cw-similar-btn]');

        var counterCur = root.querySelector('[data-cw-cur]');
        var counterTotal = root.querySelector('[data-cw-total]');
        var progressFill = root.querySelector('[data-cw-progress]');
        var prevBtn = root.querySelector('[data-cw-prev]');
        var nextBtn = root.querySelector('[data-cw-next]');

        // Modal refs
        var modal = root.querySelector('[data-cw-modal]');
        var modalTitle = root.querySelector('[data-cw-modal-title]');
        var modalCat = root.querySelector('[data-cw-modal-cat]');
        var modalFrame = root.querySelector('[data-cw-modal-frame]');
        var modalImg = root.querySelector('[data-cw-modal-img]');
        var modalPages = root.querySelector('[data-cw-modal-pages]');
        var modalCta = root.querySelector('[data-cw-modal-cta]');
        var modalDevBtns = Array.prototype.slice.call(root.querySelectorAll('[data-cw-modal-device]'));
        var modalCloseBtns = Array.prototype.slice.call(root.querySelectorAll('[data-cw-modal-close]'));

        root.style.setProperty('--cw-auto-duration', AUTO_ADVANCE_MS + 'ms');
        if (counterTotal) counterTotal.textContent = pad2(projects.length);

        function triggerWake(img) {
            if (!img) return;
            img.classList.remove('is-waking');
            void img.offsetWidth;
            img.classList.add('is-waking');
        }

        function renderMedia() {
            var proj = projects[state.index];
            var page = proj.pages[state.pageIndex] || proj.pages[0];
            if (!page) return;

            if (deviceCol) deviceCol.setAttribute('data-device', state.device);
            devBtns.forEach(function (b) {
                var active = b.getAttribute('data-cw-device') === state.device;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-pressed', active ? 'true' : 'false');
            });

            if (addressText) {
                addressText.textContent = proj.name + (proj.pages.length > 1 ? ' — ' + page.label : '');
            }

            if (desktopImg) {
                desktopImg.src = page.desktop;
                desktopImg.alt = proj.name + ' — ' + page.label + ' desktop preview';
                triggerWake(desktopImg);
                computePan(desktopImg, desktopScreen);
            }

            if (mobileImg) {
                mobileImg.src = page.mobile;
                mobileImg.alt = proj.name + ' — ' + page.label + ' mobile preview';
                triggerWake(mobileImg);
                computePan(mobileImg, mobileScreen);
            }

            // Subpage controls
            if (subpagesWrap) {
                var hasMulti = proj.pages.length > 1;
                subpagesWrap.classList.toggle('is-hidden', !hasMulti);
                if (hasMulti) {
                    if (subpageLabel) subpageLabel.textContent = page.label;
                    if (subpageDots) {
                        subpageDots.innerHTML = '';
                        proj.pages.forEach(function (pg, idx) {
                            var dot = document.createElement('button');
                            dot.type = 'button';
                            dot.className = 'cbd-work-subdot' + (idx === state.pageIndex ? ' is-active' : '');
                            dot.setAttribute('aria-label', 'View ' + pg.label + ' screen');
                            dot.addEventListener('click', function () {
                                state.pageIndex = idx;
                                renderMedia();
                                restartTimer();
                            });
                            subpageDots.appendChild(dot);
                        });
                    }
                }
            }
        }

        function renderProject(resetPage) {
            var proj = projects[state.index];
            if (!proj) return;
            if (resetPage !== false) state.pageIndex = 0;

            // Tabs & progress animation restart
            tabs.forEach(function (tab, i) {
                var active = i === state.index;
                tab.classList.toggle('is-active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
                var bar = tab.querySelector('.cbd-work-tab-progress');
                if (bar) {
                    bar.style.animation = 'none';
                    if (active) {
                        void bar.offsetWidth;
                        bar.style.animation = '';
                    }
                }
                if (active && tab.scrollIntoView) {
                    // Keep active pill gently visible inside horizontal tab strip without scrolling page
                    var tabsParent = tab.parentElement;
                    if (tabsParent && tabsParent.scrollWidth > tabsParent.clientWidth) {
                        var left = tab.offsetLeft - (tabsParent.clientWidth / 2) + (tab.clientWidth / 2);
                        tabsParent.scrollTo({ left: Math.max(0, left), behavior: 'smooth' });
                    }
                }
            });

            if (catBadge) catBadge.textContent = proj.category;
            if (techLabel) {
                techLabel.textContent = proj.tech || '';
                techLabel.style.display = proj.tech ? 'inline' : 'none';
            }
            if (projTitle) projTitle.textContent = proj.name;
            if (projDesc) projDesc.textContent = proj.summary;
            if (watermarkEl) watermarkEl.textContent = pad2(state.index + 1);

            if (highlightsEl) {
                highlightsEl.innerHTML = proj.highlights.map(function (h) {
                    return '<li><i class="bi bi-check2" aria-hidden="true"></i><span>' + escapeHtml(h) + '</span></li>';
                }).join('');
            }

            if (metricsEl) {
                var m = proj.metrics || [];
                metricsEl.classList.toggle('is-empty', m.length === 0);
                metricsEl.classList.toggle('is-two', m.length === 2);
                metricsEl.innerHTML = m.map(function (item) {
                    return '<div class="cbd-work-metric-card">' +
                        '<span class="cbd-work-metric-val">' + escapeHtml(item.val) + '</span>' +
                        '<span class="cbd-work-metric-lbl">' + escapeHtml(item.label) + '</span>' +
                        '</div>';
                }).join('');
            }

            if (similarBtn) {
                var waText = encodeURIComponent('Hi Chulbul Design, I loved your ' + proj.name + ' (' + proj.category + ') project and want to build something similar.');
                similarBtn.href = 'https://wa.me/919990548795?text=' + waText;
            }

            if (counterCur) counterCur.textContent = pad2(state.index + 1);
            if (progressFill) {
                var pct = ((state.index + 1) / projects.length) * 100;
                progressFill.style.width = pct.toFixed(2) + '%';
            }

            renderMedia();
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function stopTimer() {
            if (state.timer) {
                clearInterval(state.timer);
                state.timer = null;
            }
        }

        function startTimer() {
            stopTimer();
            if (prefersReducedMotion || projects.length <= 1 || state.paused || state.modalOpen) return;
            state.timer = setInterval(function () {
                state.index = (state.index + 1) % projects.length;
                renderProject(true);
            }, AUTO_ADVANCE_MS);
        }

        function restartTimer() {
            stopTimer();
            startTimer();
        }

        function setPaused(val) {
            state.paused = val;
            root.classList.toggle('is-paused', val);
            if (val) stopTimer();
            else startTimer();
        }

        // Modal controls
        function syncModal() {
            if (!modal) return;
            var proj = projects[state.index];
            var page = proj.pages[state.pageIndex] || proj.pages[0];
            if (modalTitle) modalTitle.textContent = proj.name;
            if (modalCat) modalCat.textContent = proj.category + (proj.tech ? ' · ' + proj.tech : '');
            if (modalFrame) modalFrame.classList.toggle('is-mobile', state.device === 'mobile');
            if (modalImg && page) {
                modalImg.src = state.device === 'mobile' ? page.mobile : page.desktop;
                modalImg.alt = proj.name + ' — ' + page.label;
            }
            modalDevBtns.forEach(function (b) {
                b.classList.toggle('is-active', b.getAttribute('data-cw-modal-device') === state.device);
            });
            if (modalPages) {
                modalPages.innerHTML = '';
                proj.pages.forEach(function (pg, idx) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'cbd-work-modal-page-btn' + (idx === state.pageIndex ? ' is-active' : '');
                    btn.textContent = pg.label;
                    btn.addEventListener('click', function () {
                        state.pageIndex = idx;
                        renderMedia();
                        syncModal();
                    });
                    modalPages.appendChild(btn);
                });
            }
            if (modalCta) {
                var waText = encodeURIComponent('Hi Chulbul Design, I saw your ' + proj.name + ' project in your portfolio and would like to discuss a similar build.');
                modalCta.href = 'https://wa.me/919990548795?text=' + waText;
            }
        }

        function openModal() {
            if (!modal) return;
            state.modalOpen = true;
            setPaused(true);
            syncModal();
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            if (!modal || !state.modalOpen) return;
            state.modalOpen = false;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            setPaused(false);
        }

        // Bind events
        tabs.forEach(function (tab, i) {
            tab.addEventListener('click', function () {
                state.index = i;
                renderProject(true);
                restartTimer();
            });
        });

        devBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                state.device = btn.getAttribute('data-cw-device') === 'mobile' ? 'mobile' : 'desktop';
                renderMedia();
                restartTimer();
            });
        });

        if (subPrev) {
            subPrev.addEventListener('click', function () {
                var len = projects[state.index].pages.length;
                if (len <= 1) return;
                state.pageIndex = (state.pageIndex - 1 + len) % len;
                renderMedia();
                restartTimer();
            });
        }

        if (subNext) {
            subNext.addEventListener('click', function () {
                var len = projects[state.index].pages.length;
                if (len <= 1) return;
                state.pageIndex = (state.pageIndex + 1) % len;
                renderMedia();
                restartTimer();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                state.index = (state.index - 1 + projects.length) % projects.length;
                renderProject(true);
                restartTimer();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                state.index = (state.index + 1) % projects.length;
                renderProject(true);
                restartTimer();
            });
        }

        if (viewBtn) {
            viewBtn.addEventListener('click', openModal);
        }

        modalDevBtns.forEach(function (b) {
            b.addEventListener('click', function () {
                state.device = b.getAttribute('data-cw-modal-device') === 'mobile' ? 'mobile' : 'desktop';
                renderMedia();
                syncModal();
            });
        });

        modalCloseBtns.forEach(function (b) {
            b.addEventListener('click', closeModal);
        });

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && state.modalOpen) closeModal();
        });

        if (stage) {
            stage.addEventListener('mouseenter', function () { setPaused(true); });
            stage.addEventListener('mouseleave', function () { if (!state.modalOpen) setPaused(false); });
        }

        // Recalculate smooth pan on resize
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                computePan(desktopImg, desktopScreen);
                computePan(mobileImg, mobileScreen);
            }, 150);
        });

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) stopTimer();
            else if (!state.paused && !state.modalOpen) startTimer();
        });

        // Initial render
        renderProject(true);
        startTimer();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-cbd-work]').forEach(initShowcase);
    });
})();
