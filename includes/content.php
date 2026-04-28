<?php

const CONTENT_FILE = __DIR__ . '/../data/content.json';

function lecani_content(): array
{
    static $content = null;

    if ($content !== null) {
        return $content;
    }

    if (!is_file(CONTENT_FILE)) {
        return [];
    }

    $json = file_get_contents(CONTENT_FILE);
    $content = json_decode($json, true);

    return is_array($content) ? $content : [];
}

function lecani_save_content(array $content): bool
{
    $json = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    return file_put_contents(CONTENT_FILE, $json . PHP_EOL, LOCK_EX) !== false;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function lecani_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = strtr($value, ['å' => 'a', 'ä' => 'a', 'ö' => 'o']);
    $value = preg_replace('/[^a-z0-9]+/u', '-', $value);
    $value = trim((string) $value, '-');

    return $value !== '' ? $value : 'entry-' . time();
}

function lecani_enabled(array $item): bool
{
    return !array_key_exists('enabled', $item) || (bool) $item['enabled'];
}

function lecani_thumbnail_url(string $imageUrl): string
{
    $imageUrl = trim(str_replace('\\', '/', $imageUrl), '/');
    if (!str_starts_with($imageUrl, 'img/')) {
        return $imageUrl;
    }

    $relative = substr($imageUrl, 4);
    $thumbnail = 'img/' . dirname($relative) . '/thumbs/' . basename($relative);
    if (is_file(__DIR__ . '/../' . $thumbnail)) {
        return $thumbnail;
    }

    $legacyThumbnail = preg_replace('/\.(jpe?g|png|gif|webp)$/i', '_480.$1', $imageUrl);
    if (is_string($legacyThumbnail) && is_file(__DIR__ . '/../' . $legacyThumbnail)) {
        return $legacyThumbnail;
    }

    return $imageUrl;
}

function lecani_paragraphs(string $text): string
{
    $text = trim($text);
    if ($text === '') {
        return '';
    }

    if (preg_match('/<\s*(p|ul|ol|h[1-6]|div|blockquote)\b/i', $text)) {
        return $text;
    }

    $paragraphs = preg_split('/\R{2,}/', $text) ?: [];
    return implode("\n", array_map(static fn($paragraph) => '<p>' . nl2br(e(trim($paragraph))) . '</p>', $paragraphs));
}

function render_place_entry(array $item, string $kind): void
{
    if (!lecani_enabled($item)) {
        return;
    }

    $id = (string) ($item['id'] ?? lecani_slug($item['title'] ?? $kind));
    $title = (string) ($item['title'] ?? $id);
    $folder = trim((string) ($item['imageFolder'] ?? $kind . '/' . $id), '/');
    $thumbnail = (string) ($item['thumbnail'] ?? 'img/' . $folder . '/1.jpg');
    $displayThumbnail = lecani_thumbnail_url($thumbnail);
    $attributes = is_array($item['attributes'] ?? null) ? $item['attributes'] : [];
    $description = lecani_paragraphs((string) ($item['descriptionHtml'] ?? ''));
    ?>
    <div id="<?= e($kind . '-' . $id) ?>" class="city-container">
      <h2 class="city-title"><?= e($title) ?></h2>

      <ul class="city-attributes">
        <?php foreach ($attributes as $attribute): ?>
          <li><?= e((string) $attribute) ?></li>
        <?php endforeach; ?>
      </ul>

      <div class="city-collapsed">
        <div class="city-collapsed-info"></div>
        <div class="city-thumbnail">
          <img src="<?= e($displayThumbnail) ?>" alt="<?= e($title) ?>" />
        </div>
      </div>

      <div class="city-expanded">
        <div class="city-expanded-content">
          <div class="slideshow-container" id="<?= e($id . '-slideshow') ?>" data-image-folder="<?= e($folder) ?>">
            <div class="main-image">
              <a href="<?= e($thumbnail) ?>"><img id="<?= e($id . '-slideshow_currentImage') ?>" src="<?= e($displayThumbnail) ?>" alt="<?= e($title) ?>" /></a>
            </div>
            <button class="prev">&#10094;</button>
            <button class="next">&#10095;</button>
          </div>

          <div class="city-expanded-layout">
            <div class="city-expanded-attributes-container"></div>
            <div class="city-expanded-description">
              <?= $description ?>
            </div>
          </div>
        </div>
      </div>

      <button class="city-toggle-btn">Expandera &#9660;</button>
    </div>
    <?php
}

function render_cities_section(): void
{
    $content = lecani_content();
    ?>
    <section class="content-section" id="section-cities">
      <div class="content-container">
        <h1>Städer</h1>
        <?php foreach (($content['cities'] ?? []) as $city): ?>
          <?php render_place_entry($city, 'city'); ?>
        <?php endforeach; ?>
      </div>
    </section>
    <?php
}

function render_monuments_section(): void
{
    $content = lecani_content();
    ?>
    <section class="content-section" id="section-monuments">
      <div class="content-container">
        <h1>Monument</h1>
        <?php foreach (($content['monuments'] ?? []) as $monument): ?>
          <?php render_place_entry($monument, 'monument'); ?>
        <?php endforeach; ?>
      </div>
    </section>
    <?php
}

