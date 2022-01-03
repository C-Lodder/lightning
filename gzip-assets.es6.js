/**
 * For creating Brotli files you need to install iltorb
 * and import it like:
 * const { compressStream } = require('iltorb');
 */
const Fs       = require("fs");
const {gzip}   = require("@gfx/zopfli");
const walkSync = require("walk-sync");

const RootPath         = process.cwd();
const {compressStream} = require("iltorb");

const options = {
    verbose: false,
    verbose_more: false,
    numiterations: 15,
    blocksplitting: true,
    blocksplittingmax: 15,
};

/**
 * Method that will create a gzipped vestion of the given file
 *
 * @param   { string }  file  The path of the file
 *
 * @returns { void }
 */
const compressFile = (file, enableBrotli) => {
    if (file.match(/\.js/) && !file.match(/\.js\.gz/) && !file.match(/\.js\.br/)) {
        // eslint-disable-next-line no-console
        console.log(`Compressing JS: ${file}`);

        if (enableBrotli && compressStream) {
            // Brotli file
            Fs.createReadStream(file)
              .pipe(compressStream())
              .pipe(Fs.createWriteStream(file.replace(/\.js$/, '.js.br')));
        }

        // Gzip the file
        Fs.readFile(file, (err, data) => {
            if (err) throw err;
            gzip(data, options, (error, output) => {
                if (error) throw err;
                // Save the gzipped file
                Fs.writeFileSync(
                    file.replace(/\.js$/, '.js.gz'),
                    output,
                    { encoding: 'utf8' },
                );
            });
        });
    }

    if (file.match(/\.css/) && !file.match(/\.css\.gz/) && !file.match(/\.css\.br/)) {
        // eslint-disable-next-line no-console
        console.log(`Compressing CSS: ${file}`);

        if (enableBrotli && compressStream) {
            // Brotli file
            Fs.createReadStream(file)
              .pipe(compressStream())
              .pipe(Fs.createWriteStream(file.replace(/\.css$/, '.css.br')));
        }

        // Gzip the file
        Fs.readFile(file, (err, data) => {
            if (err) throw err;
            gzip(data, options, (error, output) => {
                if (error) throw err;
                // Save the gzipped file
                Fs.writeFileSync(
                    file.replace(/\.css$/, '.css.gz'),
                    output,
                    { encoding: 'utf8' },
                );
            });
        });
    }
};

module.exports.compressFile = compressFile;