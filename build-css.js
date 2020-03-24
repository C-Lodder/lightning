const postcss = require('postcss')
const fs = require('fs')

const plugins = [
  require('postcss-easy-import'),
  require('postcss-mixins'),
  require('postcss-custom-selectors'),
  require('postcss-nested'),
  require('autoprefixer'),
  require('postcss-custom-media'),
  require('postcss-discard-comments'),
  require('cssnano')({
    preset: 'default',
  })
]

module.exports = (file, dest) => {
  const dir = dest.substring(0, dest.lastIndexOf('/'))
  fs.readFile(file, (err, css) => {
    postcss(plugins)
      .process(css, { from: file, to: dest })
      .then((result) => {
        fs.mkdir(`${__dirname}/${dir}`, { recursive: true }, (err) => {
          if (err) throw err
          fs.writeFile(dest, result.css, { flag: 'wx' }, () => true)
        })
      })
  })
}
