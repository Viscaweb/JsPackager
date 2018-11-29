const webpack = require('webpack');

module.exports = {
    entry: require('./entry-points-desktop'),
    output: {
        "filename": "[name].dist.js",
        "chunkFilename": "[id].dist.js",
        "path": "",
        "publicPath": "/"
    },
    resolve: {
        alias: require('./aliases-desktop')
    },
    plugins: require('./plugins-desktop'),
    devtool: 'source-map'
};