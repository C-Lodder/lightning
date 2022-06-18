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
  'src/css/hiq.css':                                              'media/css/template.css',
  'src/css/switch.css':                                           'media/css/switch.css',
  'src/css/fields/colour-picker.css':                             'media/css/fields/colour-picker.css',
  'src/css/media/vendor/joomla-custom-elements/joomla-tab.css':   'css/vendor/joomla-custom-elements/joomla-tab.css',
  'src/css/media/vendor/joomla-custom-elements/joomla-alert.css': 'css/vendor/joomla-custom-elements/joomla-alert.css',
  'src/css/media/layouts/modal/modal.css':                        'media/css/modal.css',
  'src/css/media/vendor/choicesjs/choices.css':                   'media/css/vendor/choicesjs/choices.min.css',
  'src/css/media/system/frontediting.css':                        'media/css/system/frontediting.css',
  'src/css/media/system/fields/calendar.css':                     'media/css/system/fields/calendar.min.css',
  'src/css/media/system/fields/joomla-field-media.css':           'css/system/fields/joomla-field-media.min.css',
  'src/css/media/mod_menu/menu.css':                              'media/css/mod_menu/menu.min.css',
  'src/css/media/layouts/pagination/pagination.css':              'media/css/pagination.css',
  'src/css/mediamanager/mediamanager.css':                        'media/css/com_media/mediamanager.min.css',
}

// Copy Font Awesome files
async function CopyFontAwesome() {
  await mkdir(`${__dirname}/webfonts`)
  try {
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/css/all.min.css`, `${__dirname}/media/css/fontawesome.css`)
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/webfonts/fa-brands-400.woff2`, `${__dirname}/media/webfonts/fa-brands-400.woff2`)
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/webfonts/fa-regular-400.woff2`, `${__dirname}/media/webfonts/fa-regular-400.woff2`)
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.woff2`, `${__dirname}/media/webfonts/fa-solid-900.woff2`)
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
