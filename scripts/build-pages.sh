#!/usr/bin/env bash
set -euo pipefail

readonly REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
readonly OUT_DIR="${REPO_ROOT}/.pages-dist"
readonly SITE_HOST="xn----7sbifbcehpkpjtajmf.xn--p1ai"
readonly SITE_URL="https://${SITE_HOST}"

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

canonical_url_for() {
  local rel_path="$1"

  if [[ "${rel_path}" == "index.html" ]]; then
    printf '%s/\n' "${SITE_URL}"
    return
  fi

  if [[ "${rel_path}" == */index.html ]]; then
    printf '%s/%s/\n' "${SITE_URL}" "${rel_path%/index.html}"
    return
  fi

  printf '%s/%s\n' "${SITE_URL}" "${rel_path}"
}

postprocess_html_file() {
  local file_path="$1"
  local rel_path="${file_path#"${OUT_DIR}/"}"
  local canonical_url
  canonical_url="$(canonical_url_for "${rel_path}")"

  CANONICAL_URL="${canonical_url}" perl -0pi -e '
    if (/<html\b/i) {
      s{<html\b([^>]*)>}{
        my $attrs = $1;
        $attrs =~ s/\s+xml:lang\s*=\s*(["\047]).*?\1//i;
        $attrs =~ s/\s+lang\s*=\s*(["\047]).*?\1//i;
        qq{<html$attrs xml:lang="ru" lang="ru">};
      }eis;
    }

    if (/<head\b/i && /<\/head>/i && !/<link\s+rel=["\047]canonical["\047]/i) {
      s{</head>}
       {\t<link rel="canonical" href="$ENV{CANONICAL_URL}" />\n</head>}i;
    }
  ' "${file_path}"
}

generate_robots_txt() {
  cat > "${OUT_DIR}/robots.txt" <<EOF
User-agent: *
Allow: /

Sitemap: ${SITE_URL}/sitemap.xml
EOF
}

is_full_html_document() {
  local file_path="$1"
  grep -qi '<html\b' "${file_path}" && grep -qi '</head>' "${file_path}"
}

generate_sitemap() {
  local file_path rel_path canonical_url lastmod

  {
    printf '%s\n' '<?xml version="1.0" encoding="UTF-8"?>'
    printf '%s\n' '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'

    while IFS= read -r -d '' file_path; do
      if ! is_full_html_document "${file_path}"; then
        continue
      fi

      rel_path="${file_path#"${OUT_DIR}/"}"
      canonical_url="$(canonical_url_for "${rel_path}")"
      lastmod="$(date -u -r "${file_path}" +%F)"
      printf '  <url><loc>%s</loc><lastmod>%s</lastmod></url>\n' "${canonical_url}" "${lastmod}"
    done < <(find "${OUT_DIR}" -type f \( -name '*.html' -o -name '*.htm' \) -print0 | sort -z)

    printf '%s\n' '</urlset>'
  } > "${OUT_DIR}/sitemap.xml"
}

while IFS= read -r -d '' html_file; do
  if ! is_full_html_document "${html_file}"; then
    continue
  fi

  postprocess_html_file "${html_file}"
done < <(find "${OUT_DIR}" -type f \( -name '*.html' -o -name '*.htm' \) -print0)

generate_robots_txt
generate_sitemap
