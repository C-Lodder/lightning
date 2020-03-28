const { createWriteStream } = require('fs')
const archiver = require('archiver')

const output = createWriteStream(`${__dirname}/lightning.zip`)

const archive = archiver('zip', {
  zlib: { level: 9 }
})

// Listen for all archive data to be written
output.on('close', () => {
  console.log(`Template has been packaged successfully`)
})

// Catch this error explicitly
archive.on('error', (err) => {
  throw err
})
 
// Pipe archive data to the file
archive.pipe(output)

// Append the files and directories
archive.file('component.php')
archive.file('error.php')
archive.file('favicon.ico')
archive.file('index.php')
archive.file('offline.php')
archive.file('template_preview.png')
archive.file('template_thumbnail.png')
archive.file('templateDetails.xml')
archive.directory('css/')
archive.directory('html/')
archive.directory('js/')
archive.directory('language/')

// Finalise the archive (ie we are done appending files but streams have to finish yet)
archive.finalize()
