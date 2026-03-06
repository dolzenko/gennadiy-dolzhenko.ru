#!/usr/bin/env bash
set -euo pipefail

readonly REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
readonly OUT_DIR="${REPO_ROOT}/.pages-dist"
readonly PROJECT_NAME="${PAGES_PROJECT_NAME:?set PAGES_PROJECT_NAME}"
readonly BRANCH_NAME="${PAGES_BRANCH_NAME:-master}"

test -d "${OUT_DIR}" || bash "${REPO_ROOT}/scripts/build-pages.sh"

npx wrangler pages deploy "${OUT_DIR}" \
  --project-name="${PROJECT_NAME}" \
  --branch="${BRANCH_NAME}" \
  --commit-dirty=true
