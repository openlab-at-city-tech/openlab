const getInstallablePlugins = function (mandatory = {}, recommended = {}) {
  let plugins = [...Object.keys(recommended), ...Object.keys(mandatory)]
  plugins = plugins.reduce((o, key) => Object.assign(o, { [key]: true }), {})
  return plugins
}

export {
  getInstallablePlugins
}
