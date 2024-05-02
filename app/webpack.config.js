const Encore = require('@symfony/webpack-encore');
//const CKEditorWebpackPlugin = require( '@ckeditor/ckeditor5-dev-webpack-plugin' );
const {CKEditorTranslationsPlugin} = require('@ckeditor/ckeditor5-dev-translations');
const {styles: ckeditorstyles} = require('@ckeditor/ckeditor5-dev-utils');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')
    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('back_app', './assets/js/back/back.js')
    .addEntry('front_app', './assets/js/front/front.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()
    .configureSplitChunks(function (splitChunks) {
        splitChunks.chunks = 'all'; // Split all chunks including async ones
        splitChunks.minSize = 20000; // Minimum size for a chunk to be split
        splitChunks.maxSize = 50000; // Maximum size for a chunk to be split
        splitChunks.minChunks = 1; // Minimum number of chunks a module must be in to be split
        splitChunks.maxAsyncRequests = 30; // Maximum number of async requests at a time
        splitChunks.maxInitialRequests = 30; // Maximum number of initial requests at a time
        splitChunks.automaticNameDelimiter = '~'; // Delimiter for automatic chunk names
        splitChunks.enforceSizeThreshold = 50000; // Enforce chunk size threshold
        splitChunks.cacheGroups = { // Cache groups for splitting
            defaultVendors: {
                test: /[\\/]node_modules[\\/]/,
                priority: -10, // Priority for splitting
            },
            default: {
                minChunks: 2,
                priority: -20,
                reuseExistingChunk: true,
            },
        }
    })

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-transform-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    .copyFiles({
        from: './assets/vendor/tarteaucitronjs',
        to: 'tarteaucitron/[path][name].[ext]',
    })

    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]',
    })
    .copyFiles({
        from: './assets/policy',
        to: 'fonts/[path][name].[ext]',
    })

    .configureImageRule({
        // tell Webpack it should consider inlining
        type: 'asset',
        maxSize: 8 * 1024, // 4 kb - the default is 8kb
    })

    .configureFontRule({
        type: 'asset',
        maxSize: 8 * 1024
    })
    .addPlugin(new TerserPlugin({
        terserOptions: {
            compress: {
                // Adjust compression options as needed
                drop_console: Encore.isProduction(), // Drop console.* statements
                ecma: 2018, // Use ECMAScript 2018
                inline: 2, // Inline functions with arguments used < 2 times
                passes: 3, // Number of passes to optimize
            },
            mangle: true, // Enable variable and function name mangling
            format: {
                comments: false,
            },
        },
        extractComments: false,
        parallel: true, // Enable parallelization for faster minification
    }))
    .addPlugin(new CssMinimizerPlugin())
    .addPlugin(new CKEditorTranslationsPlugin({
        language: 'fr',
        additionalLanguages: ['en'],
        buildAllTranslationsToSeparateFiles: true
    }))

    // Use raw-loader for CKEditor 5 SVG files.
    .addLoader({
        test: /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/,
        loader: 'raw-loader'
    })

    // Configure other image loaders to exclude CKEditor 5 SVG files.
    .configureLoaderRule('images', loader => {
        loader.exclude = /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/;
    })

    // Configure PostCSS loader.
    .addLoader({
        test: /ckeditor5-[^/\\]+[/\\]theme[/\\].+\.css$/,
        loader: 'postcss-loader',
        options: {
            postcssOptions: ckeditorstyles.getPostCssConfig({
                themeImporter: {
                    themePath: require.resolve('@ckeditor/ckeditor5-theme-lark')
                }
            })
        }
    })

    // uncomment if you use TypeScript
    .enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
