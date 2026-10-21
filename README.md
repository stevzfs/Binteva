# Binteva WordPress website

Custom enterprise marketing website for Binteva, a commercial opportunity intelligence platform in development.

- Theme: Binteva Premium 1.1.0
- Plugin: Binteva Conversion Suite 1.1.0
- Repository: https://github.com/stevzfs/Binteva
- Target WordPress: https://bisque-jackal-232127.hostingersite.com/

## Status

Repository write access verified on October 21, 2026. Source is recovered and revised from the original packages. WordPress administrator access and a pre-installation database/content backup are verified. Hosting deployment, email delivery, off-site backup recovery and responsive visual QA are not verified yet. The workflow fails closed: deployment is disabled until verified server configuration and the explicit enable variable exist.

## Source layout

The theme owns presentation, layouts and frontend assets. The plugin owns page provisioning, business settings and inquiry processing. Do not commit WordPress core, media uploads, private inquiries, databases or credentials.

See [installation](docs/installation.md), [deployment](docs/deployment.md), [architecture](docs/architecture.md) and [verification status](docs/status.md).

## Updates

Create a branch, change the theme/plugin, open a pull request and pass the validation workflow. After hosting setup, configure a production environment requiring review and restrict deployment to `main`. Merges to `main` then deploy only these custom directories after validation. Until the production gate is configured, keep `HOSTINGER_DEPLOY_ENABLED` unset.
