# IWM API Documentation

## Base URL
```
http://your-domain.com/backend
```

## Authentication

### Admin Authentication (JWT)
Most admin endpoints require JWT authentication.

**Login**
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@iwm.local",
  "password": "admin123"
}
```

**Response**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "name": "Administrator",
      "email": "admin@iwm.local",
      "role": "owner"
    }
  }
}
```

**Using JWT Token**
```http
GET /api/admin/employees
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

### Device Authentication (API Key)
Device endpoints use API key authentication.

```http
POST /api/device/heartbeat
X-API-Key: your-device-api-key
Content-Type: application/json
```

## Device Endpoints

### Send Heartbeat
```http
POST /api/device/heartbeat
X-API-Key: your-device-api-key
Content-Type: application/json

{
  "cpu_usage": 45.5,
  "ram_usage": 62.3
}
```

**Response**
```json
{
  "success": true,
  "message": "Heartbeat recorded",
  "data": []
}
```

### Log Activities
```http
POST /api/device/logs/activity
X-API-Key: your-device-api-key
Content-Type: application/json

{
  "activities": [
    {
      "type": "APP",
      "app_name": "chrome.exe",
      "window_title": "GitHub - Microsoft Edge",
      "domain": null,
      "start_time": "2026-01-02T10:30:00",
      "end_time": "2026-01-02T10:35:00",
      "duration_seconds": 300,
      "is_work_hours": true
    },
    {
      "type": "WEB",
      "app_name": null,
      "window_title": null,
      "domain": "github.com",
      "start_time": "2026-01-02T10:35:00",
      "end_time": "2026-01-02T10:40:00",
      "duration_seconds": 300,
      "is_work_hours": true
    }
  ]
}
```

**Response**
```json
{
  "success": true,
  "message": "Activities logged successfully",
  "data": []
}
```

### Log Idle Time
```http
POST /api/device/logs/idle
X-API-Key: your-device-api-key
Content-Type: application/json

{
  "idle_logs": [
    {
      "start_time": "2026-01-02T11:00:00",
      "end_time": "2026-01-02T11:10:00",
      "idle_seconds": 600
    }
  ]
}
```

### Log USB Activity (Optional)
```http
POST /api/device/logs/usb
X-API-Key: your-device-api-key
Content-Type: application/json

{
  "usb_logs": [
    {
      "action": "PLUG",
      "vendor_id": "0x1234",
      "product_id": "0x5678",
      "serial": "ABC123XYZ",
      "device_info": "SanDisk USB Drive"
    }
  ]
}
```

## Admin Endpoints

All admin endpoints require JWT authentication via `Authorization: Bearer <token>` header.

### Employees

**List All Employees**
```http
GET /api/admin/employees
Authorization: Bearer <token>
```

**Response**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "position": "Software Developer",
      "work_start": "09:00:00",
      "work_end": "17:00:00",
      "status": "active",
      "device_count": 2,
      "online_devices": 1,
      "created_at": "2026-01-01 10:00:00"
    }
  ]
}
```

**Get Single Employee**
```http
GET /api/admin/employees/{id}
Authorization: Bearer <token>
```

**Create Employee**
```http
POST /api/admin/employees
Authorization: Bearer <token>
Content-Type: application/json

{
  "name": "John Doe",
  "position": "Software Developer",
  "work_start": "09:00:00",
  "work_end": "17:00:00",
  "status": "active"
}
```

**Update Employee**
```http
PUT /api/admin/employees/{id}
Authorization: Bearer <token>
Content-Type: application/json

{
  "name": "John Doe",
  "position": "Senior Developer",
  "work_start": "09:00:00",
  "work_end": "17:00:00",
  "status": "active"
}
```

**Delete Employee**
```http
DELETE /api/admin/employees/{id}
Authorization: Bearer <token>
```

### Devices

**List All Devices**
```http
GET /api/admin/devices
Authorization: Bearer <token>
```

**Response**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "device_name": "PC-Office-001",
      "os_version": "Windows 10 Pro",
      "agent_version": "1.0.0",
      "last_seen_at": "2026-01-02 14:30:00",
      "status": "online",
      "employee_name": "John Doe",
      "employee_position": "Software Developer"
    }
  ]
}
```

**Register Device**
```http
POST /api/admin/devices
Authorization: Bearer <token>
Content-Type: application/json

{
  "employee_id": 1,
  "device_name": "PC-Office-001",
  "os_version": "Windows 10 Pro",
  "agent_version": "1.0.0"
}
```

**Response**
```json
{
  "success": true,
  "message": "Device registered successfully",
  "data": {
    "id": 1,
    "api_key": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6"
  }
}
```

⚠️ **Important**: The API key is only returned once during registration. Store it securely.

**Reset API Key**
```http
POST /api/admin/devices/{id}/reset-api-key
Authorization: Bearer <token>
```

**Response**
```json
{
  "success": true,
  "message": "API key reset successfully",
  "data": {
    "id": 1,
    "api_key": "new-api-key-here"
  }
}
```

**Delete Device**
```http
DELETE /api/admin/devices/{id}
Authorization: Bearer <token>
```

### Activities

**Get Activities**
```http
GET /api/admin/activities?device_id=1&date=2026-01-02&type=APP
Authorization: Bearer <token>
```

**Query Parameters:**
- `device_id` (optional): Filter by device
- `employee_id` (optional): Filter by employee
- `date` (optional): Filter by date (YYYY-MM-DD)
- `type` (optional): Filter by type (APP or WEB)

**Response**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "device_id": 1,
      "type": "APP",
      "app_name": "chrome.exe",
      "window_title": "GitHub",
      "domain": null,
      "start_time": "2026-01-02 10:30:00",
      "end_time": "2026-01-02 10:35:00",
      "duration_seconds": 300,
      "is_work_hours": true
    }
  ]
}
```

