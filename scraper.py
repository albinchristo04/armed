#!/usr/bin/env python3
"""
Footybite Match Scraper
Extracts match details and iframe stream URLs from footybite.army
"""

import json
import re
import time
import logging
import concurrent.futures
from datetime import datetime, timezone
from pathlib import Path

import requests
from bs4 import BeautifulSoup

# ── Config ────────────────────────────────────────────────────────────────────
BASE_URL = "https://www.footybite.army"
OUTPUT_FILE = Path("matches.json")
REQUEST_TIMEOUT = 15
DELAY_BETWEEN_REQUESTS = 1.5   # seconds – be polite to the server

HEADERS = {
    "User-Agent": (
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) "
        "Chrome/123.0.0.0 Safari/537.36"
    ),
    "Accept-Language": "en-US,en;q=0.9",
    "Referer": BASE_URL,
}

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s  %(levelname)-8s  %(message)s",
    datefmt="%H:%M:%S",
)
log = logging.getLogger(__name__)


# ── Helpers ───────────────────────────────────────────────────────────────────

def get(url: str) -> tuple[BeautifulSoup, str] | tuple[None, None]:
    """Fetch a page and return (BeautifulSoup, raw_html), or (None, None) on failure."""
    try:
        r = requests.get(url, headers=HEADERS, timeout=REQUEST_TIMEOUT)
        r.raise_for_status()
        return BeautifulSoup(r.text, "html.parser"), r.text
    except requests.RequestException as exc:
        log.warning("Failed to fetch %s — %s", url, exc)
        return None, None


def extract_iframes(soup: BeautifulSoup, raw_html: str = "") -> list[str]:
    """
    Pull iframe stream URLs from a match page using multiple strategies:
      1. Standard <iframe src="..."> tags
      2. <iframe data-src="..."> (lazy-loaded iframes)
      3. Regex scan of raw HTML for any iframe src patterns
      4. Regex scan of <script> blocks for embedded stream URLs
    Returns up to the top 10 unique URLs found.
    """
    seen: set[str] = set()
    urls: list[str] = []

    def add(u: str) -> None:
        u = u.strip().strip("'\"")
        if u.startswith("//"):
            u = "https:" + u
        if u and u.startswith("http") and u not in seen:
            seen.add(u)
            urls.append(u)

    # Strategy 1 & 2 – BeautifulSoup iframe tags
    for tag in soup.find_all("iframe"):
        for attr in ("src", "data-src", "data-lazy-src"):
            add(tag.get(attr, ""))

    # Strategy 3 – regex over the full raw HTML for any iframe src
    if raw_html:
        for m in re.finditer(
            r'<iframe[^>]+\bsrc\s*=\s*["\']([^"\']+)["\']', raw_html, re.IGNORECASE
        ):
            add(m.group(1))

    # Strategy 4 – mine <script> blocks for http URLs that look like stream embeds
    # Typical patterns: livesport embeds, streameast, tarjetarojatvhd, etc.
    stream_pattern = re.compile(
        r'["\']'                         # opening quote
        r'(https?://[^\s"\'<>]{6,})'     # URL – at least 6 chars
        r'["\']',                        # closing quote
        re.IGNORECASE,
    )
    for script in soup.find_all("script"):
        block = script.string or ""
        for m in stream_pattern.finditer(block):
            candidate = m.group(1)
            # Filter to likely stream embed domains / paths
            if any(kw in candidate for kw in (
                "embed", "stream", "live", "player",
                "iframe", "watch", "tv", "sport",
            )):
                add(candidate)

    # Strategy 5 – Footybite specific: Hidden inputs with id starting with 'linkk'
    raw_urls = []
    for input_tag in soup.find_all("input", type="hidden", id=re.compile(r"^linkk")):
        val = input_tag.get("value", "")
        if val:
            raw_urls.append(val)

    if raw_urls:
        def resolve_iframe(u: str) -> str:
            u = u.strip().strip("'\"")
            if u.startswith("//"):
                u = "https:" + u
            if not u.startswith("http"):
                return u
            try:
                # Use a short timeout so one bad link doesn't hang the scraper
                r = requests.get(u, headers=HEADERS, timeout=5)
                s = BeautifulSoup(r.text, "html.parser")
                for iframe in s.find_all("iframe"):
                    src = iframe.get("src")
                    if src and src.startswith("http"):
                        return src
            except Exception:
                pass
            return u

        with concurrent.futures.ThreadPoolExecutor(max_workers=5) as executor:
            for resolved_url in executor.map(resolve_iframe, raw_urls):
                if resolved_url:
                    add(resolved_url)

    return urls[:10]  # top 10 only


def extract_match_time(text: str) -> str:
    """Try to find a kick-off time string inside raw text."""
    # Common patterns: "20:45", "8:45 PM", "Live", "HT", "FT", "45'"
    patterns = [
        r"\b\d{1,2}:\d{2}\s*(?:AM|PM|am|pm)?\b",
        r"\bLive\b",
        r"\bHT\b",
        r"\bFT\b",
        r"\b\d{1,3}['']\b",
    ]
    for p in patterns:
        m = re.search(p, text, re.IGNORECASE)
        if m:
            return m.group(0).strip()
    return ""


# ── Core scraping ─────────────────────────────────────────────────────────────

