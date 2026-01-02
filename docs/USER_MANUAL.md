# INSIA Work Monitor (IWM) - User Manual

## Table of Contents
1. [Introduction](#introduction)
2. [Dashboard Overview](#dashboard-overview)
3. [Employee Management](#employee-management)
4. [Device Management](#device-management)
5. [Activity Monitoring](#activity-monitoring)
6. [Reports & Analytics](#reports--analytics)
7. [Best Practices](#best-practices)

## Introduction

INSIA Work Monitor (IWM) is a comprehensive employee monitoring system designed to help organizations:
- Track application usage and productivity
- Monitor web browsing activity (domains only, not content)
- Measure active vs. idle time
- Generate productivity reports
- Ensure workplace security

### What IWM Does ✅
- Tracks active applications and their usage duration
- Records accessed website domains (e.g., youtube.com, google.com)
- Monitors work hours (active vs. idle time)
- Provides productivity analytics and reports
- Optional: USB device activity logging
- Optional: Periodic screenshots (with notification)

### What IWM Does NOT Do ❌
- Does NOT record keystrokes (not a keylogger)
- Does NOT capture passwords or OTP codes
- Does NOT record camera or microphone
- Does NOT read chat messages or email content
- Does NOT access personal data

## Dashboard Overview

The dashboard provides a quick overview of your monitoring system.

### Main Statistics
- **Devices Online**: Number of currently connected devices
- **Total Employees**: Active employees in the system
- **Active Time Today**: Total productive time across all employees
- **Idle Time Today**: Total idle time across all employees

### Charts
- **Top Applications**: Most used applications today
- **Top Domains**: Most visited websites today

## Employee Management

### Adding an Employee

1. Navigate to **Employees** menu
2. Click **Add Employee** button
3. Fill in the form:
   - **Name** (required): Employee's full name
   - **Position**: Job title/position
   - **Work Start**: Work day start time (default: 09:00)
   - **Work End**: Work day end time (default: 17:00)
   - **Status**: Active or Inactive
4. Click **Save**

### Editing an Employee

1. Go to **Employees** menu
2. Click the **Edit** (pencil) icon next to the employee
3. Modify the information
4. Click **Save**

### Viewing Employee Details

The employee list shows:
- Employee name and position
- Work hours schedule
- Number of registered devices
- Number of online devices
- Current status

### Deleting an Employee

⚠️ **Warning**: Deleting an employee will also delete all associated devices and activity logs.

1. Go to **Employees** menu
2. Click the **Delete** (trash) icon
3. Confirm the deletion

## Device Management

### Registering a New Device

1. Navigate to **Devices** menu
2. Click **Register Device** button
3. Fill in the form:
   - **Employee** (required): Select the device owner
   - **Device Name** (required): e.g., "PC-Office-John"
   - **OS Version**: e.g., "Windows 10 Pro"
   - **Agent Version**: e.g., "1.0.0"
4. Click **Register**
5. **IMPORTANT**: Copy the API key displayed (it will only be shown once!)
6. Use this API key to configure the agent on the PC

### Viewing Devices

The device list shows:
- Device name and ID
- Employee owner
- Operating system
- Last seen time
- Status (Online/Offline)

A device is considered:
- **Online**: If it sent a heartbeat in the last 10 minutes
- **Offline**: If no heartbeat received for more than 10 minutes

### Resetting API Key

If an API key is compromised or lost:

1. Go to **Devices** menu
2. Click the **Key** icon next to the device
3. Confirm the reset
4. Copy the new API key
5. Update the agent configuration on the PC

⚠️ **Note**: The old API key will stop working immediately.

### Deleting a Device

1. Go to **Devices** menu
2. Click the **Delete** (trash) icon
3. Confirm the deletion

## Activity Monitoring

### Viewing Activities

1. Navigate to **Activities** menu
2. Use filters to narrow down results:
   - **Employee**: Filter by specific employee
   - **Device**: Filter by specific device
   - **Date**: Select date to view
   - **Type**: Filter by App or Web activities
3. Click **Search**

### Activity Information

Each activity record shows:
- **Type**: APP (Application) or WEB (Website)
- **Application/Domain**: Name of app or website domain
- **Window Title**: Active window title (for apps)
- **Start Time**: When activity started
- **End Time**: When activity ended
- **Duration**: How long the activity lasted
- **Work Hours**: Whether activity was during work hours

### Understanding Activity Types

**APP (Application)**
- Shows application executable name (e.g., "WINWORD.EXE", "excel.exe")
- Window title may show document name
- Duration shows how long the app was active

**WEB (Website)**
- Shows only the domain (e.g., "youtube.com", "github.com")
- Does NOT show specific pages, URLs, or content
- Duration shows time spent on that domain

## Reports & Analytics

### Daily Report

View comprehensive daily statistics:

1. Navigate to **Reports** menu
2. Select **Daily Report** section
3. Choose date and optional employee
4. Click **Generate**

Report shows:
- Total active time and sessions
- Total idle time and sessions
- Top 10 applications used
- Top 10 domains visited

### Employee Report

View individual employee productivity:

1. Navigate to **Reports** menu
2. Select **Employee Report** section
3. Choose employee and date range
4. Click **Generate**

Report shows:
- Total active time in period
- Number of days worked
- Number of devices used
- Daily breakdown of activity

### Top Applications Report

View most used applications across all employees:

1. Navigate to **Reports** menu
2. Select **Top Applications** section
3. Choose date range
4. Click **Load**

Shows top 10 applications with:
- Total usage time
- Number of sessions
- Number of devices using it

### Top Domains Report

View most visited websites across all employees:

1. Navigate to **Reports** menu
2. Select **Top Domains** section
3. Choose date range
4. Click **Load**

Shows top 10 domains with:
- Total time spent
- Number of visits
- Number of devices accessing it

## Best Practices

### For Administrators

1. **Regular Monitoring**
   - Check dashboard daily
   - Review weekly reports
   - Look for unusual patterns

2. **Employee Communication**
   - Inform employees about monitoring
   - Explain what is tracked and why
   - Be transparent about data usage

3. **Data Retention**
   - Clean old logs periodically (suggested: 90 days)
   - Backup important reports
   - Maintain audit trail

4. **Security**
   - Keep API keys secure
   - Change default admin password
   - Use HTTPS in production
   - Review device list regularly

5. **Fair Usage**
   - Use data for productivity improvement, not punishment
   - Respect employee privacy
   - Focus on patterns, not micromanagement

### For Employees

1. **Understand Monitoring**
   - Know what is being tracked
   - Ask questions if unclear
   - Report any issues with the agent

2. **Device Status**
   - Ensure agent is running
   - Check device shows as "Online" in dashboard
   - Report connectivity issues

3. **Productivity Tips**
   - Review your own activity logs
   - Identify time-wasting patterns
   - Adjust work habits accordingly

### Data Privacy

IWM is designed with privacy in mind:
- Only tracks application names and website domains
- Does NOT capture personal data or communications
- Does NOT monitor off-work hours (configurable)
- Follows principle of minimum necessary data collection

### Compliance

Ensure compliance with local regulations:
- Inform employees about monitoring (required in most jurisdictions)
- Obtain consent where necessary
- Use data only for stated purposes
- Provide data access to employees upon request

## Troubleshooting

### Device Shows Offline

**Possible causes:**
- Agent not running on PC
- Network connectivity issues
- Incorrect API key
- Firewall blocking connection

**Solutions:**
1. Check if agent is running on the PC
2. Verify network connectivity
3. Check agent logs for errors
4. Test API endpoint manually

### No Activity Logs

**Possible causes:**
- Agent not monitoring properly
- Agent lacks permissions
- API communication failure

**Solutions:**
1. Run agent with administrator privileges
2. Check agent console for errors
3. Verify API key is correct
4. Check server logs

### Inaccurate Time Tracking

**Possible causes:**
- System clock synchronization issues
- Agent restart without proper shutdown
- Network delays

**Solutions:**
1. Sync system time with NTP server
2. Ensure proper agent shutdown/restart procedures
3. Check server-client time difference

## FAQ

**Q: Can employees see their own data?**
A: Currently, only administrators can access the dashboard. Employee self-service can be added as a feature.

**Q: How often is data sent to the server?**
A: By default, every 1-5 minutes (configurable).

**Q: What happens if internet is down?**
A: The agent buffers data locally and sends it when connection is restored.

**Q: Can I export reports?**
A: Yes, reports can be exported to Excel, PDF, or CSV (feature to be implemented in next version).

**Q: Is screenshot feature enabled by default?**
A: No, screenshots are optional and disabled by default. If enabled, users will be notified.

**Q: How long is data retained?**
A: By default, indefinitely. Administrators should set up periodic cleanup based on requirements.

## Support

For technical support or questions:
- Check the documentation in `/docs` folder
- Review installation guide: `docs/INSTALLATION.md`
- Check API documentation: `docs/API.md`
- GitHub Issues: https://github.com/Askarastudio/IWM/issues

---

*Last updated: January 2, 2026*
*Version: 1.0.0*