### Reports

**Dashboard Statistics**
```http
GET /api/admin/reports/dashboard
Authorization: Bearer <token>
```

**Response**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "devices_online": 5,
    "total_employees": 10,
    "today_active_seconds": 14400,
    "today_idle_seconds": 3600
  }
}
```

**Daily Report**
```http
GET /api/admin/reports/daily?date=2026-01-02&employee_id=1
Authorization: Bearer <token>
```

**Query Parameters:**
- `date` (required): Report date (YYYY-MM-DD)
- `employee_id` (optional): Filter by employee

**Response**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "date": "2026-01-02",
    "active_time": {
      "total_seconds": 28800,
      "total_sessions": 120
    },
    "idle_time": {
      "total_seconds": 3600,
      "total_sessions": 15
    },
    "top_apps": [
      {
        "app_name": "chrome.exe",
        "total_seconds": 7200,
        "session_count": 25
      }
    ],
    "top_domains": [
      {
        "domain": "github.com",
        "total_seconds": 5400,
        "session_count": 18
      }
    ]
  }
}
```

**Employee Report**
```http
GET /api/admin/reports/employee/{id}?from=2026-01-01&to=2026-01-07
Authorization: Bearer <token>
```

**Query Parameters:**
- `from` (optional): Start date (default: 7 days ago)
- `to` (optional): End date (default: today)

**Response**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "employee": {
      "id": 1,
      "name": "John Doe",
      "position": "Software Developer"
    },
    "period": {
      "from": "2026-01-01",
      "to": "2026-01-07"
    },
    "summary": {
      "total_active_seconds": 144000,
      "days_worked": 5,
      "devices_used": 2
    },
    "daily_breakdown": [
      {
        "date": "2026-01-02",
        "active_seconds": 28800,
        "session_count": 120
      }
    ]
  }
}
```

**Top Applications**
```http
GET /api/admin/reports/top-apps?from=2026-01-01&to=2026-01-07&limit=20
Authorization: Bearer <token>
```

**Query Parameters:**
- `from` (optional): Start date (default: 7 days ago)
- `to` (optional): End date (default: today)
- `limit` (optional): Number of results (default: 20)

**Response**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "app_name": "chrome.exe",
      "total_seconds": 50400,
      "session_count": 156,
      "device_count": 8
    }
  ]
}
```

**Top Domains**
```http
GET /api/admin/reports/top-domains?from=2026-01-01&to=2026-01-07&limit=20
Authorization: Bearer <token>
```

**Query Parameters:**
- `from` (optional): Start date (default: 7 days ago)
- `to` (optional): End date (default: today)
- `limit` (optional): Number of results (default: 20)

**Response**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "domain": "github.com",
      "total_seconds": 36000,
      "session_count": 98,
      "device_count": 6
    }
  ]
}
```

## Error Responses

All endpoints return errors in the following format:

```json
{
  "success": false,
  "message": "Error message",
  "errors": []
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `500` - Internal Server Error

**Example Error Response:**
```json
{
  "success": false,
  "message": "Invalid credentials",
  "errors": []
}
```

## Rate Limiting

API rate limiting is configured but not strictly enforced by default. The default limit is 100 requests per minute per IP address.

## Data Types

### Activity Types
- `APP` - Application activity
- `WEB` - Web browsing activity

### Device Status
- `online` - Device is actively sending data
- `offline` - Device hasn't sent data recently

### Employee Status
- `active` - Employee is active in the system
- `inactive` - Employee is inactive

### USB Actions
- `PLUG` - USB device plugged in
- `UNPLUG` - USB device removed

## Best Practices

1. **API Keys**: Store API keys securely, never commit to version control
2. **JWT Tokens**: Tokens expire after 24 hours, implement refresh logic
3. **Batch Requests**: Send activities in batches (1-5 minutes) to reduce API calls
4. **Error Handling**: Implement retry logic for failed requests
5. **Offline Mode**: Buffer data locally when network is unavailable
6. **HTTPS**: Always use HTTPS in production

## Examples

### Python Example (Device Agent)

```python
import requests
import json

API_KEY = "your-device-api-key"
SERVER_URL = "https://your-domain.com/backend"

# Send heartbeat
response = requests.post(
    f"{SERVER_URL}/api/device/heartbeat",
    headers={'X-API-Key': API_KEY},
    json={
        'cpu_usage': 45.5,
        'ram_usage': 62.3
    }
)

print(response.json())
```

### JavaScript Example (Admin Dashboard)

```javascript
const API_URL = 'https://your-domain.com/backend';
const token = localStorage.getItem('token');

// Get employees
fetch(`${API_URL}/api/admin/employees`, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  console.log(data);
});
```

## Support

For API issues or questions:
- GitHub Issues: https://github.com/Askarastudio/IWM/issues
- Documentation: `/docs` folder

---

*API Version: 1.0.0*
*Last Updated: January 2, 2026*
