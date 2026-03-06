#!/usr/bin/env bash
set -euo pipefail

FTP_HOST="${FTP_HOST:-31.31.198.144}"
FTP_USER="${FTP_USER:-u3440634}"
FTP_PASSWORD="${FTP_PASSWORD:?set FTP_PASSWORD}"

readonly REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
readonly REMOTE_ROOTS=(
  "www/xn----7sbifbcehpkpjtajmf.xn--p1ai"
  "www/геннадий-долженко.рф"
)

should_upload() {
  local path="$1"

  case "$path" in
    .git/*|.gitignore|.ruby-version|Capfile|Rakefile|do_menu.rb|works.rb|task|config/*|scripts/*|tmp/*)
      return 1
      ;;
    guestbook/*|*.rb|*.erb|*.erb.html|*.deleted|Thumbs.db|*/Thumbs.db)
      return 1
      ;;
  esac

  return 0
}

upload_one() {
  local remote_root="$1"
  local relative_path="$2"
  local encoded_path

  encoded_path="$(python3 -c 'import sys, urllib.parse; print(urllib.parse.quote(sys.argv[1]))' "$relative_path")"

  curl -k --silent --show-error --ftp-create-dirs \
    --user "${FTP_USER}:${FTP_PASSWORD}" \
    --upload-file "${REPO_ROOT}/${relative_path}" \
    "ftp://${FTP_HOST}/${remote_root}/${encoded_path}"
}

main() {
  cd "${REPO_ROOT}"

  mapfile -d '' files < <(find . -type f -print0)
  for file in "${files[@]}"; do
    file="${file#./}"
    should_upload "$file" || continue

    for remote_root in "${REMOTE_ROOTS[@]}"; do
      printf 'upload %s -> %s\n' "$file" "$remote_root"
      upload_one "$remote_root" "$file"
    done
  done
}

main "$@"
