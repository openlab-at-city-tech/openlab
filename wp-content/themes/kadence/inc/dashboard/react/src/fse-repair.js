/**
 * WordPress dependencies
 */
import { __, sprintf } from "@wordpress/i18n";
import { useState, useEffect, useRef } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";
import {
  Button,
  Flex,
  Spinner,
  ToggleControl,
  VisuallyHidden,
} from "@wordpress/components";
import { useDispatch } from "@wordpress/data";

const STATUS = {
  todo: __("To do", "kadence"),
  done: __("Done", "kadence"),
};

/**
 * The repair checklist shown on the dashboard after Full Site Editing mode turns on.
 *
 * @param {Object} props      Component props.
 * @param {string} props.path REST path of the repair checklist.
 *
 * @since 1.6.0
 *
 * @return {Element|null} The checklist.
 */
export default function RepairChecklist({ path }) {
  const [items, setItems] = useState(null);
  const [dismissed, setDismissed] = useState(false);
  const [failed, setFailed] = useState(false);
  const [saving, setSaving] = useState(false);
  const latest = useRef(0);
  const busy = useRef(false);
  const toggle = useRef();
  const moveFocus = useRef(false);
  const { createErrorNotice } = useDispatch("core/notices");

  const apply = (response) => {
    setFailed(false);
    setItems(Array.isArray(response.items) ? response.items : []);
    setDismissed(true === response.dismissed);
  };

  useEffect(() => {
    let current = true;

    // Only the latest request updates the list, so an older one finishing last can't undo a newer status.
    const load = () => {
      // The save's response is newer than anything a refresh would read.
      if (busy.current) {
        return;
      }

      const request = ++latest.current;

      return apiFetch({ path })
        .then(
          (response) => current && request === latest.current && apply(response)
        )
        .catch(() => current && request === latest.current && setFailed(true));
    };

    // Coming back from the Site Editor, the browser can show this page from its cache.
    const onPageShow = (event) => event.persisted && load();
    const onVisibilityChange = () =>
      "visible" === document.visibilityState && load();

    load();
    window.addEventListener("pageshow", onPageShow);
    document.addEventListener("visibilitychange", onVisibilityChange);

    return () => {
      current = false;
      window.removeEventListener("pageshow", onPageShow);
      document.removeEventListener("visibilitychange", onVisibilityChange);
    };
  }, [path]);

  // After a click, focus moves to the button that took the clicked one's place.
  useEffect(() => {
    if (moveFocus.current) {
      moveFocus.current = false;
      toggle.current?.focus();
    }
  }, [dismissed]);

  const save = (next) => {
    // A refresh still in flight would bring back the old choice.
    ++latest.current;
    busy.current = true;
    setSaving(true);

    apiFetch({ path, method: "POST", data: { dismissed: next } })
      .then((response) => {
        moveFocus.current = (true === response.dismissed) === next;
        apply(response);
      })
      .catch((error) =>
        createErrorNotice(
          error.message ||
            __("The setting could not be saved. Please try again.", "kadence"),
          { type: "snackbar" }
        )
      )
      .finally(() => {
        busy.current = false;
        setSaving(false);
      });
  };

  const mark = (item, done) => {
    // The save in flight returns the newest list.
    if (busy.current) {
      return;
    }

    const previous = items;

    // A refresh still in flight would bring back the old status.
    ++latest.current;
    busy.current = true;
    setSaving(true);
    setItems(
      items.map((entry) =>
        entry === item ? { ...entry, status: done ? "done" : "todo" } : entry
      )
    );

    apiFetch({
      path,
      method: "POST",
      data: { type: item.type, slug: item.slug, done },
    })
      .then(apply)
      .catch((error) => {
        setItems(previous);
        createErrorNotice(
          error.message ||
            __("The setting could not be saved. Please try again.", "kadence"),
          { type: "snackbar" }
        );
      })
      .finally(() => {
        busy.current = false;
        setSaving(false);
      });
  };

  if (failed) {
    return (
      <div className="kadence-fse-repair">
        <p role="alert">
          {__(
            "The list of what to rebuild could not be loaded. Reload the page to try again.",
            "kadence"
          )}
        </p>
      </div>
    );
  }

  if (null === items) {
    return (
      <div role="status">
        <Spinner />
        <VisuallyHidden>
          {__("Loading what to rebuild.", "kadence")}
        </VisuallyHidden>
      </div>
    );
  }

  if (!items.length) {
    return null;
  }

  if (dismissed) {
    return (
      <div className="kadence-fse-repair">
        <Button
          ref={toggle}
          variant="link"
          onClick={() => save(false)}
          disabled={saving}
          accessibleWhenDisabled
        >
          {__("Show what to rebuild", "kadence")}
        </Button>
      </div>
    );
  }

  return (
    <div className="kadence-fse-repair">
      <Flex className="kadence-fse-repair-head">
        <h5>{__("Rebuild in the Site Editor", "kadence")}</h5>
        <Button
          ref={toggle}
          variant="tertiary"
          label={
            /* translators: Accessibility text for the Dismiss button. Start it with the word used for "Dismiss". */
            __("Dismiss the list of what to rebuild", "kadence")
          }
          onClick={() => save(true)}
          disabled={saving}
          accessibleWhenDisabled
        >
          {__("Dismiss", "kadence")}
        </Button>
      </Flex>
      <p>
        {items.some((item) => item.required && "todo" === item.status)
          ? __(
              "These parts of your site now use block templates. Open each one in the Site Editor and rebuild it. Saving it marks it done here, or you can mark it done yourself.",
              "kadence"
            )
          : __(
              "Everything on this list is rebuilt. You can keep adjusting these templates in the Site Editor.",
              "kadence"
            )}
      </p>
      <ul className="two-col-grid">
        {items.map((item) => (
          <li
            key={`${item.type}-${item.slug}`}
            className="link-item kadence-fse-repair-item"
          >
            <div className="kadence-fse-repair-item-head">
              <h6>{item.label}</h6>
              {"info" !== item.status && (
                <ToggleControl
                  label={
                    <span
                      className={`kadence-fse-repair-status is-${item.status}`}
                    >
                      {STATUS[item.status]}
                    </span>
                  }
                  aria-label={sprintf(
                    /* translators: 1: Part of the site, such as Header. 2: To do or Done. */
                    __("%1$s: %2$s", "kadence"),
                    item.label,
                    STATUS[item.status]
                  )}
                  checked={"done" === item.status}
                  onChange={(done) => mark(item, done)}
                  aria-disabled={saving || undefined}
                />
              )}
            </div>
            <p>{item.description}</p>
            {item.details.length > 0 && (
              <ul className="kadence-fse-repair-details">
                {item.details.map((detail) => (
                  <li key={detail}>{detail}</li>
                ))}
              </ul>
            )}
            {item.url && (
              <div className="link-item-foot">
                <a href={item.url}>
                  {sprintf(
                    /* translators: %s: Part of the site, such as Header. */
                    __("Open %s in the Site Editor", "kadence"),
                    item.label
                  )}
                </a>
              </div>
            )}
          </li>
        ))}
      </ul>
    </div>
  );
}
