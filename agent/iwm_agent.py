"""
INSIA Work Monitor - Windows Agent
Version: 1.0.0

This is a Python-based Windows monitoring agent that tracks:
- Active applications
- Browser domains
- Idle time
- USB activity (optional)

Installation Requirements:
- Python 3.7+
- pip install pywin32 psutil requests

Configuration:
- Create a config.json file with device API key
- Run as a service or startup application
"""

import win32gui
import win32process
import psutil
import time
import json
import requests
import threading
from datetime import datetime
from collections import defaultdict
import ctypes
import os

class IWMAgent:
    def __init__(self, config_file='config.json'):
        self.load_config(config_file)
        self.activity_buffer = []
        self.idle_buffer = []
        self.last_activity_time = time.time()
        self.idle_threshold = 300  # 5 minutes
        self.running = True
        
    def load_config(self, config_file):
        """Load configuration from JSON file"""
        if not os.path.exists(config_file):
            print(f"Config file {config_file} not found. Creating template...")
            self.create_config_template(config_file)
            print(f"Please edit {config_file} with your API key and server URL")
            exit(1)
            
        with open(config_file, 'r') as f:
            config = json.load(f)
            
        self.api_key = config.get('api_key', '')
        self.server_url = config.get('server_url', 'http://localhost/backend')
        self.device_name = config.get('device_name', os.environ.get('COMPUTERNAME', 'Unknown'))
        self.heartbeat_interval = config.get('heartbeat_interval', 300)  # 5 minutes
        self.log_interval = config.get('log_interval', 60)  # 1 minute
        
    def create_config_template(self, config_file):
        """Create a configuration template"""
        template = {
            "api_key": "YOUR_API_KEY_HERE",
            "server_url": "http://your-server.com/backend",
            "device_name": os.environ.get('COMPUTERNAME', 'Unknown'),
            "heartbeat_interval": 300,
            "log_interval": 60
        }
        
        with open(config_file, 'w') as f:
            json.dump(template, f, indent=4)
    
    def get_active_window(self):
        """Get currently active window information"""
        try:
            window = win32gui.GetForegroundWindow()
            window_title = win32gui.GetWindowText(window)
            
            _, pid = win32process.GetWindowThreadProcessId(window)
            process = psutil.Process(pid)
            app_name = process.name()
            
            return {
                'app_name': app_name,
                'window_title': window_title
            }
        except Exception as e:
            return None
    
    def extract_domain_from_browser(self, window_title, app_name):
        """Extract domain from browser window title"""
        browsers = ['chrome.exe', 'firefox.exe', 'msedge.exe', 'iexplore.exe', 'opera.exe']
        
        if app_name.lower() not in browsers:
            return None
        
        # Simple domain extraction from title
        # Format usually: "Page Title - Domain - Browser"
        parts = window_title.split(' - ')
        if len(parts) > 1:
            # Try to find domain-like string
            for part in parts:
                if '.' in part and ' ' not in part:
                    # Remove protocol if present
                    domain = part.replace('https://', '').replace('http://', '')
                    domain = domain.split('/')[0]  # Remove path
                    return domain
        
        return None
    
    def is_work_hours(self):
        """Check if current time is within work hours (9AM - 5PM)"""
        current_hour = datetime.now().hour
        return 9 <= current_hour < 17
    
    def get_idle_time(self):
        """Get system idle time in seconds (Windows specific)"""
        class LASTINPUTINFO(ctypes.Structure):
            _fields_ = [
                ('cbSize', ctypes.c_uint),
                ('dwTime', ctypes.c_uint),
            ]
        
        lii = LASTINPUTINFO()
        lii.cbSize = ctypes.sizeof(LASTINPUTINFO)
        ctypes.windll.user32.GetLastInputInfo(ctypes.byref(lii))
        
        millis = ctypes.windll.kernel32.GetTickCount() - lii.dwTime
        return millis / 1000.0
    
    def get_system_info(self):
        """Get CPU and RAM usage"""
        return {
            'cpu_usage': psutil.cpu_percent(interval=1),
            'ram_usage': psutil.virtual_memory().percent
        }
    
    def monitor_activity(self):
        """Main monitoring loop"""
        current_activity = None
        activity_start = None
        
        while self.running:
            try:
                # Get current window
                window_info = self.get_active_window()
                
                if window_info:
                    # Check for domain
                    domain = self.extract_domain_from_browser(
                        window_info['window_title'],
                        window_info['app_name']
                    )
                    
                    activity_type = 'WEB' if domain else 'APP'
                    activity_key = domain if domain else window_info['app_name']
                    
                    # If activity changed, save previous
                    if current_activity != activity_key:
                        if current_activity and activity_start:
                            self.save_activity(
                                current_activity,
                                activity_start,
                                time.time(),
                                'WEB' if domain else 'APP',
                                window_info['window_title'] if not domain else None,
                                domain
                            )
                        
                        current_activity = activity_key
                        activity_start = time.time()
                
                # Check idle time
                idle_time = self.get_idle_time()
                if idle_time > self.idle_threshold:
                    # User is idle
                    pass
                else:
                    self.last_activity_time = time.time()
                
                time.sleep(5)  # Check every 5 seconds
                
            except Exception as e:
                print(f"Error in monitor_activity: {e}")
                time.sleep(5)
    
    def save_activity(self, activity_key, start_time, end_time, activity_type, window_title=None, domain=None):
        """Save activity to buffer"""
        duration = int(end_time - start_time)
        
        activity = {
            'type': activity_type,
            'app_name': activity_key if activity_type == 'APP' else None,
            'domain': domain,
            'window_title': window_title,
            'start_time': datetime.fromtimestamp(start_time).isoformat(),
            'end_time': datetime.fromtimestamp(end_time).isoformat(),
            'duration_seconds': duration,
            'is_work_hours': self.is_work_hours()
        }
        
        self.activity_buffer.append(activity)
    
    def send_heartbeat(self):
        """Send heartbeat to server"""
        while self.running:
            try:
                system_info = self.get_system_info()
                
                response = requests.post(
                    f"{self.server_url}/api/device/heartbeat",
                    headers={'X-API-Key': self.api_key},
                    json=system_info,
                    timeout=10
                )
                
                if response.status_code == 200:
                    print(f"[{datetime.now()}] Heartbeat sent successfully")
                else:
                    print(f"[{datetime.now()}] Heartbeat failed: {response.status_code}")
                    
            except Exception as e:
                print(f"Error sending heartbeat: {e}")
            
            time.sleep(self.heartbeat_interval)
    
    def send_logs(self):
        """Send activity logs to server"""
        while self.running:
            try:
                time.sleep(self.log_interval)
                
                if len(self.activity_buffer) > 0:
                    # Send activities
                    activities = self.activity_buffer.copy()
                    self.activity_buffer.clear()
                    
                    response = requests.post(
                        f"{self.server_url}/api/device/logs/activity",
                        headers={'X-API-Key': self.api_key},
                        json={'activities': activities},
                        timeout=10
                    )
                    
                    if response.status_code == 200:
                        print(f"[{datetime.now()}] Sent {len(activities)} activities")
                    else:
                        print(f"[{datetime.now()}] Failed to send activities: {response.status_code}")
                        # Put back in buffer on failure
                        self.activity_buffer.extend(activities)
                        
            except Exception as e:
                print(f"Error sending logs: {e}")
    
    def start(self):
        """Start the monitoring agent"""
        print(f"Starting IWM Agent...")
        print(f"Device: {self.device_name}")
        print(f"Server: {self.server_url}")
        
        # Start threads
        monitor_thread = threading.Thread(target=self.monitor_activity, daemon=True)
        heartbeat_thread = threading.Thread(target=self.send_heartbeat, daemon=True)
        logs_thread = threading.Thread(target=self.send_logs, daemon=True)
        
        monitor_thread.start()
        heartbeat_thread.start()
        logs_thread.start()
        
        print("Agent started. Press Ctrl+C to stop.")
        
        try:
            while True:
                time.sleep(1)
        except KeyboardInterrupt:
            print("\nStopping agent...")
            self.running = False
            time.sleep(2)
            print("Agent stopped.")

if __name__ == '__main__':
    agent = IWMAgent()
    agent.start()