def scrape_matches() -> list[dict]:
    """
    1. Load the homepage and collect every match card link.
    2. For each match page scrape details + iframes.
    """
    log.info("Fetching homepage: %s", BASE_URL)
    soup, _ = get(BASE_URL)
    if not soup:
        log.error("Cannot reach homepage – aborting.")
        return []

    matches = []
    seen_urls = set()

    # ── Step 1: find match card links on the homepage ─────────────────────────
    # The site wraps each match in an <a target="_blank" href="..."> tag
    # containing a div.div-child-box with team names in span.txt-team
    # and status in the .time-txt column.
    #
    # Match URLs follow the pattern: https://www.footybite.army/<Team1>-vs-<Team2>/<id>
    match_links = soup.select('a[target="_blank"][href*="footybite.army/"]')
    log.info("  Found %d candidate <a> tags with footybite.army hrefs", len(match_links))

    card_links: list[tuple[str, dict]] = []

    for anchor in match_links:
        href = anchor.get("href", "")
        if not href or href == BASE_URL or href.rstrip("/") == BASE_URL.rstrip("/"):
            continue
        # Skip non-match links (news, register, etc.)
        if "/news/" in href or "streamsportal.com" in href or "discord.gg" in href:
            continue
        full_url = href if href.startswith("http") else BASE_URL.rstrip("/") + "/" + href.lstrip("/")
        if full_url in seen_urls:
            continue
        seen_urls.add(full_url)

        # Extract team names from span.txt-team elements inside the card
        team_spans = anchor.select("span.txt-team")
        home_team = team_spans[0].get_text(strip=True) if len(team_spans) > 0 else ""
        away_team = team_spans[1].get_text(strip=True) if len(team_spans) > 1 else ""

        # Extract logos (assume standard structure where cols are text-right and text-left respectively)
        home_logo = ""
        away_logo = ""
        col_right = anchor.select_one(".col-5.text-right")
        if col_right:
            img_home = col_right.select_one("img")
            if img_home: home_logo = img_home.get("src", "")

        col_left = anchor.select_one(".col-5.text-left")
        if col_left:
            img_away = col_left.select_one("img")
            if img_away: away_logo = img_away.get("src", "")

        # Extract status text from the .time-txt column
        status_el = anchor.select_one(".time-txt")
        status = status_el.get_text(" ", strip=True) if status_el else ""

        # Fallback: extract team names from the URL path
        # URL pattern: /Team1-vs-Team2/12345
        if not home_team or not away_team:
            url_match = re.search(r'/([^/]+)-vs-([^/]+)/\d+', href)
            if url_match:
                if not home_team:
                    home_team = url_match.group(1).replace("-", " ")
                if not away_team:
                    away_team = url_match.group(2).replace("-", " ")

        # Try to find league from the nearest preceding league header
        league = ""
        league_logo = ""
        # Look for a preceding .my-1 sibling with a league icon/name
        prev = anchor.find_previous("div", class_="my-1")
        if prev:
            league_span = prev.select_one("span")
            # In Important Games it's inside an h4 that blinks. So we can just take the text of the span.
            if league_span:
                league = league_span.get_text(strip=True)
            
            league_img = prev.select_one("img")
            if league_img:
                league_logo = league_img.get("src", "")

        card_links.append((full_url, {
            "home_team": home_team,
            "away_team": away_team,
            "home_logo": home_logo,
            "away_logo": away_logo,
            "score": "",
            "league": league,
            "league_logo": league_logo,
            "status": status,
            "match_url": full_url,
        }))

    log.info("Found %d unique match links", len(card_links))

    # ── Step 2: visit each match page and grab iframes ────────────────────────
    for idx, (match_url, info) in enumerate(card_links, 1):
        log.info("  [%d/%d] %s", idx, len(card_links), match_url)
        match_soup, raw_html = get(match_url)
        time.sleep(DELAY_BETWEEN_REQUESTS)

        iframes: list[str] = []
        kick_off = ""

        if match_soup:
            iframes = extract_iframes(match_soup, raw_html or "")

            # Try to extract kick-off time from status
            kick_off = extract_match_time(info.get("status", ""))

            # If we didn't get teams from the card, try the match page <title>
            if not info["home_team"] and not info["away_team"]:
                title_tag = match_soup.find("title")
                if title_tag:
                    title_text = title_tag.get_text(strip=True)
                    teams = re.split(r"\s+(?:vs\.?|-|–|v)\s+", title_text, maxsplit=1, flags=re.IGNORECASE)
                    if len(teams) == 2:
                        info["home_team"] = teams[0].strip()
                        info["away_team"] = teams[1].strip()

        matches.append({
            **info,
            "kick_off": kick_off,
            "iframe_urls": iframes,
            "scraped_at": datetime.now(timezone.utc).isoformat(),
        })

    return matches


# ── Output ────────────────────────────────────────────────────────────────────

def save(matches: list[dict]) -> None:
    payload = {
        "last_updated": datetime.now(timezone.utc).isoformat(),
        "total_matches": len(matches),
        "matches": matches,
    }
    OUTPUT_FILE.write_text(json.dumps(payload, indent=2, ensure_ascii=False))
    log.info("Saved %d matches → %s", len(matches), OUTPUT_FILE)


# ── Entry point ───────────────────────────────────────────────────────────────

if __name__ == "__main__":
    data = scrape_matches()
    save(data)
    print(json.dumps({"status": "ok", "matches_found": len(data)}, indent=2))
