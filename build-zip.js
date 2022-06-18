const { createWriteStream } = require('fs')
const archiver = require('archiver')
const pkg = require('./package.json')

const templateOutput = createWriteStream(`${__dirname}/tpl_lightning-v${pkg.version}.zip`)
const pluginOutput = createWriteStream(`${__dirname}/plg_sampledata-lightning_v${pkg.version}.zip`)

const template = archiver('zip', {
  zlib: { level: 9 }
})

const plugin = archiver('zip', {
  zlib: { level: 9 }
})

// Listen for all archive data to be written
templateOutput.on('close', () => {
  console.log(`Template has been packaged successfully`)
})
pluginOutput.on('close', () => {
  console.log(`Plugin has been packaged successfully`)
})

// Catch this error explicitly
template.on('error', (err) => {
  throw err
})
plugin.on('error', (err) => {
  throw err
})
 
// Pipe archive data to the file
template.pipe(templateOutput)
plugin.pipe(pluginOutput)

// Append the files and directories
template.file('component.php')
template.file('error.php')
template.file('helper.php')
template.file('index.php')
template.file('offline.php')
template.file('joomla.asset.json')
template.file('templateDetails.xml')
template.directory('media/')
template.directory('favicon/')
template.directory('fields/')
template.directory('helper/')
template.directory('html/')
template.directory('src/')
template.directory('language/')
plugin.directory('sampledata/')

// Finalise the template (ie we are done appending files but streams have to finish yet)
template.finalize()

// Finalise the plugin (ie we are done appending files but streams have to finish yet)
plugin.finalize()
