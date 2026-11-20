/** Coordinate dynamic translation requests without depending on DOM nodes. */
(function (window) {
    'use strict';

    window.TRP_Translation_Requests = function (trp) {
        const cache = new window.TRP_Dynamic_Cache(trp);
        const custom_ajax_url = trp.trp_custom_ajax_url;
        const wp_ajax_url = trp.trp_wp_ajax_url;
        const burst_trigger_window_ms = 1000;
        const burst_trigger_requests = 4;
        const burst_window_ms = 100;
        const burst_quiet_period_ms = 1000;
        // Bounds for one merged request and for the per-page bookkeeping maps.
        const max_request_originals = 500;
        const max_request_characters = 65536;
        const max_batch_jobs = 500;
        const max_tracked_entries = 100;
        const request_timeout_ms = 20000;
        const max_retry_after_seconds = 60;
        const translation_pending = new Map();
        const translation_retry_after = new Map();
        const translation_batches = new Map();
        let translation_generation = 0;
        let memory_generation = 0;
        const recent_uncached_lookups = [];
        let translation_burst_until = 0;

        /** Strings are compared trimmed; one Set per request keeps each membership check constant. */
        function trimmed_set(values) {
            return new Set(
                values.map(function (value) {
                    return value.trim();
                })
            );
        }

        this.clear = function () {
            translation_generation++;
            recent_uncached_lookups.length = 0;
            translation_burst_until = 0;
            Array.from(translation_batches.values()).forEach(function (batch) {
                flush_translation_batch(batch, true);
            });

            translation_pending.clear();
            translation_retry_after.clear();
            cache.clear();
        };

        // The editor saves without reloading the preview, so answers fetched before a save must not be reused.
        this.forget = function () {
            memory_generation++;
            translation_pending.clear();
            cache.forget();
        };

        function flush_translation_batch(batch, cancel) {
            clearTimeout(batch.timer);
            if (translation_batches.get(batch.key) === batch) {
                translation_batches.delete(batch.key);
            }

            const jobs = [];
            const originals = new Set();
            const skip = new Set();
            batch.jobs.forEach(function (job) {
                if (cancel || !job.current()) {
                    job.finish(null);
                    return;
                }

                jobs.push(job);
                job.originals.forEach(function (original) {
                    originals.add(original);
                });

                job.skip.forEach(function (original) {
                    skip.add(original);
                });
            });

            if (!jobs.length) {
                return;
            }

            const first = jobs[0];
            const data = Object.assign({}, first.data, {
                originals: JSON.stringify(Array.from(originals)),
                skip_machine_translation: JSON.stringify(Array.from(skip))
            });

            send_translation_request(data, first.url, false, first.persistent, function (response, snapshot) {
                jobs.forEach(function (job) {
                    job.finish(response, snapshot);
                });
            });
        }

        // More than four uncached lookups in one second activate fixed 100 ms batching windows.
        function queue_translation_request(context, generation, job) {
            // Only unresolved work reaches this point; cache hits and shared pending work do not count.
            const now = performance.now();
            const retry_key = JSON.stringify([job.url, job.data.security, job.data.language]);
            if (now < (translation_retry_after.get(retry_key) || 0)) {
                job.finish(null);
                return;
            }

            // Retain only the five most recent misses: enough to detect the threshold without growing.
            const cutoff = now - burst_trigger_window_ms;
            while (recent_uncached_lookups.length && recent_uncached_lookups[0] <= cutoff) {
                recent_uncached_lookups.shift();
            }

            recent_uncached_lookups.push(now);
            if (recent_uncached_lookups.length > burst_trigger_requests + 1) {
                recent_uncached_lookups.shift();
            }

            const burst =
                now < translation_burst_until || recent_uncached_lookups.length > burst_trigger_requests;
            if (!burst) {
                send_translation_request(job.data, job.url, false, job.persistent, job.finish);
                return;
            }

            // No expiry timer is needed: the next miss checks whether the page has been quiet.
            translation_burst_until = now + burst_quiet_period_ms;
            const key = JSON.stringify([context, generation, job.persistent]);
            job.originals = JSON.parse(job.data.originals);
            job.skip = JSON.parse(job.data.skip_machine_translation);
            // Keep existing large single calls intact; only bound requests constructed by merging calls.
            const characters = job.data.originals.length + job.data.skip_machine_translation.length;
            if (job.originals.length > max_request_originals || characters > max_request_characters) {
                send_translation_request(job.data, job.url, false, job.persistent, job.finish);
                return;
            }

            let batch = translation_batches.get(key);
            const job_skip = trimmed_set(job.skip);
            const policies = job.originals.map(function (original) {
                return [original.trim(), job_skip.has(original.trim())];
            });

            const conflict =
                batch &&
                policies.some(function (policy) {
                    return batch.policies.has(policy[0]) && batch.policies.get(policy[0]) !== policy[1];
                });

            if (
                batch &&
                (conflict ||
                    batch.count + job.originals.length > max_request_originals ||
                    batch.size + characters > max_request_characters ||
                    batch.jobs.length >= max_batch_jobs)
            ) {
                flush_translation_batch(batch, false);
                batch = null;
            }

            if (!batch) {
                // Limit independent contexts as well as the size of each combined request.
                if (translation_batches.size >= max_tracked_entries) {
                    flush_translation_batch(translation_batches.values().next().value, false);
                }

                batch = {
                    key: key,
                    jobs: [],
                    policies: new Map(),
                    count: 0,
                    size: 0
                };

                translation_batches.set(key, batch);
                batch.timer = setTimeout(function () {
                    flush_translation_batch(batch, false);
                }, burst_window_ms);
            }

            batch.jobs.push(job);
            batch.count += job.originals.length;
            batch.size += characters;
            policies.forEach(function (policy) {
                batch.policies.set(policy[0], policy[1]);
            });
        }

        function send_translation_request(data, url, is_editor, persistent, finish) {
            // One malformed or untranslated row (e.g. translated = NULL) must not discard the others.
            function normalize(response) {
                if (!Array.isArray(response)) {
                    return null;
                }

                return response.filter(function (row) {
                    const translation =
                        row && typeof row.original === 'string' && row.translationsArray
                            ? row.translationsArray[data.language]
                            : null;
                    if (!translation || typeof translation !== 'object') {
                        return false;
                    }

                    if (typeof translation.translated !== 'string') {
                        translation.translated = '';
                    }

                    return true;
                });
            }

            function request(endpoint, can_fallback) {
                const retry_key = JSON.stringify([endpoint, data.security, data.language]);
                if (!is_editor && performance.now() < (translation_retry_after.get(retry_key) || 0)) {
                    finish(null);
                    return;
                }

                translation_retry_after.delete(retry_key);

                function failed(xhr, status) {
                    if (xhr && xhr.status === 429) {
                        if (!is_editor) {
                            const retry = xhr.getResponseHeader('Retry-After');
                            const seconds = /^\d+$/.test(retry || '')
                                ? Number(retry)
                                : (Date.parse(retry) - Date.now()) / 1000;
                            const delay = Number.isFinite(seconds)
                                ? Math.max(1, Math.min(seconds, max_retry_after_seconds)) * 1000
                                : 1000;
                            if (translation_retry_after.size >= max_tracked_entries) {
                                translation_retry_after.clear();
                            }

                            translation_retry_after.set(retry_key, performance.now() + delay);
                        }

                        finish(null);
                    } else if (can_fallback && status !== 'abort') {
                        request(wp_ajax_url, false);
                    } else {
                        finish(null);
                    }
                }

                jQuery.ajax({
                    url: endpoint,
                    type: 'post',
                    dataType: 'json',
                    timeout: is_editor ? 0 : request_timeout_ms,
                    data: persistent
                        ? Object.assign({}, data, {
                              trp_cache_protocol: '2'
                          })
                        : data,
                    success: function (response) {
                        let snapshot = null;
                        if (persistent && response && Array.isArray(response.rows)) {
                            snapshot = response.cache;
                            response = response.rows;
                        }

                        response = normalize(response);
                        if (response) {
                            finish(response, snapshot);
                        } else {
                            failed();
                        }
                    },
                    error: failed
                });
            }

            request(
                url,
                !is_editor && url === custom_ajax_url && custom_ajax_url !== wp_ajax_url
            );
        }

        this.request = function (options, consumer) {
            const {
                is_editor,
                page_url,
                display_language,
                data,
                url,
                string_originals,
                skip_machine_translation
            } = options;
            const generation = translation_generation;
            const memory_at_start = memory_generation;
            const context_key = JSON.stringify([url, page_url, display_language, data]);
            const share = trp.trp_share_dynamic_translation_requests !== 'no';
            // The editor shares page memory and pending requests, but never IndexedDB or burst batching.
            const reuse = !is_editor && share;
            const originals = Array.from(new Set(string_originals));
            const skip_set = trimmed_set(skip_machine_translation);
            const missing = [];
            const responses = [];
            let remaining = originals.length;

            function current_context() {
                return generation === translation_generation && options.is_current();
            }

            const page_cache = cache.persistence(reuse, data.language, display_language);

            function deliver(response) {
                consumer(response, current_context);
            }

            function remember(item, row, answered) {
                if (current_context() && data.language === display_language && memory_at_start === memory_generation) {
                    cache.remember(item, row, data.language, answered);
                }
            }

            function start(persisted) {
                if (!current_context()) {
                    deliver(null);
                    return;
                }

                originals.forEach(function (original, index) {
                    const skip_machine = skip_set.has(original.trim());
                    const key = cache.key(context_key, original, skip_machine);
                    const eligible = cache.eligible(share, key);

                    function receive(row) {
                        responses[index] = row;
                        remaining--;
                        if (remaining === 0) {
                            // Cached and network results use the same asynchronous, guarded delivery.
                            setTimeout(function () {
                                deliver(
                                    responses.filter(function (value) {
                                        return value !== null;
                                    })
                                );
                            }, 0);
                        }
                    }

                    if (eligible && data.language === display_language && cache.has(key)) {
                        receive(cache.get(key));
                        return;
                    }

                    if (eligible && persisted && Object.prototype.hasOwnProperty.call(persisted, original)) {
                        remember(
                            {
                                key: key,
                                eligible: eligible,
                                skip_machine: skip_machine
                            },
                            persisted[original]
                        );

                        receive(persisted[original]);
                        return;
                    }

                    const pending = eligible && translation_pending.get(key);
                    if (pending && pending.listeners.length < max_tracked_entries) {
                        pending.listeners.push(receive);
                        return;
                    }

                    const entry = {
                        listeners: [receive]
                    };

                    // A full subscriber group starts another shared group, not one request per overflow node.
                    // The completing group only removes itself, so older groups cannot delete this new entry.
                    if (eligible && (pending || translation_pending.size < max_tracked_entries)) {
                        translation_pending.set(key, entry);
                    }

                    missing.push({
                        original: original,
                        key: key,
                        entry: entry,
                        eligible: eligible,
                        skip_machine: skip_machine,
                        keep_row: is_editor
                    });
                });

                if (missing.length === 0) {
                    return;
                }

                // Only send originals not already cached or being fetched by another request.
                data.originals = JSON.stringify(
                    missing.map(function (item) {
                        return item.original;
                    })
                );

                const missing_originals = trimmed_set(
                    missing.map(function (item) {
                        return item.original;
                    })
                );

                data.skip_machine_translation = JSON.stringify(
                    skip_machine_translation.filter(function (original) {
                        return missing_originals.has(original.trim());
                    })
                );

                let finished = false;

                function finish(response, snapshot) {
                    if (finished) {
                        return;
                    }

                    finished = true;
                    if (page_cache && response && snapshot && current_context()) {
                        page_cache.store(missing, response, snapshot);
                    }

                    const rows = response ? window.TRP_Dynamic_Cache.rowsByOriginal(response) : null;
                    missing.forEach(function (item) {
                        const row = rows && rows.get(item.original.trim());

                        if (translation_pending.get(item.key) === item.entry) {
                            translation_pending.delete(item.key);
                        }

                        // Only a successful response is an answer; cancelled, throttled or failed lookups are not.
                        remember(item, row, Array.isArray(response));
                        const listeners = item.entry.listeners;
                        item.entry.listeners = [];
                        listeners.forEach(function (listener) {
                            listener(row || null);
                        });
                    });
                }

                const job = {
                    data: data,
                    url: url,
                    persistent: !!page_cache,
                    finish: finish,
                    current: current_context
                };

                if (reuse) {
                    queue_translation_request(context_key, generation, job);
                } else {
                    send_translation_request(data, url, is_editor, !!page_cache, finish);
                }
            }

            if (page_cache) {
                const disk_originals = originals.filter(function (original) {
                    const key = cache.key(context_key, original, skip_set.has(original.trim()));
                    return cache.eligible(reuse, key) && !cache.has(key) && !translation_pending.has(key);
                });

                if (disk_originals.length) {
                    page_cache.lookup(disk_originals, skip_set).then(start);
                } else {
                    start(null);
                }
            } else {
                start(null);
            }
        };
    };
})(window);
