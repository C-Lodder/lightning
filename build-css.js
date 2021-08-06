const { Worker, isMainThread, parentPort } = require('worker_threads')
const { readFile, mkdir, writeFile, rm, copyFile, stat } = require('fs').promises
const postcss = require('postcss')

const plugins = [
  require('postcss-easy-import'),
  require('postcss-mixins'),
  require('postcss-custom-selectors'),
  require('postcss-nested'),
  require('autoprefixer'),
  require('postcss-custom-media'),
  require('postcss-discard-comments'),
  require('postcss-each'),
  require('cssnano')({
    preset: 'default',
  })
]

const files = {
  'src/css/hiq.css':                                              'css/template.css',
  'src/css/switch.css':                                           'css/switch.css',
  'src/css/fields/colour-picker.css':                             'css/fields/colour-picker.css',
  'src/css/media/vendor/joomla-custom-elements/joomla-tab.css':   'css/vendor/joomla-custom-elements/joomla-tab.css',
  'src/css/media/vendor/joomla-custom-elements/joomla-alert.css': 'css/vendor/joomla-custom-elements/joomla-alert.css',
  'src/css/media/layouts/modal/modal.css':                        'css/modal.css',
  'src/css/media/vendor/choicesjs/choices.css':                   'css/vendor/choicesjs/choices.min.css',
  'src/css/media/system/frontediting.css':                        'css/system/frontediting.css',
  'src/css/media/system/fields/calendar.css':                     'css/system/fields/calendar.min.css',
  'src/css/media/system/fields/joomla-field-media.css':           'css/system/fields/joomla-field-media.min.css',
  'src/css/media/mod_menu/menu.css':                              'css/mod_menu/menu.min.css',
  'src/css/media/layouts/pagination/pagination.css':              'css/pagination.css',
  'src/css/mediamanager/mediamanager.css':                        'css/com_media/mediamanager.min.css',
}

// Copy Font Awesome files
async function CopyFontAwesome() {
  await mkdir(`${__dirname}/webfonts`)
  try {
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/css/all.min.css`, `${__dirname}/css/fontawesome.css`)
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/webfonts/fa-brands-400.woff2`, `${__dirname}/webfonts/fa-brands-400.woff2`)
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/webfonts/fa-regular-400.woff2`, `${__dirname}/webfonts/fa-regular-400.woff2`)
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.woff2`, `${__dirname}/webfonts/fa-solid-900.woff2`)
  } catch (error) {
    console.log(error)
  }
}

// Process CSS
async function ProcessCss() {
  await mkdir(`${__dirname}/css`)

  CopyFontAwesome()

  Object.entries(files).forEach(async([path, dest]) => {
    try {
      const dir = dest.substring(0, dest.lastIndexOf('/'))
      const css = await readFile(path)
      const compiled = await postcss(plugins).process(css, { from: path, to: dest })
      await mkdir(`${__dirname}/${dir}`, { recursive: true })

      writeFile(dest, compiled.css, { flag: 'wx' }, () => true)
    } catch (error) {
      console.log(error)
    }
  })
}

if (isMainThread) {
  // Delete the dist directories from the main thread
  const dirsToRemove = [
    `${__dirname}/webfonts`,
    `${__dirname}/css`,
  ]
  dirsToRemove.forEach(async(path) => {
    try {
      const dir = await stat(path)
      if (dir.isDirectory()) {
        rm(path, { recursive: true, force: true })
      }
    } catch (error) {
      console.log(error)
    }
  })

  const worker = new Worker(__filename)
  worker.postMessage('message')
} else {
  parentPort.once('message', () => {
    ProcessCss()
  })
}
