<?php
session_start();

require_once __DIR__ . '/../includes/content.php';

const PASSWORD_FILE = __DIR__ . '/../data/admin-password.php';
const IMAGE_ROOT = __DIR__ . '/../img';
const UPLOAD_ROOT = 'uploads';
const THUMB_DIR = 'thumbs';
const THUMB_MAX_SIZE = 480;

$collections = [
    'cities' => 'Städer',
    'monuments' => 'Monument',
    'emblems' => 'Stadsvapen',
    'news' => 'Nyheter',
    'rules' => 'Regler',
    'textures' => 'Texturpaket',
    'wallpapers' => 'Skrivbordsbakgrunder',
    'settings' => 'Sidtexter',
];

function admin_password_hash(): string
{
    return is_file(PASSWORD_FILE) ? (string) require PASSWORD_FILE : '';
}

function is_admin(): bool
{
    return !empty($_SESSION['lecani_admin']);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['csrf'];
}

function require_csrf(): void
{
    if (!hash_equals((string) ($_SESSION['csrf'] ?? ''), (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Invalid CSRF token');
    }
}

function posted_lines(string $name): array
{
    $value = trim((string) ($_POST[$name] ?? ''));
    if ($value === '') {
        return [];
    }

    return array_values(array_filter(array_map('trim', preg_split('/\R/', $value) ?: []), static fn($line) => $line !== ''));
}

function normalize_enabled(): bool
{
    return !empty($_POST['enabled']);
}

function clean_filename(string $name): string
{
    $name = pathinfo($name, PATHINFO_FILENAME);
    $name = strtolower(trim($name));
    $name = strtr($name, ['å' => 'a', 'ä' => 'a', 'ö' => 'o']);
    $name = preg_replace('/[^a-z0-9_-]+/u', '-', $name);
    $name = trim((string) $name, '-');

    return $name !== '' ? $name : 'image';
}

function image_extension(string $tmpName): ?string
{
    $type = @exif_imagetype($tmpName);

    return match ($type) {
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_GIF => 'gif',
        IMAGETYPE_WEBP => 'webp',
        default => null,
    };
}

function safe_img_path(string $relativePath): ?string
{
    $relativePath = trim(str_replace('\\', '/', $relativePath), '/');
    if ($relativePath === '' || str_contains($relativePath, '..')) {
        return null;
    }

    if (str_starts_with($relativePath, 'img/')) {
        $relativePath = substr($relativePath, 4);
    }

    $fullPath = IMAGE_ROOT . '/' . $relativePath;
    $imageRoot = realpath(IMAGE_ROOT);
    $parent = realpath(dirname($fullPath));

    if ($imageRoot === false || $parent === false || !str_starts_with($parent, $imageRoot)) {
        return null;
    }

    return $fullPath;
}

function delete_image(string $relativePath): void
{
    $fullPath = safe_img_path($relativePath);
    if ($fullPath && is_file($fullPath)) {
        unlink($fullPath);
        delete_thumbnail_for($fullPath);
    }
}

function thumbnail_path_for(string $fullPath): string
{
    return dirname($fullPath) . '/' . THUMB_DIR . '/' . basename($fullPath);
}

function thumbnail_url_for(string $imageUrl): string
{
    $imageUrl = trim(str_replace('\\', '/', $imageUrl), '/');
    if (str_starts_with($imageUrl, 'img/')) {
        $imageUrl = substr($imageUrl, 4);
    }

    return 'img/' . dirname($imageUrl) . '/' . THUMB_DIR . '/' . basename($imageUrl);
}

function delete_thumbnail_for(string $fullPath): void
{
    $thumbPath = thumbnail_path_for($fullPath);
    if (is_file($thumbPath)) {
        unlink($thumbPath);
    }
}

function create_thumbnail(string $sourcePath): ?string
{
    if (!function_exists('imagecreatetruecolor') || !is_file($sourcePath)) {
        return null;
    }

    $info = @getimagesize($sourcePath);
    if (!$info) {
        return null;
    }

    [$width, $height] = $info;
    if ($width <= 0 || $height <= 0) {
        return null;
    }

    $scale = min(THUMB_MAX_SIZE / $width, THUMB_MAX_SIZE / $height, 1);
    $newWidth = max(1, (int) round($width * $scale));
    $newHeight = max(1, (int) round($height * $scale));

    $source = match ($info[2]) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
        IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
        IMAGETYPE_GIF => @imagecreatefromgif($sourcePath),
        IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
        default => false,
    };

    if (!$source) {
        return null;
    }

    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    if (in_array($info[2], [IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP], true)) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
        imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $thumbPath = thumbnail_path_for($sourcePath);
    if (!is_dir(dirname($thumbPath))) {
        mkdir(dirname($thumbPath), 0775, true);
    }

    $saved = match ($info[2]) {
        IMAGETYPE_JPEG => imagejpeg($thumb, $thumbPath, 82),
        IMAGETYPE_PNG => imagepng($thumb, $thumbPath, 6),
        IMAGETYPE_GIF => imagegif($thumb, $thumbPath),
        IMAGETYPE_WEBP => function_exists('imagewebp') ? imagewebp($thumb, $thumbPath, 82) : false,
        default => false,
    };

    return $saved ? $thumbPath : null;
}

function upload_image_file(array $file, string $folder, string $prefix = ''): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file((string) $file['tmp_name'])) {
        return null;
    }

    $extension = image_extension((string) $file['tmp_name']);
    if ($extension === null) {
        return null;
    }

    $folder = trim(str_replace('\\', '/', $folder), '/');
    if ($folder === '' || str_contains($folder, '..')) {
        return null;
    }

    $targetDir = IMAGE_ROOT . '/' . $folder;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $baseName = clean_filename((string) ($file['name'] ?? 'image'));
    if ($prefix !== '') {
        $baseName = clean_filename($prefix) . '-' . $baseName;
    }

    $filename = $baseName . '.' . $extension;
    $counter = 2;
    while (is_file($targetDir . '/' . $filename)) {
        $filename = $baseName . '-' . $counter . '.' . $extension;
        $counter++;
    }

    $targetPath = $targetDir . '/' . $filename;
    if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
        return null;
    }

    create_thumbnail($targetPath);

    return 'img/' . $folder . '/' . $filename;
}