function render_emblems_section(): void
{
    $content = lecani_content();
    ?>
    <section class="content-section" id="section-emblem">
      <div class="content-container">
        <h1>LeCaNis Stadsvapen</h1>
        <p>Vissa städer på LeCaNi har ett 4x4-pixlar emblem, eller ett "stadsvapen". Några av dessa märken har en längre historia än staden själv, så jag kände att de kunde få en egen sida här på hemsidan.</p>
        <?php foreach (($content['emblems'] ?? []) as $emblem): ?>
          <?php if (!lecani_enabled($emblem)) continue; ?>
          <div class="vapen">
            <h2><?= e((string) ($emblem['title'] ?? '')) ?></h2>
            <img class="flag" src="<?= e((string) ($emblem['image'] ?? '')) ?>" alt="<?= e((string) ($emblem['title'] ?? '')) ?>">
            <?= lecani_paragraphs((string) ($emblem['descriptionHtml'] ?? '')) ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php
}

function render_news_section(): void
{
    $content = lecani_content();
    $news = $content['news'] ?? [];
    ?>
    <section class="content-section" id="section-news">
      <div class="content-container">
        <h1>Nyheter</h1>
        <div id="news">
          <?= lecani_paragraphs((string) ($news['introHtml'] ?? '')) ?>
          <?php foreach (($news['items'] ?? []) as $item): ?>
            <?php if (!lecani_enabled($item)) continue; ?>
            <h2><?= e((string) ($item['title'] ?? '')) ?></h2>
            <h3><?= e((string) ($item['date'] ?? '')) ?></h3>
            <?php foreach (($item['images'] ?? []) as $image): ?>
              <img src="<?= e((string) $image) ?>" alt="<?= e((string) ($item['title'] ?? '')) ?>" />
            <?php endforeach; ?>
            <?= lecani_paragraphs((string) ($item['bodyHtml'] ?? '')) ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php
}

function render_rules_section(): void
{
    $content = lecani_content();
    $rules = $content['rules'] ?? [];
    ?>
    <section class="content-section" id="section-rules">
      <div class="content-container">
        <h1>Regler</h1>
        <div class="rule-list">
          <?php foreach (($rules['items'] ?? []) as $rule): ?>
            <?php if (!lecani_enabled($rule)) continue; ?>
            <div class="rule-item">
              <div class="rule-title"><span class="arrow">&#10148;</span> <?= e((string) ($rule['title'] ?? '')) ?></div>
              <div class="rule-description"><?= nl2br(e((string) ($rule['description'] ?? ''))) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
        <?= (string) ($rules['extraHtml'] ?? '') ?>
      </div>
    </section>
    <?php
}

function render_download_section(): void
{
    $content = lecani_content();
    $downloads = $content['downloads'] ?? [];
    ?>
    <section class="content-section" id="section-download">
      <div class="content-container">
        <h1>Ladda ner</h1>
        <p>Här kan du ladda ner texturpaket och annat som kan vara användbart för att spela på LeCaNi</p>

        <div class="download-container">
          <h2>Texture Packs</h2>
          <?php foreach (($downloads['textures'] ?? []) as $texture): ?>
            <?php if (!lecani_enabled($texture)) continue; ?>
            <div class="download-texture">
              <img src="<?= e((string) ($texture['image'] ?? '')) ?>" alt="<?= e((string) ($texture['title'] ?? '')) ?>" />
              <h3><?= e((string) ($texture['title'] ?? '')) ?></h3>
              <p>Version: <?= e((string) ($texture['version'] ?? '')) ?><br />
              <a href="<?= e((string) ($texture['download'] ?? '')) ?>">Ladda Ner</a></p>
              <p><?= e((string) ($texture['description'] ?? '')) ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="download-container">
          <h2>LeCaNis Skrivbordsbakgrunder</h2>
          <p>Vi har tagit fram en samling fina bilder, med högkvalitativa shaders och lång render distance från servern som är perfekta att använda som skrivbordsbakgrunder eller liknande! Du har redan sett några som bakgrunder på de olika sektionerna av den här hemsidan. Här kan du ladda ner allihop eller en åt gången!</p>
          <?php foreach (($downloads['wallpapers'] ?? []) as $wallpaper): ?>
            <?php if (!lecani_enabled($wallpaper)) continue; ?>
            <div class="download-wallpaper">
              <a href="<?= e((string) ($wallpaper['fullImage'] ?? '')) ?>"><img src="<?= e((string) ($wallpaper['image'] ?? '')) ?>" alt="<?= e((string) ($wallpaper['caption'] ?? '')) ?>" /></a>
              <p><?= e((string) ($wallpaper['caption'] ?? '')) ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="download-container">
          <p>Du kan klicka på bilderna för att se och ladda ner fullstora versioner av dem, eller <a href="dl/lecani_wallpapers.zip">ladda ner alla skrivbordsbakgrunder som zip-fil</a>.</p>
        </div>

        <div class="download-container">
          <h2>LeCaNis värld</h2>
          <p>Du kan ladda ner hela LeCaNi-världen, och inte bara det, utan välja vilket år du vill ladda ner! Vi har en samling av gamla backups som vem som helst får ladda ner! Du kan använda världen som en bobby-fallback när du spelar på LeCaNi eller för att gå tillbaka i tiden och utforska din stad när den var ny, eller till och med för att starta din egen server med LeCaNis värld som bas! Om du använder LeCaNis värld i externa sammanhang måste du vara tydlig med att det är LeCaNis värld! Du får inte påstå att du byggt något du inte byggt, till exempel!</p>
          <p><a href="http://lecani.se:5000/fsdownload/ogC2YfnFw/LeCaNi%20genom%20tiderna">Se alla versioner av LeCaNis värld</a>!</p>
        </div>
      </div>
    </section>
    <?php
}
