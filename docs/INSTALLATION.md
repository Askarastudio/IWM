# INSIA Work Monitor (IWM) - Installation Guide

## Overview
INSIA Work Monitor (IWM) is a comprehensive employee monitoring system designed to track application usage, web activity, and work productivity while respecting privacy boundaries.

## System Requirements

### Backend Server (VPS)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Minimum 1GB RAM
- 10GB disk space

### Windows Agent (Client PC)
- Windows 10 or higher
- Python 3.7+ (for Python agent)
- .NET Framework 4.7+ (for C# agent - future)
- Internet connection
- 100MB disk space

## Installation Steps

### 1. Database Setup

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE iwm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user (recommended)
CREATE USER 'iwm_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON iwm_db.* TO 'iwm_user'@'localhost';
FLUSH PRIVILEGES;

# Import schema
mysql -u iwm_user -p iwm_db < database/schema.sql
```

### 2. Backend API Setup

```bash
# Clone/upload project to server
cd /var/www/html
# Upload the IWM project

# Set permissions
sudo chown -R www-data:www-data /var/www/html/IWM
sudo chmod -R 755 /var/www/html/IWM

# Create upload directories
mkdir -p uploads/screenshots
chmod 755 uploads
chmod 755 uploads/screenshots

# Create logs directory
mkdir logs
chmod 755 logs
```

#### Configure Database Connection

Edit `backend/config/database.php` or set environment variables:

```bash
export DB_HOST=localhost
export DB_NAME=iwm_db
export DB_USER=iwm_user
export DB_PASS=your_secure_password
```

#### Configure Web Server

**Apache (.htaccess)**

Create `.htaccess` in backend directory:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

**Nginx**

Add to your nginx configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/IWM;
    
    index index.html index.php;
    
    location /backend {
        try_files $uri $uri/ /backend/index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Frontend Dashboard Setup

The frontend is already set up in the `frontend` directory. Simply access it through your browser:

```
http://your-domain.com/frontend/
```

Default login credentials:
- Email: `admin@iwm.local`
- Password: `admin123`

**⚠️ IMPORTANT: Change the default password immediately after first login!**

### 4. Windows Agent Setup

#### Python Agent Installation

1. Install Python 3.7+ from python.org
2. Install required packages:

```bash
pip install pywin32 psutil requests
```

3. Copy the agent to target PC:

```bash
# Copy agent/iwm_agent.py to PC
# e.g., C:\IWM\iwm_agent.py
```

4. Create configuration file `config.json`:

```json
{
    "api_key": "YOUR_API_KEY_FROM_DASHBOARD",
    "server_url": "http://your-domain.com/backend",
    "device_name": "PC-NAME",
    "heartbeat_interval": 300,
    "log_interval": 60
}
```

5. Get API Key from dashboard:
   - Login to dashboard
   - Go to Devices > Register Device
   - Fill in employee and device info
   - Copy the API key shown (only shown once!)

6. Run the agent:

```bash
python iwm_agent.py
```

7. (Optional) Set up as Windows service or startup application:

```bash
# Add to Windows startup folder
# C:\Users\USERNAME\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup
```

## First-Time Configuration

### 1. Login to Dashboard

Access: `http://your-domain.com/frontend/`

Use default credentials, then change password.

### 2. Add Employees

- Navigate to Employees menu
- Click "Add Employee"
- Fill in:
  - Name
  - Position
  - Work hours (default: 09:00 - 17:00)
  - Status (Active)

### 3. Register Devices

- Navigate to Devices menu
- Click "Register Device"
- Select employee
- Enter device name (e.g., "PC-John-Doe")
- Enter OS version (e.g., "Windows 10 Pro")
- Click Register
- **IMPORTANT**: Copy the API key immediately (it won't be shown again)

### 4. Install Agent on PC

- Install Python and dependencies
- Create config.json with the API key
- Run the agent
- Verify device shows "Online" in dashboard

## Security Recommendations

### 1. Change Default Credentials

```sql
-- In MySQL
UPDATE users SET password_hash = '$2y$10$NEW_HASH_HERE' WHERE email = 'admin@iwm.local';
```

Or change through PHP:
```php
<?php
echo password_hash('your_new_password', PASSWORD_DEFAULT);
?>
```

### 2. Use HTTPS

Install SSL certificate (Let's Encrypt recommended):

```bash
sudo apt-get install certbot python3-certbot-apache
sudo certbot --apache -d your-domain.com
```

### 3. Change JWT Secret

Edit `backend/config/config.php`:

```php
define('JWT_SECRET', 'your-random-secret-key-here');
```

Generate random key:
```bash
openssl rand -base64 32
```

### 4. Restrict Database Access

```sql
-- Only allow localhost connections
REVOKE ALL PRIVILEGES ON *.* FROM 'iwm_user'@'%';
GRANT ALL PRIVILEGES ON iwm_db.* TO 'iwm_user'@'localhost';
```

### 5. Set Up Firewall

```bash
# Allow only HTTP/HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

## Testing the System

### 1. Test API Connection

```bash
# Test heartbeat endpoint
curl -X POST http://your-domain.com/backend/api/device/heartbeat \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"cpu_usage": 25.5, "ram_usage": 60.2}'
```

### 2. Test Admin Login

```bash
# Login
curl -X POST http://your-domain.com/backend/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@iwm.local", "password": "admin123"}'
```

### 3. Verify Agent

- Run agent on PC
- Check dashboard for device status (should show "Online")
- Check dashboard for activity logs after a few minutes

## Troubleshooting

### Backend Issues

**Database connection error**
- Verify MySQL credentials in `backend/config/database.php`
- Check MySQL service is running: `sudo service mysql status`

**404 errors on API endpoints**
- Check web server configuration
- Verify .htaccess is working (Apache) or nginx config is correct
- Enable mod_rewrite: `sudo a2enmod rewrite`

### Frontend Issues

**Cannot login**
- Check browser console for errors
- Verify API URL in `frontend/js/app.js`
- Check CORS settings in `backend/config/config.php`

### Agent Issues

**Cannot connect to server**
- Verify server URL in config.json
- Check firewall allows outbound connections
- Test API endpoint manually with curl

**No activity logs**
- Check agent console for errors
- Verify API key is correct
- Check agent has permission to monitor windows

## Maintenance

### Database Cleanup

Clean old logs periodically:

```sql
-- Delete logs older than 90 days
DELETE FROM activity_sessions WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
DELETE FROM heartbeats WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
DELETE FROM idle_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Backup Database

```bash
# Daily backup
mysqldump -u iwm_user -p iwm_db > backup_$(date +%Y%m%d).sql

# Compress
gzip backup_$(date +%Y%m%d).sql
```

### Monitor Disk Space

```bash
# Check disk usage
df -h

# Check database size
du -sh /var/lib/mysql/iwm_db
```

## Support & Documentation

For issues, questions, or contributions:
- GitHub: https://github.com/Askarastudio/IWM
- Documentation: See `/docs` folder

## License

Copyright © 2026 INSIA Work Monitor
All rights reserved.
