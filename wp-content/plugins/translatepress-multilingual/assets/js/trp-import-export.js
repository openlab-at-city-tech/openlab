// TranslatePress - Import & Export Translations page: runs a job's batches and updates its progress bar, loads the export counts, toggles the skip notes and the keys warning, confirms the import, blocks a double upload.
(function () {
    'use strict';

    function each(selector, callback) {
        Array.prototype.forEach.call(document.querySelectorAll(selector), callback);
    }

    // The skip notes that match "Also import the settings".
    var settings = document.getElementById('trp-ie-import-settings');
    if (settings) {
        settings.addEventListener('change', function () {
            each('[data-trp-ie-plan]', function (element) {
                element.classList.toggle('trp-hidden', element.getAttribute('data-trp-ie-plan') !== (settings.checked ? 'with' : 'without'));
            });
        });
    }

    // Including the keys turns the choice red and shows what that means.
    var keys = document.getElementById('trp-ie-include-keys');
    if (keys) {
        keys.addEventListener('change', function () {
            keys.parentNode.classList.toggle('trp-ie-keys-choice--on', keys.checked);
            document.getElementById('trp-ie-keys-warning').classList.toggle('trp-hidden', !keys.checked);
        });
    }

    var start = document.getElementById('trp-ie-import-start');
    if (start) {
        start.addEventListener('click', function (event) {
            // An unanswered required choice is reported by the browser, not by the confirmation.
            if (!start.form.checkValidity()) {
                return;
            }
            if (!window.confirm(start.getAttribute('data-trp-ie-confirm'))) {
                event.preventDefault();
            }
        });
    }

    var upload = document.getElementById('trp-ie-upload-form');
    if (upload) {
        upload.addEventListener('submit', function () {
            upload.querySelector('[type="submit"]').disabled = true;
        });
    }

    // The export card's languages table is fetched the first time its details are opened.
    var summary = document.querySelector('[data-trp-ie-summary]');
    if (summary) {
        summary.parentNode.addEventListener('toggle', function () {
            var url = summary.getAttribute('data-trp-ie-summary');
            if (!url) {
                return;
            }
            summary.removeAttribute('data-trp-ie-summary');
            fetch(url, { credentials: 'same-origin' })
                .then(function (response) { return response.text(); })
                .then(function (html) { summary.innerHTML = html; });
        });
    }

    // A running job: fetch its run URL until the server says where to go next.
    var progress = document.querySelector('[data-trp-ie-run]');
    if (progress) {
        var failures = 0;
        var warn = function (event) {
            event.preventDefault();
            event.returnValue = '';
        };
        var run = function () {
            fetch(progress.getAttribute('data-trp-ie-run'), { credentials: 'same-origin' })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    failures = 0;
                    if (data.redirect) {
                        window.removeEventListener('beforeunload', warn);
                        window.location.replace(data.redirect);
                        return;
                    }
                    progress.querySelector('.trp-ie-progress__bar').style.width = data.percent + '%';
                    progress.querySelector('.trp-ie-progress').setAttribute('aria-valuenow', data.percent);
                    progress.querySelector('.trp-ie-progress__label').textContent = data.label;
                    progress.querySelector('.trp-ie-progress__counter').textContent = data.counter;
                    run();
                })
                .catch(function () {
                    failures++;
                    if (failures < 3) {
                        window.setTimeout(run, 2000);
                        return;
                    }
                    window.removeEventListener('beforeunload', warn);
                    progress.querySelector('.spinner').classList.remove('is-active');
                    progress.querySelector('.trp-ie-progress__label').textContent = progress.getAttribute('data-trp-ie-error');
                });
        };
        window.addEventListener('beforeunload', warn);
        run();
    }
})();
