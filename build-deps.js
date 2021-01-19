const { Worker, isMainThread, parentPort } = require('worker_threads')
const { mkdir, rmdir, copyFile } = require('fs').promises

const CopyFontAwesome = async() => {
  try {
    await mkdir(`${__dirname}/webfonts`)
    // Copy Font Awesome files
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/css/all.min.css`, `${__dirname}/css/fontawesome.css`)
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/webfonts/fa-brands-400.woff2`, `${__dirname}/webfonts/fa-brands-400.woff2`)
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/webfonts/fa-regular-400.woff2`, `${__dirname}/webfonts/fa-regular-400.woff2`)
    copyFile(`${__dirname}/node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.woff2`, `${__dirname}/webfonts/fa-solid-400.woff2`)
  } catch (error) {
    console.log(error)
  }
}

if (isMainThread) {
  // Delete the CSS directory from the main thread
  rmdir(`${__dirname}/webfonts`, { recursive: true, force: true })

  const worker = new Worker(__filename)
  worker.postMessage('message')
} else {
  parentPort.once('message', () => {
    CopyFontAwesome()
  })
}
