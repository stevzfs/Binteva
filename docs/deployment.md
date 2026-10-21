# GitHub to Hostinger deployment

## Required verified connection

The hPanel sign-in route is currently restricted for this Cloud Browser. The server host, SSH port, username, actual WordPress directory and host-key fingerprint have not been obtained. Do not guess them. WordPress admin access alone does not establish SSH access.

Use a dedicated least-privilege deployment identity. Store these values in the GitHub production environment as secrets:

- HOSTINGER_SSH_HOST
- HOSTINGER_SSH_PORT
- HOSTINGER_SSH_USER
- HOSTINGER_SSH_PRIVATE_KEY
- HOSTINGER_SSH_KNOWN_HOSTS (full known_hosts line, verified against trusted provider information; include [host]:port for nonstandard ports)
- HOSTINGER_WP_CONTENT_PATH (verified absolute path ending /wp-content)

Create the `production` environment, restrict it to `main` and configure an approval reviewer where supported. Require validation before merging production PRs. Only then set repository variable HOSTINGER_DEPLOY_ENABLED=true. The deployment script has no default port and refuses an installation without wp-load.php in its parent.

## Behavior and limitations

Validation runs PHP syntax and JavaScript checks. Deployment runs after validation on main only when enabled, using strict SSH host-key verification. The script snapshots existing Binteva code into ~/.binteva-backups outside the web root, then synchronizes only the theme and companion plugin. No rsync deletion, core update, upload synchronization or database migration is performed. This code snapshot is not a full site backup.

Deployment is not atomic. A maintenance window or verified staging deployment is appropriate before first production use. Remote rsync, tar and bash must exist. A successful Actions run is not proof the public site works: check the live version, all pages, forms and screenshots after each release.

## Rollback

Before first deployment, establish provider-level full database/files backups and test restore availability. To roll back code, redeploy a previously verified commit or restore the corresponding custom-code archive using the verified SSH account and directory. Restore both theme and plugin together. Restore database/pages separately from the full backup only if necessary and explicitly authorized; code rollback alone does not revert page edits or settings.

On activation the plugin retains prior homepage settings in binteva_previous_site_settings. The prior theme remains installed, enabling an admin to switch back if required. Keep credential rotation and recovery instructions in the provider secret manager, never this repository.
