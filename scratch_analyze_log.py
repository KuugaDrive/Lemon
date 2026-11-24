import re

with open('remote_laravel.log', 'r', encoding='utf-8', errors='ignore') as f:
    text = f.read()

user_ids = set(re.findall(r'"userId":(\d+)', text))
print('User IDs in logs:', user_ids)

sqls = set(re.findall(r'SQL: ([^\n\r]+)', text))
print(f'Total unique SQL error statements: {len(sqls)}')
for s in sorted(sqls):
    print('  *', s[:140])
