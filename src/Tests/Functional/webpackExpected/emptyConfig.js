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
        path: __dirname + ''
    },

    resolve: {
        alias: []
    },

    module :{
        loaders: [
            // Disable AMD @todo To check.
            { test: /\.js/, loader: 'imports?define=>false'},
            {
                test: /bootstrap/,
                loader: "imports?$=jquery"
            },
        ]
    },

    plugins : [
        new webpack.optimize.DedupePlugin(),
        new webpack.optimize.CommonsChunkPlugin("commons.js", ['jquery']),
        //	new webpack.optimize.UglifyJsPlugin()
    ]
};