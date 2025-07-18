<?php
// File Browser dengan UI/UX yang Menarik
$currentDir = isset($_GET['dir']) ? $_GET['dir'] : '.';
$currentDir = rtrim($currentDir, '/');

// Fungsi untuk mendapatkan ukuran file yang readable
function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

// Fungsi untuk mendapatkan icon berdasarkan ekstensi file
function getFileIcon($fileName) {
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $icons = [
        'php' => 'fab fa-php text-info',
        'html' => 'fab fa-html5 text-danger',
        'css' => 'fab fa-css3-alt text-primary',
        'js' => 'fab fa-js-square text-warning',
        'json' => 'fas fa-code text-success',
        'xml' => 'fas fa-code text-warning',
        'txt' => 'fas fa-file-alt text-secondary',
        'md' => 'fab fa-markdown text-dark',
        'pdf' => 'fas fa-file-pdf text-danger',
        'doc' => 'fas fa-file-word text-primary',
        'docx' => 'fas fa-file-word text-primary',
        'xls' => 'fas fa-file-excel text-success',
        'xlsx' => 'fas fa-file-excel text-success',
        'ppt' => 'fas fa-file-powerpoint text-warning',
        'pptx' => 'fas fa-file-powerpoint text-warning',
        'zip' => 'fas fa-file-archive text-warning',
        'rar' => 'fas fa-file-archive text-warning',
        '7z' => 'fas fa-file-archive text-warning',
        'tar' => 'fas fa-file-archive text-warning',
        'gz' => 'fas fa-file-archive text-warning',
        'jpg' => 'fas fa-file-image text-success',
        'jpeg' => 'fas fa-file-image text-success',
        'png' => 'fas fa-file-image text-success',
        'gif' => 'fas fa-file-image text-success',
        'svg' => 'fas fa-file-image text-success',
        'webp' => 'fas fa-file-image text-success',
        'mp4' => 'fas fa-file-video text-primary',
        'avi' => 'fas fa-file-video text-primary',
        'mkv' => 'fas fa-file-video text-primary',
        'mov' => 'fas fa-file-video text-primary',
        'mp3' => 'fas fa-file-audio text-info',
        'wav' => 'fas fa-file-audio text-info',
        'flac' => 'fas fa-file-audio text-info',
    ];
    
    return isset($icons[$extension]) ? $icons[$extension] : 'fas fa-file text-secondary';
}

// Validasi dan normalisasi path
$realCurrentDir = realpath($currentDir);
if (!$realCurrentDir || !is_dir($realCurrentDir)) {
    $currentDir = '.';
    $realCurrentDir = realpath('.');
}

// Membaca isi direktori
$files = [];
$directories = [];

if ($handle = opendir($realCurrentDir)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $fullPath = $realCurrentDir . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($fullPath)) {
                $directories[] = [
                    'name' => $entry,
                    'path' => $currentDir . '/' . $entry,
                    'modified' => date('Y-m-d H:i:s', filemtime($fullPath))
                ];
            } else {
                $files[] = [
                    'name' => $entry,
                    'path' => $currentDir . '/' . $entry,
                    'size' => filesize($fullPath),
                    'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
                    'icon' => getFileIcon($entry)
                ];
            }
        }
    }
    closedir($handle);
}

// Sorting
usort($directories, function($a, $b) { return strcasecmp($a['name'], $b['name']); });
usort($files, function($a, $b) { return strcasecmp($a['name'], $b['name']); });

// Breadcrumb navigation
$breadcrumbs = [];
$pathParts = explode('/', trim($currentDir, './'));
$cumulativePath = '.';
$breadcrumbs[] = ['name' => 'Home', 'path' => '.'];

