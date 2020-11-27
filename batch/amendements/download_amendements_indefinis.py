import requests

count = 0

for url_amdt in open('liste_sort_indefini.txt'):
    url_amdt = url_amdt.strip()
    print(url_amdt)
    resp = requests.get(url_amdt, cookies={'website_version': 'old'})
    if resp.status_code != 200:
        print('invalid response')
        continue
    slug = url_amdt.replace('/', '_-_')
    with open(f'html/{slug}.asp', 'w') as f:
        f.write(resp.text)
        count += 1

print(count, 'amendements indefinis téléchargés')