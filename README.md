# Binteva WordPress website

Custom enterprise marketing website for Binteva, a commercial opportunity intelligence platform in development.

- Theme: Binteva Premium 1.1.1
- Plugin: Binteva Conversion Suite 1.1.1
- Repository: https://github.com/stevzfs/Binteva
- Target WordPress: https://bisque-jackal-232127.hostingersite.com/

## Status

Binteva Premium and Conversion Suite 1.1.1 are installed and active on the target WordPress site as of October 21, 2026. The homepage and all eight interior pages were checked in Cloud Browser. Phone and tablet previews were checked, including menu open/Escape dismissal and horizontal overflow. A pre-installation database/content backup remains available. GitHub Actions PHP/JavaScript validation passed. Automated SSH deployment, inquiry email delivery, off-site backup recovery and final legal details remain incomplete. Deployment stays disabled until the verified server configuration and production gate exist.

## Source layout

The theme owns presentation, layouts and frontend assets. The plugin owns page provisioning, business settings and inquiry processing. Do not commit WordPress core, media uploads, private inquiries, databases or credentials.

See [installation](docs/installation.md), [deployment](docs/deployment.md), [architecture](docs/architecture.md) and [verification status](docs/status.md).

## Updates

Create a branch, change the theme/plugin, open a pull request and pass the validation workflow. After hosting setup, configure a production environment requiring review and restrict deployment to `main`. Merges to `main` then deploy only these custom directories after validation. Until the production gate is configured, keep `HOSTINGER_DEPLOY_ENABLED` unset.