foreach ($pathParts as $part) {
    if (!empty($part)) {
        $cumulativePath .= '/' . $part;
        $breadcrumbs[] = ['name' => $part, 'path' => $cumulativePath];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Browser - <?php echo htmlspecialchars($currentDir); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container-main {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .header-section {
            background: linear-gradient(135deg, #6c5ce7, #74b9ff);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .breadcrumb {
            background: rgba(108, 92, 231, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        
        .file-item, .folder-item {
            background: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        
        .file-item:hover, .folder-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .item-content {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }
        
        .item-content:hover {
            color: inherit;
            text-decoration: none;
        }
        
        .item-icon {
            font-size: 2rem;
            margin-right: 1rem;
            min-width: 50px;
            text-align: center;
        }
        
        .item-details {
            flex-grow: 1;
        }
        
        .item-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
            color: #2d3436;
        }
        
        .item-meta {
            font-size: 0.85rem;
            color: #636e72;
        }
        
        .folder-item .item-content {
            background: linear-gradient(135deg, #74b9ff20, #6c5ce720);
        }
        
        .folder-item .item-name {
            color: #0984e3;
            font-weight: 600;
        }
        
        .file-item .item-content {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
        }
        
        .stats-section {
            background: linear-gradient(135deg, #00b894, #00cec9);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }
        
        .search-box {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .search-input {
            border: none;
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            background: #f8f9fa;
            width: 100%;
            font-size: 1rem;
        }
        
        .search-input:focus {
            outline: none;
            background: #e9ecef;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #636e72;
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .container-main {
                margin: 1rem;
                padding: 1rem;
            }
            
            .item-content {
                padding: 1rem;
            }
            
            .item-icon {
                font-size: 1.5rem;
                margin-right: 0.75rem;
                min-width: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="container-main">
            <!-- Header -->
            <div class="header-section">
                <h1><i class="fas fa-folder-open me-3"></i>File Browser</h1>
                <p class="mb-0">Jelajahi file dan folder dengan mudah</p>
            </div>
            
            <!-- Statistics -->
            <div class="stats-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count($directories); ?></span>
                            <small>Folder</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count($files); ?></span>
                            <small>File</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count($directories) + count($files); ?></span>
                            <small>Total Item</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                        <?php if ($index === count($breadcrumbs) - 1): ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-folder"></i> <?php echo htmlspecialchars($breadcrumb['name']); ?>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item">
                                <a href="?dir=<?php echo urlencode($breadcrumb['path']); ?>" class="text-decoration-none">
                                    <i class="fas fa-folder"></i> <?php echo htmlspecialchars($breadcrumb['name']); ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
            
            <!-- Search Box -->
            <div class="search-box">
                <input type="text" id="searchInput" class="search-input" placeholder="🔍 Cari file atau folder...">
            </div>
            
            <!-- File List -->
            <div id="fileList">
                <?php if (empty($directories) && empty($files)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h4>Folder Kosong</h4>
                        <p>Tidak ada file atau folder di direktori ini.</p>
                    </div>
                <?php else: ?>
                    <!-- Parent Directory Link -->
                    <?php if ($currentDir !== '.'): ?>
                        <div class="folder-item">
                            <a href="?dir=<?php echo urlencode(dirname($currentDir)); ?>" class="item-content">
                                <div class="item-icon">
                                    <i class="fas fa-level-up-alt text-primary"></i>
                                </div>
                                <div class="item-details">
                                    <div class="item-name">.. (Kembali)</div>
                                    <div class="item-meta">Direktori Parent</div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Directories -->
                    <?php foreach ($directories as $dir): ?>
                        <div class="folder-item searchable-item" data-name="<?php echo strtolower($dir['name']); ?>">
                            <div  class="item-content">
                                <div class="item-icon">
                                    <i class="fas fa-folder text-warning"></i>
                                </div>
                                <div class="item-details">
                                    <a href="<?php echo $dir['path']; ?>" class="item-name">
										<?php echo htmlspecialchars($dir['name']); ?>/
									</a>
                                    <div class="item-meta">
                                        <i class="fas fa-clock me-1"></i>
                                        Dimodifikasi: <?php echo $dir['modified']; ?>
                                    </div>
                                </div>
                                <div class="text-end">
									<a href="?dir=<?php echo urlencode($dir['path']); ?>">
										<i class="fas fa-chevron-right text-muted"></i>
									</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Files -->
                    <?php foreach ($files as $file): ?>
                        <div class="file-item searchable-item" data-name="<?php echo strtolower($file['name']); ?>">
                            <a href="<?php echo htmlspecialchars($file['path']); ?>" class="item-content" target="_blank">
                                <div class="item-icon">
                                    <i class="<?php echo $file['icon']; ?>"></i>
                                </div>
                                <div class="item-details">
                                    <div class="item-name"><?php echo htmlspecialchars($file['name']); ?></div>
                                    <div class="item-meta">
                                        <i class="fas fa-hdd me-1"></i>
                                        <?php echo formatBytes($file['size']); ?>
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo $file['modified']; ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <i class="fas fa-external-link-alt text-muted"></i>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll('.searchable-item');
            
            items.forEach(item => {
                const itemName = item.getAttribute('data-name');
                if (itemName.includes(searchTerm)) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeIn 0.3s ease';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Add fade-in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
        
        // Add loading animation for links
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon && !icon.classList.contains('fa-spin')) {
                    icon.classList.add('fa-spin');
                }
            });
        });
    </script>
</body>
</html>