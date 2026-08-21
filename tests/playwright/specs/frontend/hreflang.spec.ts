import { test, expect } from '../../fixtures/msls-fixtures';

test.describe('MSLS frontend — hreflang alternates', () => {
  for (const slug of ['', 'de', 'it'] as const) {
    test(`home for "${slug || 'root'}" emits hreflang alternates`, async ({
      page,
    }) => {
      const url = slug === '' ? '/' : `/${slug}/`;
      const response = await page.goto(url);

      expect(
        response?.ok(),
        `home for "${slug || 'root'}" should respond 2xx`
      ).toBe(true);

      const html = (await response?.text()) ?? '';
      expect(html, 'rel=alternate hreflang link tag').toMatch(
        /<link[^>]+rel=["']alternate["'][^>]+hreflang=/i
      );
    });

    test(`single post for "${slug || 'root'}" emits hreflang for the other two languages`, async ({
      page,
      seed,
    }) => {
      const response = await page.goto(seed.posts[slug].link);
      expect(response?.ok()).toBe(true);

      const html = (await response?.text()) ?? '';

      // Expect at least the two foreign languages plus optionally x-default.
      const expected =
        slug === '' ? ['de', 'it'] : slug === 'de' ? ['en', 'it'] : ['en', 'de'];

      for (const lang of expected) {
        expect(
          html,
          `hreflang for "${lang}" on "${slug || 'root'}"`
        ).toMatch(new RegExp(`hreflang=["']${lang}[A-Z_-]*["']`, 'i'));
      }
    });
  }
});
