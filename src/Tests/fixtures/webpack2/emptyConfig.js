'use strict';

var webpack = require('webpack');
var duplicatePackageCheckerWebpackPlugin = require('duplicate-package-checker-webpack-plugin');
var webpack2PolyfillPlugin = require('webpack2-polyfill-plugin');
var webpackBundleAnalyzer = require('webpack-bundle-analyzer');
const outputConfig = {
    "filename": "[name].dist.js",
    "chunkFilename": "[id].dist.js",
    "path": "",
    "publicPath": "/"
};

const entriesConfig = {};

const aliasConfig = {};

const loadersConfig = [];

const pluginsConfig = [];
pluginsConfig.push(new webpack.optimize.CommonsChunkPlugin(null));
pluginsConfig.push(new webpack.optimize.UglifyJsPlugin({
    "sourceMap": true
}));
pluginsConfig.push(new webpack.optimize.MinChunkSizePlugin({
    "minChunkSize": 200000
}));
pluginsConfig.push(new duplicatePackageCheckerWebpackPlugin([]));
pluginsConfig.push(new webpack2PolyfillPlugin([]));
pluginsConfig.push(new webpack.ProvidePlugin({
    "Promise": "es6-promise"
}));

module.exports = {
    entry: entriesConfig,
    output: outputConfig,
    resolve: {
        alias: aliasConfig
    },
    module: {
        loaders: loadersConfig
    },
    plugins: pluginsConfig
};