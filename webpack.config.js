const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[hash:8].[ext]',
    })
    .addEntry('app', './assets/app.js')
    .enablePostCssLoader((options) => {
        options.postcssOptions = {
            config: './postcss.config.js'
        };
    })
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild()
    .enableSingleRuntimeChunk()
    .enableBuildNotifications()
    .enableVersioning(Encore.isProduction());

module.exports = Encore.getWebpackConfig();