function uploaded_files(string $field): array
{
    $files = $_FILES[$field] ?? null;
    if (!is_array($files) || !is_array($files['name'] ?? null)) {
        return [];
    }

    $normalized = [];
    foreach ($files['name'] as $index => $name) {
        $normalized[] = [
            'name' => $name,
            'type' => $files['type'][$index] ?? '',
            'tmp_name' => $files['tmp_name'][$index] ?? '',
            'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
            'size' => $files['size'][$index] ?? 0,
        ];
    }

    return $normalized;
}

function upload_single(string $field, string $folder, string $prefix = ''): ?string
{
    $file = $_FILES[$field] ?? null;
    if (!is_array($file) || is_array($file['name'] ?? null)) {
        return null;
    }

    return upload_image_file($file, $folder, $prefix);
}

function gallery_images(string $folder): array
{
    $folder = trim(str_replace('\\', '/', $folder), '/');
    if ($folder === '' || str_contains($folder, '..')) {
        return [];
    }

    $dir = IMAGE_ROOT . '/' . $folder;
    if (!is_dir($dir)) {
        return [];
    }

    $images = [];
    foreach (scandir($dir) ?: [] as $file) {
        if (is_dir($dir . '/' . $file) || preg_match('/_480\.(jpe?g|png|gif|webp)$/i', $file)) {
            continue;
        }

        if (preg_match('/\.(jpe?g|png|gif|webp)$/i', $file)) {
            $images[] = 'img/' . $folder . '/' . $file;
        }
    }

    natcasesort($images);
    return array_values($images);
}

function upload_folder(string $collection, string $id): string
{
    return UPLOAD_ROOT . '/' . $collection . '/' . lecani_slug($id);
}

function collection_items(array $content, string $collection): array
{
    return match ($collection) {
        'news' => $content['news']['items'] ?? [],
        'rules' => $content['rules']['items'] ?? [],
        'textures' => $content['downloads']['textures'] ?? [],
        'wallpapers' => $content['downloads']['wallpapers'] ?? [],
        default => $content[$collection] ?? [],
    };
}

