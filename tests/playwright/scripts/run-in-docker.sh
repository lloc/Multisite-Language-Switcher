#!/usr/bin/env bash
# Run the Playwright suite inside the official Playwright Linux image so visual
# snapshots are byte-stable across developer machines and CI. wp-env stays
# running on the host; the container reaches it via host.docker.internal.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
PW_VERSION="$(node -e "console.log(require('${REPO_ROOT}/node_modules/@playwright/test/package.json').version)")"
IMAGE="mcr.microsoft.com/playwright:v${PW_VERSION}-jammy"

# wp-env hardcodes its siteurl to http://localhost:8889 — every 302 redirect
# and absolute URL in the response uses that literal hostname. Host networking
# is the cleanest way to make `localhost` inside the container resolve to the
# host's localhost (Docker Desktop 4.34+ supports this on macOS).
NETWORK_ARGS=(--network host)

WP_BASE_URL="${WP_BASE_URL:-http://localhost:8889}"

echo "Running Playwright ${PW_VERSION} in ${IMAGE} against ${WP_BASE_URL}"
echo "Args: $*"

# Use -t only when invoked from a real terminal; the harness/CI can't allocate one.
TTY_FLAG=""
if [ -t 0 ] && [ -t 1 ]; then
  TTY_FLAG="-it"
fi

docker run --rm $TTY_FLAG \
  "${NETWORK_ARGS[@]}" \
  -v "${REPO_ROOT}:/work" \
  -w /work \
  -e CI \
  -e WP_BASE_URL="${WP_BASE_URL}" \
  -e MSLS_SKIP_E2E_SEED=1 \
  "${IMAGE}" \
  npx playwright test "$@"
