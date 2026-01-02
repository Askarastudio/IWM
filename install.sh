#!/bin/bash

# INSIA Work Monitor - Installation Script
# This script helps set up IWM on a Linux server

echo "======================================"
echo "INSIA Work Monitor - Installation"
echo "======================================"
echo ""

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   echo "This script should be run as root or with sudo"
   exit 1
fi

echo "Step 1: Checking system requirements..."

# Check PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 7.4 or higher."
    exit 1
else
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2)
    echo "✅ PHP $PHP_VERSION found"
fi

# Check MySQL
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL is not installed. Please install MySQL 5.7 or higher."
    exit 1
else
    echo "✅ MySQL found"
fi

echo ""
echo "Step 2: Database setup..."
read -p "Enter MySQL root password: " -s MYSQL_ROOT_PASS
echo ""

read -p "Enter new database name [iwm_db]: " DB_NAME
DB_NAME=${DB_NAME:-iwm_db}

read -p "Enter new database user [iwm_user]: " DB_USER
DB_USER=${DB_USER:-iwm_user}

read -p "Enter password for database user: " -s DB_PASS
echo ""

# Create database and user
mysql -u root -p"$MYSQL_ROOT_PASS" <<EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

if [ $? -eq 0 ]; then
    echo "✅ Database created successfully"
else
    echo "❌ Database creation failed"
    exit 1
fi

# Import schema
echo "Importing database schema..."
mysql -u root -p"$MYSQL_ROOT_PASS" $DB_NAME < database/schema.sql

if [ $? -eq 0 ]; then
    echo "✅ Schema imported successfully"
else
    echo "❌ Schema import failed"
    exit 1
fi

echo ""
echo "Step 3: Configuring application..."

# Create .env file
cat > .env <<EOF
DB_HOST=localhost
DB_NAME=$DB_NAME
DB_USER=$DB_USER
DB_PASS=$DB_PASS
JWT_SECRET=$(openssl rand -base64 32)
EOF

echo "✅ Configuration file created"

echo ""
echo "Step 4: Setting up directories..."

# Create necessary directories
mkdir -p uploads/screenshots
mkdir -p logs

# Set permissions
chown -R www-data:www-data .
chmod -R 755 .
chmod -R 755 uploads
chmod -R 755 logs

echo "✅ Directories created and permissions set"

echo ""
echo "Step 5: Web server configuration..."

# Detect web server
if systemctl is-active --quiet apache2; then
    echo "Apache detected"
    # Enable mod_rewrite
    a2enmod rewrite
    systemctl restart apache2
    echo "✅ Apache configured"
elif systemctl is-active --quiet nginx; then
    echo "Nginx detected"
    echo "⚠️  Please manually configure Nginx (see docs/INSTALLATION.md)"
else
    echo "⚠️  No web server detected. Please install Apache or Nginx"
fi

echo ""
echo "======================================"
echo "Installation Complete!"
echo "======================================"
echo ""
echo "Database: $DB_NAME"
echo "DB User: $DB_USER"
echo ""
echo "Next steps:"
echo "1. Access the dashboard: http://your-domain/frontend/"
echo "2. Login with: admin@iwm.local / admin123"
echo "3. ⚠️  CHANGE THE DEFAULT PASSWORD IMMEDIATELY"
echo "4. Add employees and register devices"
echo "5. Install agent on client PCs"
echo ""
echo "Documentation: docs/INSTALLATION.md"
echo "======================================"
