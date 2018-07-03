'use strict';

const outputConfig = {
    "filename": "[name].dist.js",
    "chunkFilename": "[id].dist.js",
    "path": "",
    "publicPath": "js/"
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