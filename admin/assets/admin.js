/* Chulbul Admin — shared JS */

/* ── Auto-filter: typing → debounce submit, select change → instant submit ── */
(function () {
    function initAutoFilter() {
        document.querySelectorAll('form[method="GET"]').forEach(function (form) {

            // Text / search inputs — debounce 400 ms
            form.querySelectorAll('input[type=text], input[type=search]').forEach(function (input) {
                var timer;
                input.addEventListener('input', function () {
                    clearTimeout(timer);
                    setSearching(input, true);
                    timer = setTimeout(function () {
                        form.submit();
                    }, 400);
                });
            });

            // Select dropdowns — instant on change
            form.querySelectorAll('select').forEach(function (sel) {
                sel.addEventListener('change', function () {
                    form.submit();
                });
            });
        });
    }

    // Spinner inside the search input
    function setSearching(input, on) {
        var wrap = input.parentElement;
        var spinner = wrap.querySelector('.autosearch-spinner');
        if (!spinner) {
            spinner = document.createElement('span');
            spinner.className = 'autosearch-spinner';
            spinner.innerHTML =
                '<svg class="spin" width="14" height="14" viewBox="0 0 14 14" fill="none">' +
                '<circle cx="7" cy="7" r="5.5" stroke="#EE483D" stroke-width="1.5" stroke-dasharray="20 14"/>' +
                '</svg>';
            spinner.style.cssText =
                'position:absolute;right:.75rem;top:50%;transform:translateY(-50%);' +
                'pointer-events:none;display:flex;align-items:center;';
            if (getComputedStyle(wrap).position === 'static') wrap.style.position = 'relative';
            wrap.appendChild(spinner);
        }
        spinner.style.display = on ? 'flex' : 'none';
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAutoFilter);
    } else {
        initAutoFilter();
    }
})();
