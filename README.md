# INSIA Work Monitor (IWM)

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![License](https://img.shields.io/badge/license-Proprietary-red)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1)
![Python](https://img.shields.io/badge/Python-3.7%2B-3776AB)

**INSIA Work Monitor (IWM)** is a comprehensive employee monitoring and productivity tracking system designed for organizations to monitor workplace activity while respecting employee privacy.

## 🎯 Features

### Core Monitoring
- ✅ **Application Usage Tracking** - Monitor which applications employees use and for how long
- ✅ **Web Activity Monitoring** - Track website domains (not content) accessed during work hours
- ✅ **Active vs Idle Time** - Measure productive work time vs idle periods
- ✅ **Real-time Device Status** - See which devices are online/offline
- ✅ **Work Hours Tracking** - Configurable work schedules per employee

### Security & Audit
- ✅ **USB Activity Logging** (optional) - Track USB device connections
- ✅ **File Activity Monitoring** (optional) - Log file operations
- ✅ **Screenshot Capture** (optional) - Periodic screenshots with notification

### Reports & Analytics
- ✅ **Daily Reports** - Comprehensive daily activity summaries
- ✅ **Employee Reports** - Individual productivity analytics
- ✅ **Top Apps/Domains** - Most used applications and websites
- ✅ **Custom Date Ranges** - Flexible reporting periods
- ✅ **Export Capabilities** - PDF, Excel, CSV exports (planned)

### Privacy & Ethics
- ❌ **NO Keylogging** - Does not capture keystrokes
- ❌ **NO Password Capture** - Does not intercept credentials
- ❌ **NO Personal Data** - Does not access emails, chats, or personal files
- ❌ **NO Camera/Mic Recording** - Respects employee privacy
- ✅ **Transparent Monitoring** - Employees know what is tracked

## 🏗️ Architecture

```
┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐
│  Windows Agent  │────────▶│   API Server    │◀────────│  Web Dashboard  │
│   (Client PC)   │         │  (PHP/MySQL)    │         │  (Admin Panel)  │
└─────────────────┘         └─────────────────┘         └─────────────────┘
     Monitor                    Store & Process              View & Analyze
     - Apps                     - REST API                   - Reports
     - Domains                  - JWT Auth                   - Charts
     - Idle time                - API Key Auth               - CRUD
     - USB (opt)                - MySQL DB                   - Export
```

### Components

1. **Windows Agent** (`/agent`)
   - Background service running on employee PCs
   - Monitors active windows, applications, and web activity
   - Buffers data offline and syncs when connected
   - Sends heartbeats and activity logs to server

2. **API Server** (`/backend`)
   - PHP-based REST API
   - MySQL database for data storage
   - JWT authentication for admins
   - API key authentication for devices
   - Activity logging and reporting endpoints

3. **Web Dashboard** (`/frontend`)
   - Modern responsive web interface
   - Employee and device management
   - Real-time monitoring and analytics
   - Comprehensive reporting system

## 📋 Requirements

### Server (VPS/Cloud)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Minimum 1GB RAM
- 10GB disk space

### Client (Employee PC)
- Windows 10 or higher
- Python 3.7+ (for agent)
- Internet connection
- 100MB disk space

## 🚀 Quick Start

### 1. Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE iwm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Import schema
mysql -u root -p iwm_db < database/schema.sql
```

### 2. Backend Configuration

```bash
# Configure database connection
# Edit backend/config/database.php or set environment variables:
export DB_HOST=localhost
export DB_NAME=iwm_db
export DB_USER=your_user
export DB_PASS=your_password
```

### 3. Access Dashboard

```
URL: http://your-domain.com/frontend/
Default Login:
  Email: admin@iwm.local
  Password: admin123
```

⚠️ **Change default password immediately!**

### 4. Register Device & Install Agent

1. Login to dashboard
2. Add employee (Employees menu)
3. Register device (Devices menu)
4. Copy API key (shown only once!)
5. Install agent on PC:

```bash
# Install Python dependencies
pip install pywin32 psutil requests

# Create config.json
{
  "api_key": "YOUR_API_KEY",
  "server_url": "http://your-domain.com/backend"
}

# Run agent
python agent/iwm_agent.py
```

## 📚 Documentation

- **[Installation Guide](docs/INSTALLATION.md)** - Detailed setup instructions
- **[User Manual](docs/USER_MANUAL.md)** - How to use the system
- **[API Documentation](docs/API.md)** - API endpoints and examples

## 🗂️ Project Structure

```
IWM/
├── agent/                  # Windows monitoring agent
│   ├── iwm_agent.py       # Python agent
│   └── config.json        # Agent configuration
├── backend/               # PHP API server
│   ├── config/           # Configuration files
│   ├── controllers/      # API controllers
│   ├── models/           # Data models
│   ├── middleware/       # Auth middleware
│   ├── utils/            # Helper functions
│   └── index.php         # API router
├── frontend/              # Web dashboard
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript
│   ├── dashboard.html    # Main dashboard
│   ├── employees.html    # Employee management
│   ├── devices.html      # Device management
│   ├── activities.html   # Activity monitoring
│   └── reports.html      # Reports & analytics
├── database/              # Database files
│   └── schema.sql        # Database schema
└── docs/                  # Documentation
    ├── INSTALLATION.md   # Installation guide
    ├── USER_MANUAL.md    # User manual
    └── API.md            # API documentation
```

## 🔒 Security

- **JWT Authentication** for admin dashboard
- **API Key Authentication** for devices
- **Password Hashing** using bcrypt
- **SQL Injection Protection** via prepared statements
- **HTTPS Support** (recommended for production)
- **Rate Limiting** for API endpoints

### Security Best Practices

1. Change default admin password
2. Use HTTPS in production
3. Keep API keys secure
4. Regular database backups
5. Update dependencies regularly
6. Restrict database access
7. Enable firewall rules

## 🎨 Screenshots

### Dashboard
![Dashboard](https://via.placeholder.com/800x400/667eea/ffffff?text=Dashboard+Overview)

### Employee Management
![Employees](https://via.placeholder.com/800x400/28a745/ffffff?text=Employee+Management)

### Reports
![Reports](https://via.placeholder.com/800x400/ffc107/ffffff?text=Reports+%26+Analytics)

## 🤝 Contributing

This is a proprietary project. For bug reports or feature requests, please contact the development team.

## 📄 License

Copyright © 2026 INSIA Work Monitor. All rights reserved.

This software is proprietary and confidential. Unauthorized copying, distribution, or use is strictly prohibited.

## ⚖️ Legal & Compliance

**Important**: Before deploying this system:

1. **Inform Employees** - Transparency is legally required in most jurisdictions
2. **Obtain Consent** - Get written consent where necessary
3. **Define Purpose** - Clearly state why monitoring is needed
4. **Limit Scope** - Only collect necessary data
5. **Data Protection** - Comply with GDPR, local privacy laws
6. **Employee Rights** - Allow data access upon request

### Recommended Use

- ✅ Productivity improvement
- ✅ Security auditing
- ✅ Resource optimization
- ✅ Performance analytics
- ❌ Employee harassment
- ❌ Privacy invasion
- ❌ Micromanagement

## 📞 Support

- **Documentation**: See `/docs` folder
- **Issues**: GitHub Issues
- **Email**: support@iwm.local (configure)

## 🗺️ Roadmap

### Version 1.1 (Planned)
- [ ] Export reports to PDF/Excel
- [ ] Email notifications
- [ ] Employee self-service portal
- [ ] Mobile app for monitoring
- [ ] Advanced analytics with charts
- [ ] Custom alert rules

### Version 2.0 (Future)
- [ ] Multi-tenant support
- [ ] Machine learning insights
- [ ] Integration with HR systems
- [ ] Compliance reporting
- [ ] Advanced screenshot management
- [ ] Linux/Mac agent support

## 🙏 Acknowledgments

- **AdminLTE** - Dashboard template inspiration
- **Bootstrap 5** - UI framework
- **DataTables** - Table functionality
- **Font Awesome** - Icons
- **Chart.js** - Charts (planned)

---

**Version**: 1.0.0  
**Release Date**: January 2, 2026  
**Status**: Production Ready

Made with ❤️ for workplace productivity and transparency
