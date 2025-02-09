import socket
import requests
import sys
import os
import re
from urllib.parse import urlparse
import ipaddress

ALLOWED_EXTENSIONS = {".jpg", ".jpeg", ".png", ".bmp", ".webp"}

BLOCKED_IP_RANGES = [
    ipaddress.ip_network("127.0.0.0/8"),   # Localhost
    ipaddress.ip_network("10.0.0.0/8"),    # Private network
    ipaddress.ip_network("172.16.0.0/12"), # Private network
    ipaddress.ip_network("192.168.0.0/16"),# Private network
    ipaddress.ip_network("169.254.0.0/16"),# Link-local
    ipaddress.ip_network("::1/128"),       # Localhost
    ipaddress.ip_network("fc00::/7"),      # Unique local address
    ipaddress.ip_network("fe80::/10")      # Link-local
]

def is_valid_url(url):
    """ Check if the URL is valid """
    parsed_url = urlparse(url)

    # Check if the URL has a valid scheme and extension
    if parsed_url.scheme not in ["http", "https"]:
        return False

    if not any(parsed_url.path.lower().endswith(ext) for ext in ALLOWED_EXTENSIONS):
        return False

    # Check if the hostname can be resolved to an IP address
    try:
        ip = socket.gethostbyname(parsed_url.hostname)
        ip_addr = ipaddress.ip_address(ip)
    except Exception:
        return False  

    # Check if the IP is not in a blocked range
    for blocked_range in BLOCKED_IP_RANGES:
        if ip_addr in blocked_range:
            return False

    return True

def sanitize_filename(filename):
    """ Remove all characters that are not letters, numbers, hyphens or underscores """
    return re.sub(r'[^a-zA-Z0-9_-]', '_', filename) 



# Get the url of the image from the command-line
url = sys.argv[1]
filename = sys.argv[2]
type = sys.argv[3]


# Check if the file already exists
if type == 'movie':
    if os.path.exists('../../img/Covers/movies/' + filename + '.jpg'):
        print('The file already exists')
        sys.exit(1)
elif type == 'serie':
    if os.path.exists('../../img/Covers/series/' + filename + '.jpg'):
        print('The file already exists')
        sys.exit(1)

# Get the image from the url
if not is_valid_url(url):
    print('Invalid URL')
    sys.exit(1)

img = requests.get(url, timeout=5, stream=True)

content_type = img.headers.get("Content-Type", "")
if not content_type.startswith("image/"):
    print("The URL does not point to an image")
    sys.exit(1)

if type == 'movie':

    filename = sanitize_filename(filename)

    name = filename + '.jpg'

    # Save the image in the directory ../../img/Covers/movies/ with os module
    with open('../../img/Covers/movies/' + name, 'wb') as f:
        f.write(img.content)
        
elif type == 'serie':
    filename = sanitize_filename(filename)

    name = filename + '.jpg'

    # Save the image in the directory ../../img/Covers/series/ with os module
    with open('../../img/Covers/series/' + name, 'wb') as f:
        f.write(img.content)
else:
    print('Invalid type')
    sys.exit(1)  