'use strict';
var webpack = require('webpack');
module.exports = {

    // Entry points for the application
    entry : {
            },

    output: {
        filename: "[name].dist.js",
        chunkFilename: "[id].dist.js",

        // The base directory (absolute path) for resolving
        // the entry option.
        path: '',
        publicPath: 'js/'
    },

    resolve: {
        alias: []
    },

    module: {
        loaders: [
            // Disable AMD @todo To check.
            {
                test: /bootstrap/,
                loader: "imports-loader?$=jquery"
            }
        ]
    },

    plugins: [
        new webpack.optimize.DedupePlugin(),
        new webpack.optimize.CommonsChunkPlugin({
            "name": "commons.js",
        }),
        //new webpack.optimize.UglifyJsPlugin()
    ]
};