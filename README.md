# gennadiy-dolzhenko.ru

Статическая версия сайта для деплоя в Cloudflare Pages.

Важно: Cloudflare Pages в этой схеме будет обслуживать только статику. `guestbook/`, PHP и MySQL на Pages не поедут, их надо либо отключить, либо выносить отдельно на Workers/D1/другой backend.

## Локальная сборка

```bash
bash ./scripts/build-pages.sh
```

После этого готовый каталог публикации будет в `.pages-dist/`.

## Настройка Cloudflare Pages через GitHub

Создай Pages project через GitHub integration для этого репозитория и укажи:

- Production branch: `master`
- Framework preset: `None`
- Build command: `bash ./scripts/build-pages.sh`
- Build output directory: `.pages-dist`
- Root directory: `/`

Если Cloudflare dashboard позволит оставить Build command пустым, это тоже допустимо только если выбрать output directory равным корню репозитория, но для этого проекта так делать не надо: в корне лежат PHP и служебные файлы. Поэтому нужен отдельный build step в `.pages-dist`.

## Настройка Cloudflare Pages без GitHub

Если не хочешь держать рабочий доступ к GitHub с этой машины, для этого проекта лучше использовать Direct Upload через Wrangler.

1. В Cloudflare создай Pages project как `Direct Upload`.
2. Задай имя проекта, например `gennadiy-dolzhenko-ru`.
3. Локально выполни:

```bash
bash ./scripts/build-pages.sh
npx wrangler login
PAGES_PROJECT_NAME=gennadiy-dolzhenko-ru bash ./scripts/deploy-pages-direct.sh
```

Это загрузит `.pages-dist/` напрямую в Cloudflare Pages без GitHub integration.

## Домен

По документации Cloudflare Pages:

- для apex-домена `gennadiy-dolzhenko.ru` зона должна быть заведена в Cloudflare и NS нужно перевести на Cloudflare;
- для поддомена можно обойтись CNAME на `*.pages.dev`, но для основного домена это не подходит.

После первого деплоя:

1. Добавь домен `gennadiy-dolzhenko.ru` в `Workers & Pages -> <project> -> Custom domains`.
2. Если нужен `www.gennadiy-dolzhenko.ru`, добавь и его тоже.
3. Если хочешь, чтобы `*.pages.dev` не светился, настрой redirect на custom domain.

## Что публикуется

В `.pages-dist/` попадает только статика:

- HTML
- CSS
- JS
- изображения
- PDF/DOC и прочие статические ассеты

Исключаются:

- `guestbook/`
- ruby-скрипты и capistrano-файлы
- шаблоны `*.erb*`
- временные каталоги
