<?php
/**
 * System Configuration Checker
 * INSIA Work Monitor (IWM)
 * 
 * This script checks if your system meets the requirements for IWM
 */

echo "====================================\n";
echo "IWM System Configuration Checker\n";
echo "====================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// Check PHP version
echo "Checking PHP version... ";
$phpVersion = phpversion();
if (version_compare($phpVersion, '7.4.0', '>=')) {
    echo "✅ PHP $phpVersion\n";
    $success[] = "PHP version $phpVersion is supported";
} else {
    echo "❌ FAIL\n";
    $errors[] = "PHP 7.4 or higher is required. Current version: $phpVersion";
}

// Check required PHP extensions
echo "\nChecking PHP extensions:\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl', 'hash'];

foreach ($requiredExtensions as $ext) {
    echo "  - $ext... ";
    if (extension_loaded($ext)) {
        echo "✅\n";
        $success[] = "Extension $ext is loaded";
    } else {
        echo "❌\n";
        $errors[] = "Required PHP extension not loaded: $ext";
    }
}

// Check optional PHP extensions
$optionalExtensions = ['gd', 'curl', 'zip'];
foreach ($optionalExtensions as $ext) {
    echo "  - $ext (optional)... ";
    if (extension_loaded($ext)) {
        echo "✅\n";
        $success[] = "Optional extension $ext is loaded";
    } else {
        echo "⚠️ \n";
        $warnings[] = "Optional extension not loaded: $ext";
    }
}

// Check database configuration
echo "\nChecking database configuration... ";
if (file_exists(__DIR__ . '/backend/config/database.php')) {
    echo "✅\n";
    
    // Try to connect
    try {
        require_once __DIR__ . '/backend/config/database.php';
        $db = Database::getInstance();
        $result = $db->fetchOne("SELECT 1 as test");
        if ($result) {
            echo "Database connection... ✅\n";
            $success[] = "Database connection successful";
            
            // Check tables
            $tables = $db->fetchAll("SHOW TABLES");
            echo "Database tables found: " . count($tables) . "\n";
            if (count($tables) > 0) {
                $success[] = "Database schema is installed";
            } else {
                $warnings[] = "No tables found. Run database/schema.sql";
            }
        }
    } catch (Exception $e) {
        echo "❌\n";
        $errors[] = "Database connection failed: " . $e->getMessage();
    }
} else {
    echo "❌\n";
    $errors[] = "Database configuration file not found";
}

// Check directories and permissions
echo "\nChecking directories and permissions:\n";

$directories = [
    'uploads' => true,
    'uploads/screenshots' => true,
    'logs' => true,
    'backend' => false,
    'frontend' => false
];

foreach ($directories as $dir => $writable) {
    echo "  - $dir... ";
    if (file_exists($dir)) {
        if (!$writable) {
            echo "✅\n";
            $success[] = "Directory $dir exists";
        } else {
            if (is_writable($dir)) {
                echo "✅ (writable)\n";
                $success[] = "Directory $dir is writable";
            } else {
                echo "⚠️  (not writable)\n";
                $warnings[] = "Directory $dir is not writable";
            }
        }
    } else {
        if ($writable) {
            echo "❌\n";
            $errors[] = "Directory $dir does not exist or is not writable";
        } else {
            echo "⚠️ \n";
            $warnings[] = "Directory $dir does not exist";
        }
    }
}

// Check configuration files
echo "\nChecking configuration files:\n";
$configFiles = [
    'backend/config/config.php',
    'backend/config/database.php',
    'database/schema.sql'
];

foreach ($configFiles as $file) {
    echo "  - $file... ";
    if (file_exists($file)) {
        echo "✅\n";
        $success[] = "Config file $file exists";
    } else {
        echo "❌\n";
        $errors[] = "Required file not found: $file";
    }
}

// Check web server
echo "\nDetecting web server... ";
$server = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
echo "$server\n";

if (strpos(strtolower($server), 'apache') !== false) {
    echo "  - mod_rewrite... ";
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if (in_array('mod_rewrite', $modules)) {
            echo "✅\n";
            $success[] = "Apache mod_rewrite is enabled";
        } else {
            echo "⚠️ \n";
            $warnings[] = "Apache mod_rewrite may not be enabled";
        }
    } else {
        echo "⚠️  (cannot detect)\n";
        $warnings[] = "Cannot detect Apache modules";
    }
}

// Summary
echo "\n====================================\n";
echo "Summary\n";
echo "====================================\n\n";

if (count($errors) > 0) {
    echo "❌ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "⚠️  WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "  - $warning\n";
    }
    echo "\n";
}

echo "✅ SUCCESS (" . count($success) . "):\n";
if (count($success) > 5) {
    echo "  - All major requirements met\n";
} else {
    foreach ($success as $item) {
        echo "  - $item\n";
    }
}

echo "\n";

if (count($errors) === 0) {
    echo "✅ System is ready for IWM!\n\n";
    echo "Next steps:\n";
    echo "1. Access: http://your-domain/frontend/\n";
    echo "2. Login: admin@iwm.local / admin123\n";
    echo "3. Change default password\n";
    echo "4. Start adding employees and devices\n";
} else {
    echo "❌ Please fix the errors above before using IWM\n";
    echo "See docs/INSTALLATION.md for help\n";
}

echo "\n====================================\n";
