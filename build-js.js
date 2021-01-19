const { Worker, isMainThread, parentPort } = require('worker_threads')
const { readdir, readFile, writeFile, mkdir, copyFile } = require('fs').promises
const { resolve } = require('path')
const Terser = require('terser')

async function* recursiveSearch(dir) {
  const dirents = await readdir(dir, { withFileTypes: true })
  for (const dirent of dirents) {
    const res = resolve(dir, dirent.name)
    if (dirent.isDirectory()) {
      yield* recursiveSearch(res)
    } else {
      yield res
    }
  }
}

async function processJs() {
  for await (const file of recursiveSearch(`${__dirname}/src/js/`)) {
    readFile(file, { encoding: 'utf8' })
      .then(async (response) => {
        try {
          const dest = file.replace(/src\\/g, '')
          await mkdir(dest.substring(0, dest.lastIndexOf('\\')), { recursive: true })
          await copyFile(file, dest)
          const data = await Terser.minify(response)
          writeFile(`${dest.substr(0, dest.lastIndexOf('.'))}.min.js`, data.code)
        } catch(error) {
          console.log(error)
        }
      })
  }
}

if (isMainThread) {
  const worker = new Worker(__filename)
  worker.postMessage('message')
} else {
  parentPort.once('message', () => {
    processJs()
  })
}
