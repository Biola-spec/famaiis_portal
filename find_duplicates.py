import re
from collections import Counter

with open('routes/web.php', 'r') as f:
    content = f.read()

names = re.findall(r"->name\(['\"]([^'\"]+)['\"]\)", content)
duplicates = [name for name, count in Counter(names).items() if count > 1]

if duplicates:
    print("Duplicate route names found:")
    for d in duplicates:
        print(f"- {d}")
else:
    print("No duplicate route names found.")
