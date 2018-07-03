<script src="/bundles/app/js/common/requirejs.js"></script>
<script>requirejs.config({
    "paths": {
        "bootstrap": "/js/bootstrap.min.js"
    },
    "shim": {
        "bootstrap": {
            "deps": [
                "jquery"
            ]
        }
    },
    "waitSeconds": 15
});
</script>