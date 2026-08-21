import {
  test,
  expect,
  subsiteUrl,
  storageStatePath,
} from '../../fixtures/msls-fixtures';

test.describe('MSLS admin — post translation metabox', () => {
  for (const slug of ['', 'de', 'it'] as const) {
    test.describe(`on "${slug || 'root'}" subsite`, () => {
      test.use({
        storageState: storageStatePath(slug),
        baseURL: subsiteUrl(slug),
      });

      test('translation picker metabox appears on the seed post', async ({
        page,
        seed,
      }) => {
        // Use the absolute subsite URL — Playwright's baseURL path prefix is
        // dropped when goto() receives an absolute path (leading "/").
        const editUrl = `${subsiteUrl(slug)}/wp-admin/post.php?post=${seed.posts[slug].id}&action=edit`;
        await page.goto(editUrl);

        // The Gutenberg "Meta Boxes" pane is collapsed by default — but the
        // markup is in the DOM regardless. Assert on presence of the MSLS
        // metabox and its language fields without depending on visibility.
        const metabox = page.locator('#msls');
        await expect(metabox).toHaveCount(1, { timeout: 15_000 });

        const foreignLangs =
          slug === ''
            ? ['de_DE', 'it_IT']
            : slug === 'de'
              ? ['en_US', 'it_IT']
              : ['en_US', 'de_DE'];

        for (const lang of foreignLangs) {
          const field = metabox.locator(`[name="msls_input_${lang}"]`);
          await expect(
            field,
            `field for ${lang} on "${slug || 'root'}"`
          ).toHaveCount(1);
        }
      });
    });
  }
});
