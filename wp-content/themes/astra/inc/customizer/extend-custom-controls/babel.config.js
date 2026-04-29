module.exports = {
  presets: ['@wordpress/babel-preset-default'],
  plugins: [
    [
      '@babel/plugin-transform-react-jsx',
      {
        pragma: 'React.createElement',
        pragmaFrag: 'React.Fragment'
      }
    ]
  ]
};