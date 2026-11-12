import csv
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import urlsplit

ROOT = Path(__file__).resolve().parents[1]

class SitePage(HTMLParser):
    def __init__(self):
        super().__init__()
        self.ids = set()
        self.links = []
        self.headings = 0
        self.tags = set()
        self.form_method = None
        self.form_action = None
        self.disabled_fieldset = False

    def handle_starttag(self, tag, attrs):
        attributes = dict(attrs)
        self.tags.add(tag)
        if "id" in attributes:
            assert attributes["id"] not in self.ids, attributes["id"]
            self.ids.add(attributes["id"])
        if tag == "h1":
            self.headings += 1
        if tag == "form":
            self.form_method = attributes.get("method")
            self.form_action = attributes.get("action")
        if tag == "fieldset" and "disabled" in attributes:
            self.disabled_fieldset = True
        if "href" in attributes:
            self.links.append(attributes["href"])
        if "src" in attributes:
            self.links.append(attributes["src"])

home_path = ROOT / "index.php"
source = home_path.read_text(encoding="utf-8-sig")
page = SitePage()
page.feed(source)
assert page.headings == 1
assert {"main", "nav", "footer"} <= page.tags
assert {"guide", "scam-checklist", "tracker", "about", "contact"} <= page.ids
assert not {"script", "iframe"} & page.tags
assert page.form_method == "post" and page.form_action == "contact.php"
assert not page.disabled_fieldset
assert "How to Find Part-Time Remote Jobs" in source
assert "We publish educational guides" in source
assert "https://consumer.ftc.gov/articles/job-scams" in page.links

pages = {home_path: page}
for relative_path in ("privacy-policy/index.html", "thank-you/index.html"):
    path = ROOT / relative_path
    document = SitePage()
    document.feed(path.read_text(encoding="utf-8-sig"))
    pages[path] = document

for path, document in pages.items():
    for link in document.links:
        url = urlsplit(link)
        if url.scheme:
            assert url.scheme == "https" or (url.scheme == "mailto" and url.path.endswith("@nimblyscout.com")), link
            continue
        target = (path.parent / url.path).resolve() if url.path else path
        assert target.is_relative_to(ROOT) and target.is_file(), (path, link)
        if url.fragment:
            assert target in pages and url.fragment in pages[target].ids, (path, link)

with (ROOT / "remote-job-search-tracker.csv").open(encoding="utf-8-sig", newline="") as tracker:
    rows = list(csv.reader(tracker))
assert rows[0] == ["Employer", "Role", "Posting URL", "Hours and time zone", "Date applied", "Status", "Next step"]
assert len(rows) > 1 and all(len(row) == len(rows[0]) for row in rows)
print("PASS: destination content, links and assets across all three pages, and offline tracker")
