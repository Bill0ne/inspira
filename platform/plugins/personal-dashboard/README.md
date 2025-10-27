# Personal Dashboard Plugin

## Widget asset publishing

Personal dashboard widgets depend on the compiled JavaScript loader located at
`public/vendor/core/plugins/personal-dashboard/js/personal-dashboard.js`.

If widgets render with empty bodies, publish the plugin assets so the loader is
available to the dashboard client:

```bash
php artisan vendor:publish --tag=cms-public --force
```

Re-run the command after deployments or when updating the plugin to ensure the
latest loader script is copied into the public asset directory.
