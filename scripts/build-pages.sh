#!/usr/bin/env bash
set -euo pipefail

readonly REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
readonly OUT_DIR="${REPO_ROOT}/.pages-dist"

rm -rf "${OUT_DIR}"
mkdir -p "${OUT_DIR}"

rsync -a \
  --exclude '.git/' \
  --exclude '.gitignore' \
  --exclude '.pages-dist/' \
  --exclude 'README.md' \
  --exclude '.ruby-version' \
  --exclude 'Capfile' \
  --exclude 'Rakefile' \
  --exclude 'do_menu.rb' \
  --exclude 'works.rb' \
  --exclude 'task' \
  --exclude 'config/' \
  --exclude 'guestbook/' \
  --exclude 'scripts/' \
  --exclude 'tmp/' \
  --exclude 'materials/' \
  --exclude 'tour_guide_training/' \
  --exclude '*.rb' \
  --exclude '*.erb' \
  --exclude '*.erb.html' \
  --exclude '*.deleted' \
  --exclude 'Thumbs.db' \
  --exclude '*/Thumbs.db' \
  "${REPO_ROOT}/" "${OUT_DIR}/"

test -f "${OUT_DIR}/index.html"
