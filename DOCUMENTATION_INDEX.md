# 📖 IWM Documentation Index

Welcome to the INSIA Work Monitor (IWM) documentation hub. This index helps you find the right documentation for your needs.

---

## 🚀 Getting Started

### New to IWM?
Start here to understand what IWM is and get it running quickly:

1. **[README.md](README.md)** - Project overview, features, and introduction
   - What is IWM?
   - Key features
   - Technology stack
   - Quick overview

2. **[QUICKSTART.md](QUICKSTART.md)** - Get up and running in 5 minutes
   - Fastest way to deploy
   - Minimal configuration
   - Quick testing
   - Essential steps only

---

## 📚 Core Documentation

### Installation & Deployment

**[INSTALLATION.md](docs/INSTALLATION.md)** - Complete installation guide
- System requirements
- Database setup
- Backend configuration
- Frontend setup
- Agent installation
- Security recommendations
- Troubleshooting

*Use this when:* You're deploying IWM for the first time or setting up production

### Using the System

**[USER_MANUAL.md](docs/USER_MANUAL.md)** - Complete user guide
- Dashboard overview
- Employee management
- Device management
- Activity monitoring
- Reports & analytics
- Best practices
- FAQ

*Use this when:* You're an admin learning to use the dashboard

### API Integration

**[API.md](docs/API.md)** - API documentation
- Authentication methods
- All endpoints documented
- Request/response examples
- Error handling
- Code samples
- Best practices

*Use this when:* Integrating with the API or developing custom clients

---

## 🔍 Technical Documentation

### Implementation Details

**[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)** - Technical overview
- Architecture details
- Component breakdown
- Database schema
- Code structure
- Feature completeness
- Scalability notes

*Use this when:* You need technical details about the implementation

### Visual Guide

**[FEATURE_SHOWCASE.md](FEATURE_SHOWCASE.md)** - Visual feature showcase
- UI mockups
- Dashboard layouts
- API flow diagrams
- Database structure
- Usage examples
- Success metrics

*Use this when:* You want to see what the system looks like and how it works

---

## 📂 Documentation by Role

### 👨‍💼 For Administrators

**Primary Documents:**
1. [INSTALLATION.md](docs/INSTALLATION.md) - Set up the system
2. [USER_MANUAL.md](docs/USER_MANUAL.md) - Daily operations
3. [QUICKSTART.md](QUICKSTART.md) - Quick reference

**Key Sections:**
- Employee management
- Device registration
- Viewing reports
- System configuration
- Security best practices

### 👨‍💻 For Developers

**Primary Documents:**
1. [API.md](docs/API.md) - API integration
2. [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) - Architecture
3. [README.md](README.md) - Project structure

**Key Sections:**
- API endpoints
- Authentication
- Database schema
- Code organization
- Extension points

### 🔧 For IT/DevOps

**Primary Documents:**
1. [INSTALLATION.md](docs/INSTALLATION.md) - Deployment
2. [QUICKSTART.md](QUICKSTART.md) - Quick setup
3. Tools: `install.sh`, `check_config.php`

**Key Sections:**
- System requirements
- Server configuration
- Security setup
- Troubleshooting
- Maintenance

### 👥 For End Users (Employees)

**Primary Document:**
1. [USER_MANUAL.md](docs/USER_MANUAL.md) - Section: "For Employees"

**Key Sections:**
- What is tracked
- What is NOT tracked
- Privacy information
- Agent installation
- FAQ

---

## 🎯 Documentation by Task

### Setting Up IWM

| Task | Document | Section |
|------|----------|---------|
| Quick setup (5 min) | [QUICKSTART.md](QUICKSTART.md) | All |
| Full installation | [INSTALLATION.md](docs/INSTALLATION.md) | Installation Steps |
| Database setup | [INSTALLATION.md](docs/INSTALLATION.md) | Database Setup |
| Agent installation | [INSTALLATION.md](docs/INSTALLATION.md) | Windows Agent Setup |

### Using IWM

| Task | Document | Section |
|------|----------|---------|
| Login to dashboard | [USER_MANUAL.md](docs/USER_MANUAL.md) | Dashboard Overview |
| Add employee | [USER_MANUAL.md](docs/USER_MANUAL.md) | Employee Management |
| Register device | [USER_MANUAL.md](docs/USER_MANUAL.md) | Device Management |
| View activities | [USER_MANUAL.md](docs/USER_MANUAL.md) | Activity Monitoring |
| Generate reports | [USER_MANUAL.md](docs/USER_MANUAL.md) | Reports & Analytics |

### API Integration

| Task | Document | Section |
|------|----------|---------|
| Authentication | [API.md](docs/API.md) | Authentication |
| Send heartbeat | [API.md](docs/API.md) | Device Endpoints |
| Log activities | [API.md](docs/API.md) | Device Endpoints |
| Get reports | [API.md](docs/API.md) | Admin Endpoints |