function apply_image_changes(string $collection, array $item, ?array $existing): array
{
    $id = (string) ($item['id'] ?? '');
    $removed = array_map('strval', $_POST['remove_images'] ?? []);

    foreach ($removed as $path) {
        delete_image($path);
    }

    if ($collection === 'cities' || $collection === 'monuments') {
        if ($removed && in_array((string) ($item['thumbnail'] ?? ''), $removed, true)) {
            $item['thumbnail'] = '';
        }

        foreach (uploaded_files('gallery_uploads') as $file) {
            $uploaded = upload_image_file($file, (string) $item['imageFolder']);
            if ($uploaded && ($item['thumbnail'] ?? '') === '') {
                $item['thumbnail'] = $uploaded;
            }
        }

        if (($item['thumbnail'] ?? '') === '') {
            $gallery = gallery_images((string) $item['imageFolder']);
            $item['thumbnail'] = $gallery[0] ?? '';
        }
    }

    if ($collection === 'news') {
        $images = array_values(array_filter($item['images'] ?? [], static fn($path) => !in_array((string) $path, $removed, true)));
        foreach (uploaded_files('news_uploads') as $file) {
            $uploaded = upload_image_file($file, upload_folder('news', $id));
            if ($uploaded) {
                $images[] = $uploaded;
            }
        }
        $item['images'] = array_values(array_unique($images));
    }

    if ($collection === 'emblems' || $collection === 'textures') {
        if (!empty($_POST['remove_image']) && ($item['image'] ?? '') !== '') {
            delete_image((string) $item['image']);
            $item['image'] = '';
        }

        $uploaded = upload_single('image_upload', upload_folder($collection, $id));
        if ($uploaded) {
            $item['image'] = $uploaded;
        }
    }

    if ($collection === 'wallpapers') {
        if (!empty($_POST['remove_image']) && ($item['image'] ?? '') !== '') {
            delete_image((string) $item['image']);
            $item['image'] = '';
        }
        if (!empty($_POST['remove_fullImage']) && ($item['fullImage'] ?? '') !== '') {
            delete_image((string) $item['fullImage']);
            $item['fullImage'] = '';
            $item['image'] = '';
        }

        $thumb = upload_single('image_upload', upload_folder($collection, $id), 'thumb');
        if ($thumb) {
            $item['image'] = $thumb;
        }

        $full = upload_single('fullImage_upload', upload_folder($collection, $id), 'full');
        if ($full) {
            $item['fullImage'] = $full;
            if ($thumb === null) {
                $item['image'] = thumbnail_url_for($full);
            }
        }
    }

    return $item;
}

function set_collection_items(array &$content, string $collection, array $items): void
{
    match ($collection) {
        'news' => $content['news']['items'] = $items,
        'rules' => $content['rules']['items'] = $items,
        'textures' => $content['downloads']['textures'] = $items,
        'wallpapers' => $content['downloads']['wallpapers'] = $items,
        default => $content[$collection] = $items,
    };
}

