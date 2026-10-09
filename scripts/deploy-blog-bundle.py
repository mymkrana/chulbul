"""Publish a reviewed bundle through an authenticated, temporary FTP runner."""
import ftplib
import hashlib
import io
import json
import os
from pathlib import Path
import re
import secrets
import time
import urllib.request


def main():
    bundle = os.environ['BLOG_BUNDLE']
    if not re.fullmatch(r'blogs_[a-z0-9_]+\.json', bundle):
        raise ValueError('Invalid bundle filename')
    bundle_bytes = (Path(__file__).parent / bundle).read_bytes()
    posts = json.loads(bundle_bytes)
    if len(posts) != 4:
        raise ValueError('Expected four articles')
    token = secrets.token_hex(32)
    filename = 'cbd-publish-' + secrets.token_hex(16) + '.php'
    # Never place the bearer secret in a URL, repository, or log.
    print('::add-mask::' + token, flush=True)
    source = '''<?php
ini_set('display_errors', '0');
header('Content-Type: application/json');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'
    || time() > EXPIRY
    || !hash_equals('TOKEN_HASH', hash('sha256', (string)($_SERVER['HTTP_X_CBD_PUBLISH_TOKEN'] ?? '')))) {
    http_response_code(404); exit;
}
try {
    if (!hash_equals('BUNDLE_HASH', hash_file('sha256', __DIR__ . '/scripts/BUNDLE'))) {
        throw new RuntimeException('Bundle verification failed.');
    }
    define('CBD_DEPLOY_BLOG_AUTHORIZED', true);
    require __DIR__ . '/scripts/publish-blog-bundle.php';
    echo json_encode(cbd_publish_blog_bundle('BUNDLE'), JSON_THROW_ON_ERROR);
} catch (Throwable $error) {
    error_log('Editorial publish failed: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Editorial publish failed; review server logs.']);
}
'''.replace('EXPIRY', str(int(time.time()) + 600)).replace('TOKEN_HASH', hashlib.sha256(token.encode()).hexdigest()).replace('BUNDLE_HASH', hashlib.sha256(bundle_bytes).hexdigest()).replace('BUNDLE', bundle)
    with ftplib.FTP(timeout=60) as ftp:
        ftp.connect(os.environ['FTP_SERVER'], 21)
        ftp.login(os.environ['FTP_USERNAME'], os.environ['FTP_PASSWORD'])
        ftp.cwd('./')
        try:
            ftp.storbinary('STOR ' + filename, io.BytesIO(source.encode()))
            request = urllib.request.Request('https://www.chulbuldesign.com/' + filename, data=b'', headers={'X-CBD-Publish-Token': token}, method='POST')
            # HTTPS certificate verification stays enabled; redirects are not needed.
            class NoRedirect(urllib.request.HTTPRedirectHandler):
                def redirect_request(self, req, fp, code, msg, headers, newurl):
                    return None
            opener = urllib.request.build_opener(NoRedirect())
            with opener.open(request, timeout=90) as response:
                result = json.load(response)
            expected = [post['slug'] for post in posts]
            if not result.get('success') or result.get('slugs') != expected or result.get('inserted', 0) + result.get('unchanged', 0) != 4:
                raise RuntimeError('Publication response did not confirm four articles')
            print(json.dumps(result), flush=True)
        finally:
            ftp.delete(filename)
            print('Temporary publication runner removed.', flush=True)
    # Confirm each live article, its canonical URL and sitemap inclusion.
    sitemap = opener.open('https://www.chulbuldesign.com/sitemap.xml', timeout=60).read().decode()
    for post in posts:
        url = 'https://www.chulbuldesign.com/blog/' + post['slug']
        request = urllib.request.Request(url, headers={'User-Agent': 'ChulbulDeployMonitor/1.0'})
        with opener.open(request, timeout=60) as response:
            markup = response.read().decode()
        if post['title'] not in markup or url not in markup or url not in sitemap:
            raise RuntimeError('Live article verification failed: ' + post['slug'])
        print('Verified live article and sitemap: ' + url, flush=True)


if __name__ == '__main__':
    main()
