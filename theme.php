<?php
// theme.php
session_start();

// Function to get current theme
function getCurrentTheme() {
    return $_SESSION['theme'] ?? 'dark';
}

// CSS Variables for themes
$themes = [
    'dark' => [
        '--bg-gradient-from' => '#000428',
        '--bg-gradient-to' => '#004e92',
        '--text-color' => '#ffffff',
        '--nav-bg' => 'rgba(255, 255, 255, 0)',
        '--card-bg' => 'rgba(255, 255, 255, 0.1)',
        '--input-bg' => 'rgba(255, 255, 255, 0.1)',
        '--border-color' => 'rgba(255, 255, 255, 0.2)',
        '--hover-bg' => 'rgba(255, 255, 255, 0.2)',
        '--table-header-bg' => '#444',
        '--box-shadow: 0 4px 8px rgba(255, 255, 255, 0.7)',
        '--table-border' => '#ffffff',
        '--button-bg' => '#4a90e2',
        '--button-text' => '#ffffff',
        '--search-input-bg' => 'rgba(255, 255, 255, 0.1)'
    ],
    'light' => [
        '--bg-gradient-from' => '#ffffff',
        '--bg-gradient-to' => '#f0f0f0',
        '--text-color' => '#000000',
        '--nav-bg' => 'rgba(255, 255, 255, 0.8)',
        '--card-bg' => 'rgba(255, 255, 255, 0.5)',
        '--input-bg' => 'rgba(0, 0, 0, 0.1)',
        '--border-color' => 'rgba(0, 0, 0, 0.2)',
        '--box-shadow: 0 4px 8px rgba(0, 0, 0, 0.7)',
        '--hover-bg' => 'rgba(0, 0, 0, 0.1)',
        '--table-header-bg' => '#e0e0e0',
        '--table-border' => '#000000',
        '--button-bg' => '#0066cc',
        '--button-text' => '#ffffff',
        '--search-input-bg' => 'rgba(0, 0, 0, 0.1);'
    ]
];

// Generate CSS variables
function generateThemeCSS() {
    global $themes;
    $currentTheme = getCurrentTheme();
    $css = ':root {';
    foreach ($themes[$currentTheme] as $variable => $value) {
        $css .= "$variable: $value;";
    }
    $css .= '}';
    return $css;
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?php echo getCurrentTheme(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Jujutsu Kaisen'; ?></title>
    <style>
        <?php echo generateThemeCSS(); ?>

        /* Base styles using CSS variables */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, var(--bg-gradient-from), var(--bg-gradient-to));
            color: var(--text-color);
            min-height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .navbar {
            background: var(--nav-bg);
            color: var(--text-color);
        }

        .nav-links a {
            color: var(--text-color);
        }

        table {
            border-color: var(--table-border);
        }

        th {
            background-color: var(--table-header-bg);
        }

        .button {
            background-color: var(--button-bg);
            color: var(--button-text);
        }

        input, select, textarea {
            background-color: var(--input-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .theme-toggle {
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .theme-toggle:hover {
            background-color: var(--hover-bg);
        }
    </style>
</head>
<body>
    <script>
        // Theme toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.querySelector('.theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const currentTheme = document.documentElement.getAttribute('data-theme');
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    
                    // Update theme via AJAX
                    fetch('update_theme.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `theme=${newTheme}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.documentElement.setAttribute('data-theme', newTheme);
                            updateThemeIcon(newTheme === 'light');
                            location.reload(); // Refresh to apply new theme
                        }
                    });
                });
            }

            function updateThemeIcon(isLight) {
                const icon = document.querySelector('.theme-toggle i');
                if (icon) {
                    icon.className = isLight ? 'fas fa-moon' : 'fas fa-sun';
                }
            }
        });
    </script>
</body>
</html>