import json
import os
import concurrent.futures
from urllib.parse import urljoin
from bs4 import BeautifulSoup
import requests

BASE_URL = "https://www.footybite.army"

def download_image(url_path):
    if not url_path: return
    if not url_path.startswith("http"):
        full_url = urljoin(BASE_URL, url_path)
    else:
        full_url = url_path
        
    local_path = url_path.lstrip("/")
    if not local_path: return
        
    os.makedirs(os.path.dirname(local_path), exist_ok=True)
    if os.path.exists(local_path): return
        
    try:
        r = requests.get(full_url, timeout=10)
        r.raise_for_status()
        with open(local_path, "wb") as f:
            f.write(r.content)
        print(f"Downloaded -> {local_path}")
    except Exception as e:
        print(f"Failed {full_url}: {e}")

def main():
    try:
        with open("view-source_https___www.footybite.army.html", "r", encoding="utf-8") as f:
            html = f.read()
    except FileNotFoundError:
        print("HTML file not found.")
        return

    soup = BeautifulSoup(html, "html.parser")
    
    images_to_download = set()
    
    # Get all images
    for img in soup.find_all("img"):
        src = img.get("src")
        if src and ("/images/teams" in src or "/images/" in src):
            images_to_download.add(src)
            
    print(f"Found {len(images_to_download)} unique images to download.")
    
    with concurrent.futures.ThreadPoolExecutor(max_workers=10) as executor:
        executor.map(download_image, images_to_download)
        
    print("Done downloading images.")

if __name__ == "__main__":
    main()
