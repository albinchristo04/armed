import base64
import re
import json
import urllib.parse

def decode_custom_base64(encoded):
    alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/='
    std_base64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/='
    trans = str.maketrans(alphabet, std_base64)
    x_std = encoded.translate(trans)
    x_std += '=' * (-len(x_std) % 4)
    decoded_bytes = base64.b64decode(x_std)
    # The javascript does: decodeURIComponent('%' + hex(char))
    # which is basically decoding from URL-encoded hex.
    # In python we can just return it as a string assuming it's utf-8 or ascii, 
    # but the JS does loop and toString(16) -> we can just format it and unquote.
    encoded_str = "".join(['%' + hex(b)[2:].zfill(2) for b in decoded_bytes])
    try:
        return urllib.parse.unquote(encoded_str)
    except:
        return decoded_bytes.decode('utf-8', errors='ignore')

with open('F1 Australian Grand Prix vs Live Live Streams - Footybite.html', 'r', encoding='utf-8') as f:
    content = f.read()

m = re.search(r"window\['ZpQw9XkLmN8c3vR3'\]='([^']+)'", content)
if m:
    encoded = m.group(1)
    print("Found encoded string ZpQw9XkLmN8c3vR3.")
    decoded = decode_custom_base64(encoded)
    with open('decoded_window_custom.txt', 'w', encoding='utf-8') as out:
        out.write(decoded)
    print("Decoded string written to decoded_window_custom.txt")

# also let's just decode the array _0x5bc07d
arr_m = re.search(r"const _0x5bc07d=\[(.*?)\];", content)
if arr_m:
    arr_str = arr_m.group(1)
    items = re.findall(r"'([^']+)'", arr_str)
    decoded_items = [decode_custom_base64(item) for item in items]
    with open('decoded_array.json', 'w', encoding='utf-8') as f:
        json.dump(decoded_items, f, indent=2)
    print("Decoded array written to decoded_array.json")
