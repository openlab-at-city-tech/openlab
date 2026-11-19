/**
 * Own page-memory caching and IndexedDB persistence.
 * IndexedDB starts from the generation embedded in the HTML and moves forward to newer
 * revisions reported by the HTML or by live responses; it never moves back to an older one,
 * except to revision 0 reported by a live response, which the server only publishes when the
 * cache was created from scratch.
 *
 * Page caches can serve HTML with an old revision, and a page whose strings are all stored makes
 * no request, so stored strings are only served while a live response has confirmed the revision
 * recently (revalidate_after). Otherwise they are looked up again, which confirms it.
 */
(function (window) {
    'use strict';

    /** Larger rows are served from the network every time instead of being stored. */
    const MAX_RECORD_CHARS = 262144;

    /** Page memory keeps at most this many entries and characters (keys + JSON). */
    const PAGE_MEMORY_MAX_ENTRIES = 1000;
    const PAGE_MEMORY_MAX_CHARS = 1048576;

    /** IndexedDB keeps records for a week, and at most this many entries and characters per scope. */
    const PERSISTENT_TTL_MS = 7 * 24 * 60 * 60 * 1000;
    const PERSISTENT_MAX_ENTRIES = 5000;
    const PERSISTENT_MAX_CHARS = 5242880;

    /** Used when the page configuration has no valid revalidate_after (seconds). */
    const DEFAULT_REVALIDATE_AFTER_MS = 30 * 60 * 1000;

    /**
     * How long a lookup waits for IndexedDB before its strings go to the network instead. The
     * transaction timeouts stay longer, so slow work can finish and only stuck work disables the cache.
     */
    const LOOKUP_BUDGET_MS = 150;

    /** Machine (1), manual (2) and similar (3) translations. Keep in sync with TRP_Dynamic_Cache_Store::CACHEABLE_STATUSES. */
    const CACHEABLE_STATUSES = ['1', '2', '3'];

    /** Whether a row holds a translation that may be cached; machine translations are not used for skip strings. */
    function is_cacheable_row(row, language, skip_machine) {
        const translation = row && row.translationsArray && row.translationsArray[language];
        if (!translation || typeof translation.translated !== 'string' || translation.translated.trim() === '') {
            return false;
        }

        const status = String(translation.status);
        if (skip_machine && status === '1') {
            return false;
        }

        return CACHEABLE_STATUSES.indexOf(status) !== -1;
    }

    /** Index response rows by trimmed original; the first row wins, as with Array.find(). */
    function rows_by_original(rows) {
        const index = new Map();
        rows.forEach(function (row) {
            const original = row.original.trim();
            if (!index.has(original)) {
                index.set(original, row);
            }
        });

        return index;
    }

    /** Page-memory reuse with an optional IndexedDB storage layer. */
    class TRP_Dynamic_Cache {
        constructor(trp) {
            this._trp = trp;
            this._completed = new Map();
            this._completedSize = 0;
            this._persistentCache = null;
        }

        static rowsByOriginal(rows) {
            return rows_by_original(rows);
        }

        key(context, original, skip_machine) {
            return JSON.stringify([context, original, skip_machine]);
        }

        eligible(reuse, key) {
            return reuse && key.length < 65536;
        }

        has(key) {
            return this._completed.has(key);
        }

        get(key) {
            const cached = this._completed.get(key);
            if (!cached) {
                return null;
            }

            // Reading an entry refreshes its position in the page-memory LRU.
            this._completed.delete(key);
            this._completed.set(key, cached);
            return JSON.parse(cached.response);
        }

        remember(item, row, language, answered) {
            const positive = is_cacheable_row(row, language, item.skip_machine);

            // The editor needs every row it gets back, translated or not: the row id is what makes a
            // string selectable in the sidebar.
            const keep = !!(item.keep_row && row);

            // Errors must remain retryable. A miss the server answered is kept in page memory only
            // (never IndexedDB) as null, so it is looked up again on the next page load.
            if (!item.eligible || (!positive && !keep && !answered)) {
                return;
            }

            const serialized = JSON.stringify(positive || keep ? row : null);
            const size = item.key.length + serialized.length;
            if (size > PAGE_MEMORY_MAX_CHARS) {
                return;
            }

            if (this._completed.has(item.key)) {
                this._completedSize -= this._completed.get(item.key).size;
                this._completed.delete(item.key);
            }

            while (this._completed.size >= PAGE_MEMORY_MAX_ENTRIES || this._completedSize + size > PAGE_MEMORY_MAX_CHARS) {
                const oldest = this._completed.keys().next().value;
                this._completedSize -= this._completed.get(oldest).size;
                this._completed.delete(oldest);
            }

            this._completed.set(item.key, {
                response: serialized,
                size: size
            });

            this._completedSize += size;
        }

        /** Drop page memory only; IndexedDB is untouched. */
        forget() {
            this._completed.clear();
            this._completedSize = 0;
        }

        persistence(reuse, query_language, display_language) {
            if (!reuse || query_language !== display_language || !this._trp.trp_indexeddb_cache) {
                return null;
            }

            if (!this._persistentCache) {
                this._persistentCache = new IndexedDBCache(this._trp.trp_indexeddb_cache, this._trp);
            }

            return this._persistentCache;
        }

        clear() {
            this._completed.clear();
            this._completedSize = 0;
            if (this._persistentCache) {
                this._persistentCache.disable();
                this._persistentCache = null;
            }
        }
    }

    /** Internal storage adapter; callers use TRP_Dynamic_Cache.persistence(). */
    class IndexedDBCache {
        constructor(config, trp) {
            this._config = config;
            this._trp = trp;
            this._scope = JSON.stringify([
                this._config.protocol,
                this._config.site,
                this._config.source,
                this._config.language
            ]);

            this._db = undefined;
            this._readyPromise = undefined;
            this._disabled = false;

            const revalidate_after = this._config.revalidate_after;
            this._revalidateAfterMs =
                Number.isSafeInteger(revalidate_after) && revalidate_after >= 0
                    ? revalidate_after * 1000
                    : DEFAULT_REVALIDATE_AFTER_MS;

            // Revision a live response confirmed during this page view; trusted regardless of age.
            this._confirmedRevision = null;

            // Meta record as of _ready(), so unconfirmed lookups can skip IndexedDB without a read.
            this._state = null;
        }

        _fail() {
            this._disabled = true;
            if (this._db) {
                this._db.close();
            }

            return null;
        }

        _scopeRange() {
            return IDBKeyRange.bound([this._scope], [this._scope, []]);
        }

        _storageKey(original, skip) {
            return [this._scope, original, skip ? 1 : 0, String(this._trp.trp_translate_numerals_opt)];
        }

        _validSnapshot(snapshot) {
            return (
                snapshot &&
                snapshot.protocol === 2 &&
                Number.isSafeInteger(snapshot.revision) &&
                snapshot.revision >= 0 &&
                typeof snapshot.scope === 'string' &&
                snapshot.scope.length <= 191
            );
        }

        _positive(row, skip) {
            return !!row && typeof row.original === 'string' && is_cacheable_row(row, this._config.language, skip);
        }

        _open() {
            return new Promise((resolve, reject) => {
                let expired = false;
                const timer = setTimeout(() => {
                    expired = true;
                    reject(new Error('IndexedDB open timeout'));
                }, 300);

                const request = window.indexedDB.open('trp-dynamic-cache-v1', 1);
                request.onupgradeneeded = () => {
                    const database = request.result;
                    const records = database.createObjectStore('records');
                    records.createIndex('expiry', ['scope', 'expires']);
                    database.createObjectStore('meta');
                };

                request.onerror = request.onblocked = () => {
                    clearTimeout(timer);
                    expired = true;
                    reject(new Error('IndexedDB unavailable'));
                };

                request.onsuccess = () => {
                    clearTimeout(timer);
                    if (expired) {
                        request.result.close();
                        return;
                    }

                    this._db = request.result;
                    this._db.onversionchange = () => {
                        this._fail();
                    };

                    resolve(this._db);
                };
            });
        }

        // All callbacks enqueue IDB requests synchronously; no awaited network work inside a transaction.
        _transaction(mode, run) {
            return new Promise((resolve, reject) => {
                const tx = this._db.transaction(['records', 'meta'], mode);
                let result;
                const timer = setTimeout(() => {
                    try {
                        tx.abort();
                    } catch (e) {}
                    reject(new Error('IndexedDB transaction timeout'));
                }, 5000);

                tx.oncomplete = () => {
                    clearTimeout(timer);
                    resolve(result);
                };

                tx.onerror = tx.onabort = () => {
                    clearTimeout(timer);
                    reject(tx.error || new Error('IndexedDB transaction aborted'));
                };

                try {
                    run(tx, tx.objectStore('records'), tx.objectStore('meta'), (v) => {
                        result = v;
                    });
                } catch (e) {
                    try {
                        tx.abort();
                    } catch (ignore) {}
                    clearTimeout(timer);
                    reject(e);
                }
            });
        }

        // Exceptions in IDB event callbacks must abort atomically without leaking page errors.
        _guarded(tx, callback) {
            return (event) => {
                try {
                    callback(event);
                } catch (error) {
                    try {
                        tx.abort();
                    } catch (ignore) {}
                }
            };
        }

        _applyGeneration(response) {
            return this._transaction('readwrite', (tx, records, meta, done) => {
                meta.get(this._scope).onsuccess = this._guarded(tx, (event) => {
                    this._moveToGeneration(tx, records, meta, done, response, event.target.result);
                });
            });
        }

        _moveToGeneration(tx, records, meta, done, response, latest) {
            // Cached HTML may contain an older revision, 0 included; preserve the newer cache.
            // Only a live response can take the browser back to 0 (see _acceptsSnapshot).
            if (latest && latest.revision > response.revision) {
                done(latest);
                return;
            }

            const next = {
                revision: response.revision,
                serverScope: response.scope,
                count: latest ? latest.count : 0,
                chars: latest ? latest.chars : 0,
                verifiedAt: latest ? latest.verifiedAt : 0
            };

            const save = () => {
                meta.put(next, this._scope);
                done(next);
            };

            if (!this._sameGeneration(latest, response)) {
                records.delete(this._scopeRange());
                next.count = 0;
                next.chars = 0;
                // The HTML may itself be cached, so it does not confirm the revision.
                next.verifiedAt = 0;
                save();
                return;
            }

            this._deleteByExpiry(tx, records, Date.now(), next, () => true, save);
        }

        _sameGeneration(stored, generation) {
            return !!stored && stored.revision === generation.revision;
        }

        /** Whether a live response confirmed the stored revision recently enough to serve from it. */
        _verified(stored) {
            if (this._confirmedRevision === stored.revision) {
                return true;
            }

            // A clock moved backwards gives a negative age, which is not trusted either.
            const age = Date.now() - (stored.verifiedAt || 0);
            return age >= 0 && age <= this._revalidateAfterMs;
        }

        /** Revision 0 is only written when the server cache is created, so older rows cannot belong to it. */
        _isFreshStart(generation) {
            return generation.revision === 0;
        }

        _overLimits(counters) {
            return counters.count > PERSISTENT_MAX_ENTRIES || counters.chars > PERSISTENT_MAX_CHARS;
        }

        /**
         * Walk this scope's records from the soonest expiry up to `until`, deleting while
         * shouldDelete() holds, then call onDone(). Counters are updated for every deletion.
         */
        _deleteByExpiry(tx, records, until, counters, shouldDelete, onDone) {
            const range = IDBKeyRange.bound([this._scope, 0], [this._scope, until]);
            records.index('expiry').openCursor(range).onsuccess = this._guarded(tx, (event) => {
                const cursor = event.target.result;
                if (!cursor || !shouldDelete()) {
                    onDone();
                    return;
                }

                counters.count--;
                counters.chars -= cursor.value.size;
                cursor.delete();
                cursor.continue();
            });
        }

        _ready() {
            if (this._disabled) {
                return Promise.resolve(null);
            }

            if (!this._readyPromise) {
                if (!this._validSnapshot(this._config.generation)) {
                    return Promise.resolve(this._fail());
                }

                this._readyPromise = this._open()
                    .then(() => {
                        return this._applyGeneration(this._config.generation);
                    })
                    .then((state) => {
                        this._state = state;
                        return state;
                    })
                    .catch(() => this._fail());
            }

            return this._readyPromise;
        }

        /**
         * Stored rows for `originals`, keyed by original. Resolves within LOOKUP_BUDGET_MS: whatever
         * IndexedDB has not answered by then goes to the network, and its late result is ignored.
         * `skip` is a Set of trimmed strings that must not use machine translations.
         */
        lookup(originals, skip) {
            // Known to be unconfirmed: the network response for these strings confirms the revision.
            if (this._state && !this._verified(this._state)) {
                return Promise.resolve({});
            }

            const read = this._ready()
                .then((state) => this._readRecords(state, originals, skip))
                .catch(() => {
                    this._fail();
                    return {};
                });

            const budget = new Promise((resolve) => {
                setTimeout(() => resolve({}), LOOKUP_BUDGET_MS);
            });

            return Promise.race([read, budget]);
        }

        _readRecords(state, originals, skip) {
            if (!state || this._disabled) {
                return {};
            }

            return this._transaction('readonly', (tx, records, meta, done) => {
                const found = Object.create(null);
                done(found);
                meta.get(this._scope).onsuccess = this._guarded(tx, (event) => {
                    const current = event.target.result;
                    // Unconfirmed strings go to the network, and that response confirms the revision.
                    if (!this._sameGeneration(current, state) || !this._verified(current)) {
                        return;
                    }

                    originals.forEach((original) => {
                        this._readRecord(tx, records, original, skip, found);
                    });
                });
            });
        }

        _readRecord(tx, records, original, skip, found) {
            const policy = skip.has(original.trim());
            records.get(this._storageKey(original, policy)).onsuccess = this._guarded(tx, (event) => {
                const row = this._usableRow(event.target.result, original, policy);
                if (row) {
                    found[original] = row;
                }
            });
        }

        /**
         * The cached row for `original`, or null when it is missing, expired or unusable.
         * Every revision change empties the scope, so a stored record is never from another revision.
         */
        _usableRow(record, original, policy) {
            if (!record || record.expires <= Date.now()) {
                return null;
            }

            try {
                const row = JSON.parse(record.json);
                if (this._positive(row, policy) && row.original.trim() === original.trim()) {
                    return row;
                }
            } catch (ignore) {}

            return null;
        }

        store(items, rows, snapshot) {
            if (this._disabled || !this._validSnapshot(snapshot)) {
                return Promise.resolve();
            }

            return this._ready()
                .then((state) => this._writeRecords(state, items, rows, snapshot))
                .catch(() => this._fail());
        }

        _writeRecords(state, items, rows, snapshot) {
            if (!state || this._disabled) {
                return;
            }

            return this._transaction('readwrite', (tx, records, meta) => {
                meta.get(this._scope).onsuccess = this._guarded(tx, (event) => {
                    this._writeForGeneration(tx, records, meta, event.target.result, items, rows, snapshot);
                });
            });
        }

        _writeForGeneration(tx, records, meta, current, items, rows, snapshot) {
            // A response from an older generation must not resurrect invalidated data.
            if (!this._acceptsSnapshot(current, snapshot)) {
                return;
            }

            // A live response proves a newer revision than the (possibly cached) page HTML, or a
            // fresh start: discard the older rows now instead of waiting for the HTML to be purged.
            if (snapshot.revision !== current.revision) {
                records.delete(this._scopeRange());
                current.revision = snapshot.revision;
                current.count = 0;
                current.chars = 0;
            }

            // A live response confirms the revision even when it brings nothing to store.
            // A failed transaction disables the cache, so the page-view flag cannot outlive it.
            current.verifiedAt = Date.now();
            meta.put(current, this._scope);
            this._confirmedRevision = snapshot.revision;

            const candidates = this._candidates(items, rows);
            let remaining = candidates.length;
            if (!remaining) {
                return;
            }

            const evictOverLimits = () => {
                this._deleteByExpiry(
                    tx,
                    records,
                    Number.MAX_SAFE_INTEGER,
                    current,
                    () => this._overLimits(current),
                    () => meta.put(current, this._scope)
                );
            };

            candidates.forEach((candidate) => {
                records.get(candidate.id).onsuccess = this._guarded(tx, (event) => {
                    this._putCandidate(records, current, candidate, event.target.result);

                    remaining--;
                    if (remaining === 0) {
                        evictOverLimits();
                    }
                });
            });
        }

        _acceptsSnapshot(current, snapshot) {
            return (
                !!current &&
                current.serverScope === snapshot.scope &&
                (snapshot.revision >= current.revision || this._isFreshStart(snapshot))
            );
        }

        _candidates(items, rows) {
            const index = rows_by_original(rows);
            const candidates = [];
            items.forEach((item) => {
                const candidate = this._candidate(item, index);
                if (candidate) {
                    candidates.push(candidate);
                }
            });

            return candidates;
        }

        /** The record to store for one requested string, or null when its row cannot be cached. */
        _candidate(item, index) {
            const row = index.get(item.original.trim());

            if (!this._positive(row, item.skip_machine)) {
                return null;
            }

            const json = JSON.stringify(row);
            const id = this._storageKey(item.original, item.skip_machine);
            const size = json.length + JSON.stringify(id).length;
            if (size > MAX_RECORD_CHARS) {
                return null;
            }

            return {
                id: id,
                record: {
                    scope: this._scope,
                    json: json,
                    size: size,
                    expires: Date.now() + PERSISTENT_TTL_MS
                }
            };
        }

        _putCandidate(records, current, candidate, old) {
            if (old) {
                current.chars -= old.size;
            } else {
                current.count++;
            }

            current.chars += candidate.record.size;
            records.put(candidate.record, candidate.id);
        }

        disable() {
            return this._fail();
        }
    }

    window.TRP_Dynamic_Cache = TRP_Dynamic_Cache;
})(window);
