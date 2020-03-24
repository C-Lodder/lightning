const { Worker, isMainThread, parentPort } = require('worker_threads')
const buildCss = require('./build-css.js')

const files = {
  'src/css/hiq.css': 'css/template.css',
  'src/css/switch.css': 'css/switch.css',
  'src/css/media/vendor/joomla-custom-elements/joomla-tab.css': 'css/vendor/joomla-custom-elements/joomla-tab.css',
  'src/css/media/layouts/modal/modal.css': 'css/modal.css',
  'src/css/media/vendor/choicesjs/choices.css': 'css/vendor/choicesjs/choices.min.css',
  'src/css/media/system/frontediting.css': 'css/system/frontediting.css',
  'src/css/media/system/fields/calendar.css': 'css/system/fields/calendar.min.css',
  'src/css/media/system/fields/joomla-field-media.css': 'css/system/fields/joomla-field-media.min.css',
  'src/css/media/mod_menu/menu.css': 'css/mod_menu/menu.min.css',
  'src/css/media/layouts/pagination/pagination.css': 'css/pagination.css',
  'src/css/mediamanager/mediamanager.css': 'css/com_media/mediamanager.min.css',
}

if (isMainThread) {
  const worker = new Worker(__filename);
  worker.postMessage('message');
} else {
  parentPort.once('message', (value) => {
    Object.entries(files).forEach(([key, val]) => {
      buildCss(key, val)
    })
  });
}
