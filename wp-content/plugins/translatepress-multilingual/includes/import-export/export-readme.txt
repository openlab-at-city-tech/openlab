TRANSLATEPRESS EXPORT FILE
==========================

This .zip holds the translations of a WordPress site that uses the
TranslatePress plugin. It can be imported back into TranslatePress
(Settings -> TranslatePress -> Advanced -> Import & Export), on the same
site or on another one.

This file explains what is inside, for someone who translates or reviews
the texts without knowing TranslatePress. The import ignores this file.


WHAT IS IN THE ZIP
------------------

  dictionary/<language>.jsonl    Regular strings (page content), one file
                                 per language.
  gettext/<language>.jsonl       Gettext strings (theme and plugin texts),
                                 one file per language.
  slugs.jsonl                    Slugs (the words in page addresses), all
                                 languages in one file.
  original-meta.jsonl            Technical data. Do not edit.
  gettext-original-meta.jsonl    Technical data. Do not edit.
  settings.json                  The plugin's settings. Do not edit. It
                                 may hold the site's license key and API
                                 keys: do not pass it on to others.
  manifest.json                  A description of this export. Do not edit.

<language> is a language code such as fr_FR (French, France) or de_DE
(German, Germany). A file is only there when it has something in it.

The original texts are in the site's default language. Its code is the
"default_language" value in manifest.json.


THE THREE KINDS OF TEXT
-----------------------

Regular strings
  The site's own content, as visitors see it on the pages: headings,
  paragraphs, button labels, menu items, image descriptions, product
  names and so on.

Gettext strings
  Texts that come from the code of the theme and the plugins, not from the
  site's content: "Add to cart", "Search results for: %s", "Leave a
  comment", form messages. Most of them arrive already translated by the
  theme's or plugin's authors (status 4, see below).

Slugs
  The readable part of a page address. In
  https://example.com/fr/produit/chaise-bleue/ the slugs are "produit" and
  "chaise-bleue". A translated slug changes the page's address in that
  language, so slugs are written in lowercase, with hyphens instead of
  spaces, and without punctuation.


FILE FORMAT
-----------

Every .jsonl file is plain text in UTF-8, with one JSON object per line.
One line is one text. Example line from dictionary/fr_FR.jsonl:

  {"o":"Read more","t":"Lire la suite","s":2,"b":0}

Fields in dictionary/<language>.jsonl (regular strings):

  o    The original text. Never change it: it is how the translation is
       matched to the text on the site.
  t    The translation. This is the field to edit.
  s    The status of the translation (see STATUS).
  b    The kind of block (see BLOCK TYPE). Do not change it.

Fields in gettext/<language>.jsonl (gettext strings):

  o    The original text. Never change it.
  t    The translation. This is the field to edit.
  s    The status of the translation (see STATUS).
  d    The text domain: the theme or plugin the text belongs to, for
       example "woocommerce". "default" is WordPress itself. Do not change.
  c    The context, a hint the developer left to tell apart two identical
       texts with different meanings. "trp_context" means there is none.
       Do not change.
  p    The plural form this line holds. 0 is the singular (or a text with
       no plural). 1, 2, ... are the plural forms of the language, in the
       order its plural rules define them. Do not change.
  op   The original plural text, when the text has one. Do not change.

Fields in slugs.jsonl:

  o    The original slug. Never change it.
  type What the slug belongs to: "post" (a page, post or product), "term"
       (a category or tag), "taxonomy" (the address base of a category
       type, such as "product-category"), "post-type-base" (the address
       base of a content type, such as "product") or "other".
  tr   The translations, one entry per language code:
         "tr":{"fr_FR":{"t":"chaise-bleue","s":2},"de_DE":{"t":"blauer-stuhl","s":2}}
       t is the translated slug and s its status.


STATUS
------

The "s" field says where a translation came from:

  0    Not translated. There is no translation yet; "t" is empty.
  1    Machine translated. Made by an automatic translation service and
       not checked by a person.
  2    Human reviewed. Written or approved by a person.
  3    Similar translation. Copied automatically from the translation of a
       very similar text, and not checked by a person.
  4    Gettext strings only: the translation comes from the language files
       shipped with the theme or plugin, and nobody changed it on the site.

When you write or correct a translation, set its status to 2. On import,
a status 2 translation replaces a machine translation (1) or a similar
translation (3) already on the site. With any other status, the site
keeps the translation it has, unless the person who imports the file
chooses to replace the site's translations.


BLOCK TYPE
----------

The "b" field of a regular string:

  0    A regular string: one piece of text, such as a heading or a link.
  1    A translation block: a whole paragraph translated as one piece,
       including the HTML tags inside it.
  2    A translation block that was split back into separate strings. It
       is kept for reference and is not shown on the site.


RULES FOR EDITING
-----------------

  - Change only "t" and "s". Leave everything else as it is.
  - Keep one JSON object per line, and do not add, join or reorder lines.
  - Keep the file valid JSON: inside a text, a double quote is written \"
    a backslash \\ and a line break \n. A line that is not valid JSON is
    skipped on import.
  - Keep HTML tags (<a href="...">, <strong>, <br>) and HTML entities
    (&amp;, &rsquo;) in the translation, around the same words.
  - Keep placeholders exactly as they are: %s, %d, %1$s, {name}, [name].
    They are replaced with real values on the site.
  - Save the files as UTF-8 and put them back in the .zip under the same
    names and folders.
  - An editor made for code or plain text is safer than a word processor,
    which may change the quotes and the encoding.
