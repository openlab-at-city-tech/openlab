/**
 * Register Customizer Sections
 *
 * Registers the sections in the customizer
 * and defines which panel it should be
 * displayed in. Sections can have multiple
 * controls within it.
 *
 * Note: Controls that register an outer section
 * manage it themselves in the related control
 * file in the customizer/controls/plugins folder.
 */
const { sections } = egfCustomize;

export const registerSections = () => {
  wp.customize.bind('ready', () => {
    for (const sectionId in sections) {
      const { title, panel, description, redirect_url, customize_action } = sections[sectionId];

      // Register a new section.
      const section = new wp.customize.Section(sectionId, {
        customizeAction: customize_action,
        title: title.replace('&amp;', '&'),
        description,
        panel
      });

      wp.customize.section.add(section);

      // Add customizer redirect on section if applicable.
      if (redirect_url) {
        wp.customize.section(sectionId, section => {
          section.expanded.bind(isExpanded => {
            if (isExpanded) {
              wp.customize.previewer.previewUrl.set(redirect_url);
            }
          });
        });
      }
    }
  });
};
