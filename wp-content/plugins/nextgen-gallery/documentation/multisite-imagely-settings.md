# Multisite: Imagely admin settings

Reference for developers and support. End-user facing notes also appear in **Imagely → Settings → Image** and **Network Admin → NextGEN network options** where relevant.

## Path to galleries

| Context | Editable? | Notes |
|--------|-----------|--------|
| Single site | Yes | Must stay under the galleries document root; no `..` segments. |
| Multisite **main site** | Yes | Same validation as single site. |
| Multisite **subsite** (site admin) | **Read-only** in Imagely UI; REST ignores `gallerypath` on save | Prevents subsite admins from redirecting new uploads to another site’s upload tree. |
| Multisite **subsite** (network **super admin**) | Yes | Same validation; can adjust path when needed. |

The React admin uses `window.imagelyApp.canEditGalleryPath` (0/1) from PHP; older bootstraps fall back to `is_multisite` / `is_main_site` only.

Validation is shared: `Imagely\NGG\Settings\GalleryPathValidation::validate_relative_under_galleries_root()` (used by REST and legacy Image Options save).

## Import Folder (server directories)

| Setting | Location | Effect when off |
|--------|----------|-----------------|
| **Enable import function** (`wpmuImportFolder`) | Network Admin → NextGEN network options | Subsite users do not see **Import Folder** in the Imagely uploader; `GET/POST …/folders/browse` and `…/folders/import` return forbidden. Stored in **network** options (`GlobalSettings`), not per-blog `Settings`. |

After changing the network checkbox, **save the form** and **reload** the Imagely admin so bootstrap data refreshes.
