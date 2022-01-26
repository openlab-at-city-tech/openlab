/**
 * Register Customizer Panels
 *
 * Registers defined panels to use in the
 * customizer for this theme using the js
 * api.
 */
const { panels } = egfCustomize;
const { customize } = wp;

export const registerPanels = () => {
  customize.bind('ready', () => {
    for (const panelId in panels) {
      const { capability, description, priority, title, transport } = panels[panelId];
      const panel = new customize.Panel(panelId, {
        capability,
        description,
        priority,
        title,
        transport
      });

      customize.panel.add(panel);
    }
  });
};
