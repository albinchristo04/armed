import os
import json
import concurrent.futures
import requests
from urllib.parse import urljoin

BASE_URL = "https://www.footybite.army"

def download_image(url_path):
    if not url_path:
        return
    # Ensure it doesn't already have the base URL
    if not url_path.startswith("http"):
        full_url = urljoin(BASE_URL, url_path)
    else:
        full_url = url_path
        
    # We want to mirror the relative path structure, 
    # e.g., '/images/teams/Logo.png' -> './images/teams/Logo.png'
    # Strip leading slash to make it a relative path
    local_path = url_path.lstrip("/")
    if not local_path:
        return
        
    os.makedirs(os.path.dirname(local_path), exist_ok=True)
    
    if os.path.exists(local_path):
        return # Skip if already downloaded
        
    try:
        r = requests.get(full_url, timeout=10)
        r.raise_for_status()
        with open(local_path, "wb") as f:
            f.write(r.content)
        print(f"Downloaded -> {local_path}")
    except Exception as e:
        print(f"Failed {full_url}: {e}")

def main():
    if not os.path.exists("matches.json"):
        print("matches.json not found. Run scraper.py first.")
        return
        
    with open("matches.json", "r", encoding="utf-8") as f:
        data = json.load(f)
        
    images_to_download = set()
    for match in data.get("matches", []):
        if match.get("home_logo"):
            images_to_download.add(match["home_logo"])
        if match.get("away_logo"):
            images_to_download.add(match["away_logo"])
        if match.get("league_logo"):
            images_to_download.add(match["league_logo"])
            
    print(f"Found {len(images_to_download)} unique images to download.")
    
    with concurrent.futures.ThreadPoolExecutor(max_workers=10) as executor:
        executor.map(download_image, images_to_download)
        
    print("Done downloading images.")

if __name__ == "__main__":
    main()
