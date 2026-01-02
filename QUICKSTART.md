# Quick Start Guide - INSIA Work Monitor (IWM)

## 5-Minute Setup

### Prerequisites
- Linux server with Apache/Nginx
- PHP 7.4+, MySQL 5.7+
- Windows PC for agent

### Step 1: Install on Server (5 minutes)

```bash
# 1. Upload/clone IWM to your server
cd /var/www/html/IWM

# 2. Run installation script
sudo bash install.sh

# Follow the prompts to:
# - Create database
# - Configure credentials
# - Set permissions
```

### Step 2: Access Dashboard (1 minute)

```
1. Open browser: http://your-domain.com/frontend/
2. Login: admin@iwm.local / admin123
3. Change password immediately!
```

### Step 3: Add Employee (1 minute)

```
1. Click "Employees" menu
2. Click "Add Employee"
3. Fill in:
   - Name: John Doe
   - Position: Developer
   - Work Hours: 09:00 - 17:00
4. Click Save
```

### Step 4: Register Device (2 minutes)

```
1. Click "Devices" menu
2. Click "Register Device"
3. Select employee: John Doe
4. Device name: PC-JohnDoe-Office
5. Click Register
6. ⚠️ COPY THE API KEY (shown only once!)
```

### Step 5: Install Agent on PC (5 minutes)

```bash
# On Windows PC:

# 1. Install Python 3.7+
Download from: https://www.python.org/downloads/

# 2. Install dependencies
pip install pywin32 psutil requests

# 3. Download agent
# Copy agent/iwm_agent.py to C:\IWM\

# 4. Create config file (C:\IWM\config.json)
{
    "api_key": "YOUR_API_KEY_HERE",
    "server_url": "http://your-domain.com/backend",
    "device_name": "PC-JohnDoe-Office"
}

# 5. Run agent
cd C:\IWM
python iwm_agent.py
```

### Step 6: Verify (2 minutes)

```
1. Check dashboard - device should show "Online"
2. Wait 2-3 minutes
3. Check "Activities" menu for logs
4. Check "Dashboard" for statistics
```

## Done! 🎉

Your IWM system is now running!

### What's Next?

- Add more employees and devices
- Explore reports and analytics
- Configure work hours per employee
- Review activity logs
- Generate productivity reports

### Optional: Run Agent as Service

Windows Startup:
```
1. Press Win+R, type: shell:startup
2. Create shortcut to: python C:\IWM\iwm_agent.py
3. Agent will start on Windows boot
```

### Need Help?

- Full Installation Guide: `docs/INSTALLATION.md`
- User Manual: `docs/USER_MANUAL.md`
- API Documentation: `docs/API.md`
- Configuration Check: `php check_config.php`

## Troubleshooting

**Can't login?**
- Check browser console (F12)
- Verify backend URL in `frontend/js/app.js`
- Check Apache/Nginx is running

**Database error?**
- Verify credentials in `backend/config/database.php`
- Check MySQL is running: `sudo service mysql status`
- Import schema: `mysql -u root -p iwm_db < database/schema.sql`

**Agent can't connect?**
- Check server URL in config.json
- Verify API key is correct
- Test API: `curl http://your-domain.com/backend/api/device/heartbeat`
- Check firewall allows outbound HTTP

**No activity logs?**
- Wait 2-3 minutes for first sync
- Check agent console for errors
- Verify device shows "Online" in dashboard
- Run agent with: `python iwm_agent.py` to see output

## Security Checklist

✅ Change default admin password
✅ Use HTTPS in production  
✅ Keep API keys secure  
✅ Regular database backups  
✅ Update dependencies  
✅ Enable firewall  
✅ Restrict database access  

---

**Ready to monitor!** 🚀
