var Encore = require('@symfony/webpack-encore');

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
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('login', './assets/js/login.js')
    .addEntry('unidad', './assets/js/unidad.js')
    .addEntry('listados', './assets/js/listados.js')
    .addEntry('activar_libro', './assets/js/activar_libro.js')
    .addEntry('ajax_categoria', './assets/js/ajax_categoria.js')
    .addEntry('left_panel', './assets/js/left_panel.js')
    .addEntry('register', './assets/js/register.js')
    .addEntry('recursos', './assets/js/recursos.js')
    .addEntry('unidad_form', './assets/js/unidad_form.js')
    .addEntry('plataforma', './assets/js/plataforma.js')
    .addEntry('modal_recursos', './assets/js/modal_recursos.js')
    .addEntry('dashboard_searcher', './assets/js/dashboard_searcher.js')
    .addEntry('unidad_pdf_render', './assets/js/unidad_pdf_render.js')

    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

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

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    .configureFilenames({
        images: '[path][name].[hash:8].[ext]'
    })

    .copyFiles([
        {from: './assets/i18n', pattern: /\.json$/, to: '/assets/i18n/[path][name].[ext]'},
        // {from: './assets/pdf_report', to: 'pdf_report'},
        // {from: './assets/fonts', to: 'fonts1'},
    ])

    // enables Sass/SCSS support
    //.enableSassLoader()


    .addRule({
        test: /fos_js_routes.js/,
        use: [{
            loader: 'raw-loader',
        }]
    })

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
    .addExternals({
        fs: 'fs'
    })


// uncomment if you use API Platform Admin (composer req api-admin)
//.enableReactPreset()
//.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
