#!/usr/bin/env bash
set -euo pipefail
# All connection details must be verified before enabling this workflow.
for name in SSH_HOST SSH_PORT SSH_USER SSH_KEY KNOWN_HOSTS WP_CONTENT_PATH; do
  test -n "${!name:-}" || { echo "Missing deployment configuration: $name" >&2; exit 1; }
done
[[ "$SSH_HOST" =~ ^[a-zA-Z0-9][a-zA-Z0-9.-]+$ ]]
[[ "$SSH_USER" =~ ^[a-zA-Z0-9_][a-zA-Z0-9_-]*$ ]]
[[ "$SSH_PORT" =~ ^[0-9]{1,5}$ ]] && ((10#$SSH_PORT > 0 && 10#$SSH_PORT < 65536))
[[ "$WP_CONTENT_PATH" =~ ^/[a-zA-Z0-9_./-]+/wp-content$ ]]
[[ "$WP_CONTENT_PATH" != *..* ]]
install -d -m 700 "$HOME/.ssh"
trap 'rm -f "$HOME/.ssh/binteva_deploy"' EXIT
printf '%s\n' "$SSH_KEY" > "$HOME/.ssh/binteva_deploy"
printf '%s\n' "$KNOWN_HOSTS" > "$HOME/.ssh/known_hosts"
chmod 600 "$HOME/.ssh/binteva_deploy" "$HOME/.ssh/known_hosts"
unset SSH_KEY KNOWN_HOSTS
ssh_options=(-i "$HOME/.ssh/binteva_deploy" -p "$SSH_PORT" -o BatchMode=yes -o IdentitiesOnly=yes -o StrictHostKeyChecking=yes)
# Refuse a guessed directory. Snapshot custom code outside the web root first.
ssh "${ssh_options[@]}" "$SSH_USER@$SSH_HOST" "bash -s -- '$WP_CONTENT_PATH'" <<'REMOTE'
set -euo pipefail
content="$1"
test -d "$content/themes" && test -d "$content/plugins"
test -f "$content/../wp-load.php"
install -d -m 700 "$HOME/.binteva-backups"
stamp="$(date -u +%Y%m%dT%H%M%SZ)-$$"
paths=()
for path in themes/binteva-premium plugins/binteva-conversion-suite; do
  if test -d "$content/$path"; then paths+=("$path"); fi
done
if ((${#paths[@]})); then
  umask 077
  tar -czf "$HOME/.binteva-backups/$stamp.tar.gz" -C "$content" "${paths[@]}"
fi
mkdir -p "$content/themes/binteva-premium" "$content/plugins/binteva-conversion-suite"
REMOTE
# Deliberately no --delete: core, uploads and unrelated code are never synchronized.
transport="ssh -i $HOME/.ssh/binteva_deploy -p $SSH_PORT -o BatchMode=yes -o IdentitiesOnly=yes -o StrictHostKeyChecking=yes"
rsync -rz --chmod=D755,F644 -e "$transport" wordpress/wp-content/themes/binteva-premium/ "$SSH_USER@$SSH_HOST:$WP_CONTENT_PATH/themes/binteva-premium/"
rsync -rz --chmod=D755,F644 -e "$transport" wordpress/wp-content/plugins/binteva-conversion-suite/ "$SSH_USER@$SSH_HOST:$WP_CONTENT_PATH/plugins/binteva-conversion-suite/"