function build_item(string $collection, ?array $existing = null): array
{
    $existing ??= [];
    $title = trim((string) ($_POST['title'] ?? $existing['title'] ?? ''));
    $id = trim((string) ($_POST['id'] ?? $existing['id'] ?? ''));
    $id = $id !== '' ? lecani_slug($id) : lecani_slug($title);

    $item = match ($collection) {
        'cities', 'monuments' => [
            'id' => $id,
            'title' => $title,
            'attributes' => posted_lines('attributes'),
            'imageFolder' => trim((string) ($_POST['imageFolder'] ?? ''), '/') ?: ($collection === 'cities' ? 'city/' . $id : 'monument/' . $id),
            'thumbnail' => trim((string) ($_POST['thumbnail'] ?? '')),
            'descriptionHtml' => trim((string) ($_POST['descriptionHtml'] ?? '')),
            'enabled' => normalize_enabled(),
        ],
        'emblems' => [
            'id' => $id,
            'title' => $title,
            'image' => trim((string) ($_POST['image'] ?? '')),
            'descriptionHtml' => trim((string) ($_POST['descriptionHtml'] ?? '')),
            'enabled' => normalize_enabled(),
        ],
        'news' => [
            'id' => $id,
            'title' => $title,
            'date' => trim((string) ($_POST['date'] ?? '')),
            'images' => posted_lines('images'),
            'bodyHtml' => trim((string) ($_POST['bodyHtml'] ?? '')),
            'enabled' => normalize_enabled(),
        ],
        'rules' => [
            'id' => $id,
            'title' => $title,
            'description' => trim((string) ($_POST['description'] ?? '')),
            'enabled' => normalize_enabled(),
        ],
        'textures' => [
            'id' => $id,
            'title' => $title,
            'image' => trim((string) ($_POST['image'] ?? '')),
            'version' => trim((string) ($_POST['version'] ?? '')),
            'download' => trim((string) ($_POST['download'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'enabled' => normalize_enabled(),
        ],
        'wallpapers' => [
            'id' => $id,
            'title' => $title,
            'image' => trim((string) ($_POST['image'] ?? '')),
            'fullImage' => trim((string) ($_POST['fullImage'] ?? '')),
            'caption' => trim((string) ($_POST['caption'] ?? '')),
            'enabled' => normalize_enabled(),
        ],
        default => $existing,
    };

    return apply_image_changes($collection, $item, $existing);
}

function find_item(array $items, string $id): ?array
{
    foreach ($items as $item) {
        if (($item['id'] ?? '') === $id) {
            return $item;
        }
    }

    return null;
}

function save_password(string $password): bool
{
    if ($password === '') {
        return false;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    return file_put_contents(PASSWORD_FILE, "<?php\n\nreturn " . var_export($hash, true) . ";\n", LOCK_EX) !== false;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$requestMethod = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($requestMethod === 'POST' && isset($_POST['login_password'])) {
    if (password_verify((string) $_POST['login_password'], admin_password_hash())) {
        $_SESSION['lecani_admin'] = true;
        csrf_token();
        header('Location: index.php');
        exit;
    }
    $login_error = 'Fel lösenord.';
}

if (is_admin() && $requestMethod === 'POST' && isset($_POST['admin_action'])) {
    require_csrf();
    $content = lecani_content();
    $collection = (string) ($_POST['collection'] ?? '');
    $action = (string) $_POST['admin_action'];

    if ($action === 'settings') {
        $content['news']['introHtml'] = trim((string) ($_POST['news_intro'] ?? ''));
        $content['rules']['extraHtml'] = trim((string) ($_POST['rules_extra'] ?? ''));
        lecani_save_content($content);
        header('Location: index.php?collection=settings&saved=1');
        exit;
    }

    if ($action === 'password') {
        save_password((string) ($_POST['new_password'] ?? ''));
        header('Location: index.php?collection=settings&saved=1');
        exit;
    }

    if (array_key_exists($collection, $collections) && $collection !== 'settings') {
        $items = collection_items($content, $collection);
        $id = (string) ($_POST['original_id'] ?? '');

        if ($action === 'delete') {
            $items = array_values(array_filter($items, static fn($item) => ($item['id'] ?? '') !== $id));
        } elseif ($action === 'save') {
            $existing = $id !== '' ? find_item($items, $id) : null;
            $item = build_item($collection, $existing);
            $updated = false;

            foreach ($items as $index => $current) {
                if (($current['id'] ?? '') === $id) {
                    $items[$index] = $item;
                    $updated = true;
                    break;
                }
            }

            if (!$updated) {
                $items[] = $item;
            }
        }

        set_collection_items($content, $collection, $items);
        lecani_save_content($content);
        header('Location: index.php?collection=' . urlencode($collection) . '&saved=1');
        exit;
    }
}

$content = lecani_content();
$collection = (string) ($_GET['collection'] ?? 'cities');
if (!array_key_exists($collection, $collections)) {
    $collection = 'cities';
}
$action = (string) ($_GET['action'] ?? 'list');
$editId = (string) ($_GET['id'] ?? '');
$items = $collection !== 'settings' ? collection_items($content, $collection) : [];
$editing = $editId !== '' ? find_item($items, $editId) : null;
if ($action === 'new') {
    $editing = [];
}

function render_image_cards(array $images, string $checkboxName = 'remove_images[]'): void
{
    if (!$images) {
        echo '<p class="muted">Inga bilder ännu.</p>';
        return;
    }
    ?>
    <div class="image-grid">
      <?php foreach ($images as $image): ?>
        <div class="image-card">
          <img src="../<?= e((string) $image) ?>" alt="" />
          <label><input type="checkbox" name="<?= e($checkboxName) ?>" value="<?= e((string) $image) ?>" /> Ta bort</label>
          <label class="muted"><?= e((string) $image) ?></label>
        </div>
      <?php endforeach; ?>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LeCaNi admin</title>
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background: #151515; color: #f5f5f5; }
    a { color: #8fd3ff; }
    .wrap { max-width: 1180px; margin: 0 auto; padding: 24px; }
    .top { display: flex; justify-content: space-between; align-items: center; gap: 16px; }
    nav { display: flex; flex-wrap: wrap; gap: 8px; margin: 20px 0; }
    nav a, .button { border: 1px solid #555; border-radius: 6px; color: white; display: inline-block; padding: 8px 12px; text-decoration: none; background: #2a2a2a; }
    nav a.active, .button.primary { background: #155a76; border-color: #2785aa; }
    table { width: 100%; border-collapse: collapse; background: #202020; }
    th, td { border-bottom: 1px solid #333; padding: 10px; text-align: left; vertical-align: top; }
    label { display: block; margin: 14px 0 6px; font-weight: bold; }
    input[type=text], input[type=password], input[type=file], textarea { width: 100%; box-sizing: border-box; border: 1px solid #555; border-radius: 6px; padding: 10px; background: #101010; color: #f5f5f5; font: inherit; }
    textarea { min-height: 140px; }
    .short { max-width: 420px; }
    .row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .actions { display: flex; gap: 8px; align-items: center; }
    .notice { background: #173f23; border: 1px solid #28733b; padding: 10px; border-radius: 6px; }
    .danger { background: #672020; border-color: #9d3939; }
    .muted { color: #bdbdbd; }
    .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; margin: 10px 0 18px; }
    .image-card { background: #202020; border: 1px solid #333; border-radius: 6px; padding: 8px; }
    .image-card img { width: 100%; height: 100px; object-fit: cover; border-radius: 4px; display: block; margin-bottom: 6px; }
    .image-card label { font-weight: normal; margin: 4px 0 0; overflow-wrap: anywhere; }
    .field-note { margin: 6px 0 0; color: #bdbdbd; font-size: 0.95rem; }
    .login { max-width: 420px; margin: 15vh auto; background: #202020; padding: 24px; border-radius: 8px; }
    @media (max-width: 760px) { .row { grid-template-columns: 1fr; } .top { display: block; } }
  </style>
</head>
<body>
<?php if (!is_admin()): ?>
  <main class="login">
    <h1>LeCaNi admin</h1>
    <?php if (!empty($login_error)): ?><p class="notice danger"><?= e($login_error) ?></p><?php endif; ?>
    <form method="post">
      <label for="login_password">Lösenord</label>
      <input id="login_password" name="login_password" type="password" autofocus />
      <p><button class="button primary" type="submit">Logga in</button></p>
    </form>
  </main>
<?php else: ?>
  <main class="wrap">
    <div class="top">
      <div>
        <h1>LeCaNi admin</h1>
        <p class="muted">Lägg till, redigera och ta bort innehåll som visas på startsidan.</p>
      </div>
      <a class="button" href="?logout=1">Logga ut</a>
    </div>

    <nav>
      <?php foreach ($collections as $key => $label): ?>
        <a class="<?= $collection === $key ? 'active' : '' ?>" href="?collection=<?= e($key) ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>

    <?php if (isset($_GET['saved'])): ?><p class="notice">Sparat.</p><?php endif; ?>

    <?php if ($collection === 'settings'): ?>
      <h2>Sidtexter</h2>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
        <input type="hidden" name="admin_action" value="settings" />
        <label for="news_intro">Introtext för nyheter</label>
        <textarea id="news_intro" name="news_intro"><?= e((string) ($content['news']['introHtml'] ?? '')) ?></textarea>
        <label for="rules_extra">Extra HTML under regler</label>
        <textarea id="rules_extra" name="rules_extra"><?= e((string) ($content['rules']['extraHtml'] ?? '')) ?></textarea>
        <p><button class="button primary" type="submit">Spara texter</button></p>
      </form>

      <h2>Byt lösenord</h2>
      <form method="post" class="short">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
        <input type="hidden" name="admin_action" value="password" />
        <label for="new_password">Nytt lösenord</label>
        <input id="new_password" name="new_password" type="password" />
        <p><button class="button primary" type="submit">Spara lösenord</button></p>
      </form>
    <?php elseif ($action === 'new' || $editing !== null): ?>
      <h2><?= $action === 'new' ? 'Ny post' : 'Redigera post' ?></h2>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
        <input type="hidden" name="admin_action" value="save" />
        <input type="hidden" name="collection" value="<?= e($collection) ?>" />
        <input type="hidden" name="original_id" value="<?= e((string) ($editing['id'] ?? '')) ?>" />
        <div class="row">
          <div>
            <label for="title">Titel</label>
            <input id="title" name="title" type="text" value="<?= e((string) ($editing['title'] ?? '')) ?>" />
          </div>
          <div>
            <label for="id">ID</label>
            <input id="id" name="id" type="text" value="<?= e((string) ($editing['id'] ?? '')) ?>" />
          </div>
        </div>
        <label><input name="enabled" type="checkbox" value="1" <?= lecani_enabled($editing ?? []) ? 'checked' : '' ?> /> Synlig på sidan</label>

        <?php if ($collection === 'cities' || $collection === 'monuments'): ?>
          <?php $currentFolder = trim((string) ($editing['imageFolder'] ?? ''), '/'); ?>
          <div class="row">
            <div>
              <label for="imageFolder">Bildmapp under img/</label>
              <input id="imageFolder" name="imageFolder" type="text" value="<?= e((string) ($editing['imageFolder'] ?? '')) ?>" placeholder="<?= e($collection === 'cities' ? 'city/fabulania' : 'monument/ldtornen') ?>" />
              <p class="field-note">Nya bilder laddas upp till den här mappen. Om den lämnas tom skapas en mapp baserad på postens ID.</p>
            </div>
            <div>
              <label for="thumbnail">Startbild (original)</label>
              <input id="thumbnail" name="thumbnail" type="text" value="<?= e((string) ($editing['thumbnail'] ?? '')) ?>" />
              <p class="field-note">Spara originalbilden här. Sajten använder automatiskt motsvarande thumbnail från <code>thumbs/</code> när den finns.</p>
            </div>
          </div>
          <label for="gallery_uploads">Ladda upp bilder till galleriet</label>
          <input id="gallery_uploads" name="gallery_uploads[]" type="file" accept="image/*" multiple />
          <h3>Nuvarande originalbilder</h3>
          <?php render_image_cards($currentFolder !== '' ? gallery_images($currentFolder) : []); ?>
          <label for="attributes">Faktarader, en per rad</label>
          <textarea id="attributes" name="attributes"><?= e(implode("\n", $editing['attributes'] ?? [])) ?></textarea>
          <label for="descriptionHtml">Beskrivning, HTML tillåtet</label>
          <textarea id="descriptionHtml" name="descriptionHtml"><?= e((string) ($editing['descriptionHtml'] ?? '')) ?></textarea>
        <?php elseif ($collection === 'emblems'): ?>
          <label for="image">Bild</label>
          <input id="image" name="image" type="text" value="<?= e((string) ($editing['image'] ?? '')) ?>" />
          <?php if (!empty($editing['image'])): ?>
            <?php render_image_cards([(string) $editing['image']], 'remove_image'); ?>
          <?php endif; ?>
          <label for="image_upload">Ladda upp ny bild</label>
          <input id="image_upload" name="image_upload" type="file" accept="image/*" />
          <label for="descriptionHtml">Beskrivning, HTML tillåtet</label>
          <textarea id="descriptionHtml" name="descriptionHtml"><?= e((string) ($editing['descriptionHtml'] ?? '')) ?></textarea>
        <?php elseif ($collection === 'news'): ?>
          <label for="date">Datum</label>
          <input id="date" name="date" type="text" value="<?= e((string) ($editing['date'] ?? '')) ?>" />
          <label for="images">Bilder, en sökväg per rad</label>
          <textarea id="images" name="images"><?= e(implode("\n", $editing['images'] ?? [])) ?></textarea>
          <label for="news_uploads">Ladda upp bilder</label>
          <input id="news_uploads" name="news_uploads[]" type="file" accept="image/*" multiple />
          <h3>Nuvarande nyhetsbilder</h3>
          <?php render_image_cards($editing['images'] ?? []); ?>
          <label for="bodyHtml">Brödtext, HTML tillåtet</label>
          <textarea id="bodyHtml" name="bodyHtml"><?= e((string) ($editing['bodyHtml'] ?? '')) ?></textarea>
        <?php elseif ($collection === 'rules'): ?>
          <label for="description">Beskrivning</label>
          <textarea id="description" name="description"><?= e((string) ($editing['description'] ?? '')) ?></textarea>
        <?php elseif ($collection === 'textures'): ?>
          <div class="row">
            <div>
              <label for="image">Bild</label>
              <input id="image" name="image" type="text" value="<?= e((string) ($editing['image'] ?? '')) ?>" />
              <?php if (!empty($editing['image'])): ?>
                <?php render_image_cards([(string) $editing['image']], 'remove_image'); ?>
              <?php endif; ?>
              <label for="image_upload">Ladda upp ny bild</label>
              <input id="image_upload" name="image_upload" type="file" accept="image/*" />
            </div>
            <div>
              <label for="version">Version</label>
              <input id="version" name="version" type="text" value="<?= e((string) ($editing['version'] ?? '')) ?>" />
            </div>
          </div>
          <label for="download">Nedladdningslänk</label>
          <input id="download" name="download" type="text" value="<?= e((string) ($editing['download'] ?? '')) ?>" />
          <label for="description">Beskrivning</label>
          <textarea id="description" name="description"><?= e((string) ($editing['description'] ?? '')) ?></textarea>
        <?php elseif ($collection === 'wallpapers'): ?>
          <input name="image" type="hidden" value="<?= e((string) ($editing['image'] ?? '')) ?>" />
          <div class="row">
            <div>
              <label>Miniatyrbild</label>
              <p class="field-note">Skapas automatiskt i <code>thumbs/</code> när du laddar upp en fullstor bild.</p>
            </div>
            <div>
              <label for="fullImage">Fullstor bild</label>
              <input id="fullImage" name="fullImage" type="text" value="<?= e((string) ($editing['fullImage'] ?? '')) ?>" />
              <?php if (!empty($editing['fullImage'])): ?>
                <?php render_image_cards([(string) $editing['fullImage']], 'remove_fullImage'); ?>
              <?php endif; ?>
              <label for="fullImage_upload">Ladda upp ny fullstor bild</label>
              <input id="fullImage_upload" name="fullImage_upload" type="file" accept="image/*" />
            </div>
          </div>
          <label for="caption">Bildtext</label>
          <textarea id="caption" name="caption"><?= e((string) ($editing['caption'] ?? '')) ?></textarea>
        <?php endif; ?>

        <p class="actions">
          <button class="button primary" type="submit">Spara</button>
          <a class="button" href="?collection=<?= e($collection) ?>">Avbryt</a>
        </p>
      </form>
    <?php else: ?>
      <p><a class="button primary" href="?collection=<?= e($collection) ?>&action=new">Lägg till</a></p>
      <table>
        <thead><tr><th>Titel</th><th>ID</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?= e((string) (($item['title'] ?? '') !== '' ? $item['title'] : ($item['caption'] ?? $item['id'] ?? ''))) ?></td>
              <td><?= e((string) ($item['id'] ?? '')) ?></td>
              <td><?= lecani_enabled($item) ? 'Synlig' : 'Dold' ?></td>
              <td class="actions">
                <a class="button" href="?collection=<?= e($collection) ?>&action=edit&id=<?= e((string) ($item['id'] ?? '')) ?>">Redigera</a>
                <form method="post" onsubmit="return confirm('Ta bort posten?');">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                  <input type="hidden" name="admin_action" value="delete" />
                  <input type="hidden" name="collection" value="<?= e($collection) ?>" />
                  <input type="hidden" name="original_id" value="<?= e((string) ($item['id'] ?? '')) ?>" />
                  <button class="button danger" type="submit">Ta bort</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </main>
<?php endif; ?>
</body>
</html>
