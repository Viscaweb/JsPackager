'use strict';
// Require modules

const outputConfig = {
    filename: '[name].dist.js',
    chunkFilename: '[id].dist.js',
    path: '',
    publicPath: '/'
};

const entriesConfig = {};

const aliasConfig = {};

const loadersConfig = [];

const pluginsConfig = [];

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