#!/usr/bin/env python3
"""HEIC ファイルを JPEG サムネイルに変換し、EXIF メタデータを JSON で出力する。

Usage:
  heic_to_thumb.py <input> <output> <size>

  size は最長辺のピクセル数。縦横比は保持される。

Stdout:
  JSON {"taken_at": "YYYY:MM:DD HH:MM:SS" or null,
        "latitude": float or null,
        "longitude": float or null}
"""
import sys
import json

try:
    from pillow_heif import register_heif_opener
    from PIL import Image, ExifTags
except ImportError as e:
    sys.stderr.write(f"missing dependency: {e}\n")
    sys.exit(3)

register_heif_opener()


def parse_gps_coord(values, ref):
    """EXIF GPS座標 (度,分,秒) を十進度に変換する。"""
    if not values or not ref:
        return None
    try:
        d = float(values[0])
        m = float(values[1])
        s = float(values[2])
    except (TypeError, ValueError, IndexError):
        return None
    decimal = d + m / 60 + s / 3600
    if ref in ('S', 'W'):
        decimal = -decimal
    return decimal


def extract_metadata(img):
    meta = {"taken_at": None, "latitude": None, "longitude": None}
    exif = img._getexif() if hasattr(img, '_getexif') else None
    if not exif:
        return meta

    for tag_id, value in exif.items():
        name = ExifTags.TAGS.get(tag_id, tag_id)
        if name == 'DateTimeOriginal':
            meta['taken_at'] = value
        elif name == 'GPSInfo' and isinstance(value, dict):
            gps = {}
            for gps_tag_id, gps_value in value.items():
                gps_name = ExifTags.GPSTAGS.get(gps_tag_id, gps_tag_id)
                gps[gps_name] = gps_value
            meta['latitude'] = parse_gps_coord(gps.get('GPSLatitude'), gps.get('GPSLatitudeRef'))
            meta['longitude'] = parse_gps_coord(gps.get('GPSLongitude'), gps.get('GPSLongitudeRef'))
    return meta


if len(sys.argv) != 4:
    sys.stderr.write("usage: heic_to_thumb.py <input> <output> <size>\n")
    sys.exit(1)

src, dst, size_str = sys.argv[1], sys.argv[2], sys.argv[3]
try:
    size = int(size_str)
except ValueError:
    sys.stderr.write(f"invalid size: {size_str}\n")
    sys.exit(1)

try:
    img = Image.open(src)
    meta = extract_metadata(img)
    img.thumbnail((size, size))
    img.convert("RGB").save(dst, "JPEG", quality=85)
except Exception as e:
    sys.stderr.write(f"convert failed: {e}\n")
    sys.exit(2)

print(json.dumps(meta))
