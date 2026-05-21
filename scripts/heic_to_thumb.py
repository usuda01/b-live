#!/usr/bin/env python3
"""HEIC ファイルを JPEG サムネイルに変換する。

Usage:
  heic_to_thumb.py <input> <output> <size>

  size は最長辺のピクセル数。縦横比は保持される。
"""
import sys

try:
    from pillow_heif import register_heif_opener
    from PIL import Image
except ImportError as e:
    sys.stderr.write(f"missing dependency: {e}\n")
    sys.exit(3)

register_heif_opener()

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
    img.thumbnail((size, size))
    img.convert("RGB").save(dst, "JPEG", quality=85)
except Exception as e:
    sys.stderr.write(f"convert failed: {e}\n")
    sys.exit(2)