### Troubleshooting

| Task | Document | Section |
|------|----------|---------|
| Installation issues | [INSTALLATION.md](docs/INSTALLATION.md) | Troubleshooting |
| API errors | [API.md](docs/API.md) | Error Responses |
| Agent problems | [USER_MANUAL.md](docs/USER_MANUAL.md) | Troubleshooting |
| Check system | Run `php check_config.php` | - |

---

## 📁 Additional Resources

### Configuration Files

- **[.env.example](.env.example)** - Environment configuration template
- **[agent/config.json.example](agent/config.json.example)** - Agent configuration template
- **[backend/.htaccess](backend/.htaccess)** - Apache configuration
- **[.gitignore](.gitignore)** - Git ignore rules

### Tools & Scripts

- **[install.sh](install.sh)** - Automated installation script
  ```bash
  sudo bash install.sh
  ```

- **[check_config.php](check_config.php)** - System configuration checker
  ```bash
  php check_config.php
  ```

### Source Code

- **[database/schema.sql](database/schema.sql)** - Database schema
- **[backend/](backend/)** - PHP API source code
- **[frontend/](frontend/)** - Web dashboard source code
- **[agent/iwm_agent.py](agent/iwm_agent.py)** - Windows agent source code

---

## 🔄 Quick Reference

### Common Commands

```bash
# Check system requirements
php check_config.php

# Install (automated)
sudo bash install.sh

# Import database schema
mysql -u root -p iwm_db < database/schema.sql

# Run agent
python agent/iwm_agent.py

# Check Apache syntax
apachectl configtest

# Restart services
sudo systemctl restart apache2
sudo systemctl restart mysql
```

### Important URLs

```
Dashboard:  http://your-domain.com/frontend/
API Base:   http://your-domain.com/backend/api/
Login:      http://your-domain.com/frontend/index.html
```

### Default Credentials

```
Email:    admin@iwm.local
Password: admin123

⚠️ CHANGE IMMEDIATELY AFTER FIRST LOGIN!
```

---

## 📊 Documentation Statistics

| Document | Size | Purpose |
|----------|------|---------|
| README.md | 9,165 chars | Project overview |
| QUICKSTART.md | 3,244 chars | Quick setup |
| INSTALLATION.md | 7,643 chars | Full installation |
| USER_MANUAL.md | 9,733 chars | User guide |
| API.md | 11,281 chars | API reference |
| IMPLEMENTATION_SUMMARY.md | 11,519 chars | Technical details |
| FEATURE_SHOWCASE.md | 15,360 chars | Visual guide |
| **Total** | **67,945 chars** | **Complete docs** |

---

## 🎓 Learning Path

### Beginner Path
1. Read [README.md](README.md) - Understand what IWM is
2. Follow [QUICKSTART.md](QUICKSTART.md) - Get it running
3. Review [FEATURE_SHOWCASE.md](FEATURE_SHOWCASE.md) - See what it looks like
4. Read [USER_MANUAL.md](docs/USER_MANUAL.md) - Learn to use it

### Administrator Path
1. Read [README.md](README.md) - Project overview
2. Follow [INSTALLATION.md](docs/INSTALLATION.md) - Full setup
3. Study [USER_MANUAL.md](docs/USER_MANUAL.md) - Daily operations
4. Review [Best Practices] sections - Optimize usage

### Developer Path
1. Read [README.md](README.md) - Architecture overview
2. Review [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) - Technical details
3. Study [API.md](docs/API.md) - API integration
4. Examine source code - Implementation details

---

## 🆘 Getting Help

### Documentation Issues
- Missing information? Check [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)
- Unclear instructions? See [USER_MANUAL.md](docs/USER_MANUAL.md) FAQ
- Technical questions? Review [API.md](docs/API.md)

### System Issues
- Installation problems? See [INSTALLATION.md](docs/INSTALLATION.md) Troubleshooting
- Configuration errors? Run `php check_config.php`
- Agent issues? Check [USER_MANUAL.md](docs/USER_MANUAL.md) Troubleshooting

### Contact & Support
- GitHub Issues: https://github.com/Askarastudio/IWM/issues
- Email: support@iwm.local (configure)
- Documentation: This index!

---

## 📝 Document Versions

All documentation is current as of:
- **Version**: 1.0.0
- **Date**: January 2, 2026
- **Status**: Complete & Production Ready

---

## ✅ Quick Checklist

Before deploying, make sure you've read:
- [ ] README.md (project overview)
- [ ] INSTALLATION.md (setup guide)
- [ ] USER_MANUAL.md (usage guide)
- [ ] Security sections in all docs

After deploying, make sure you've:
- [ ] Changed default admin password
- [ ] Configured HTTPS (production)
- [ ] Set up backups
- [ ] Tested all features
- [ ] Trained administrators

---

**Happy Monitoring! 🎉**

*For the latest documentation updates, check the repository: https://github.com/Askarastudio/IWM*
