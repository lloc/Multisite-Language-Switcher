import type { Page } from '@playwright/test';

import { test, expect, subsiteUrl } from '../../fixtures/msls-fixtures';

/**
 * Reads the rel=alternate head links MSLS prints, keyed by their hreflang.
 *
 * They run through the same Blog::get_url() as the visible switcher, so they are
 * the theme-independent way to assert the link a blog is pointed at.
 */
function alternates(html: string): Record<string, string> {
  const links: Record<string, string> = {};

  for (const tag of html.match(
    /<link[^>]+rel=["']alternate["'][^>]*>/gi
  ) ?? []) {
    const href = /href=["']([^"']+)["']/i.exec(tag)?.[1];
    const lang = /hreflang=["']([^"']+)["']/i.exec(tag)?.[1];

    if (href && lang) {
      links[lang] = href;
    }
  }

  return links;
}

async function alternatesOf(
  page: Page,
  url: string
): Promise<Record<string, string>> {
  const response = await page.goto(url);
  expect(response?.ok(), `${url} responds 2xx`).toBe(true);

  return alternates((await response?.text()) ?? '');
}

test.describe('MSLS frontend — pagination', () => {
  test('page 2 of a split post points at page 2 of the translation', async ({
    page,
    seed,
  }) => {
    const links = await alternatesOf(page, `${seed.paged[''].link}2/`);

    expect(links.en).toBe(`${subsiteUrl('')}/${seed.paged[''].slug}/2/`);
    expect(links.de).toBe(`${subsiteUrl('de')}/${seed.paged.de.slug}/2/`);
  });

  test('page 2 falls back to the first page of a shorter translation', async ({
    page,
    seed,
  }) => {
    const links = await alternatesOf(page, `${seed.paged[''].link}2/`);

    // The Italian post has no <!--nextpage-->, so it has no second page.
    expect(links.it).toBe(`${subsiteUrl('it')}/${seed.paged.it.slug}/`);
  });

  test('the switcher in the content carries the page as well', async ({
    page,
    seed,
  }) => {
    await page.goto(`${seed.paged[''].link}2/`);

    const anchor = page.locator(
      `a[href="${subsiteUrl('de')}/${seed.paged.de.slug}/2/"]`
    );

    await expect(anchor.first()).toBeVisible();
  });

  test('the first page of a split post is left alone', async ({
    page,
    seed,
  }) => {
    const links = await alternatesOf(page, seed.paged[''].link);

    expect(links.de).toBe(`${subsiteUrl('de')}/${seed.paged.de.slug}/`);
    expect(links.it).toBe(`${subsiteUrl('it')}/${seed.paged.it.slug}/`);
  });

  test('page 2 of the blog index points at page 2 of the other blogs', async ({
    page,
  }) => {
    const links = await alternatesOf(page, '/page/2/');

    expect(links.en).toBe(`${subsiteUrl('')}/page/2/`);
    expect(links.de).toBe(`${subsiteUrl('de')}/page/2/`);
    expect(links.it).toBe(`${subsiteUrl('it')}/page/2/`);
  });

  test('the first page of the blog index is left alone', async ({ page }) => {
    const links = await alternatesOf(page, '/');

    expect(links.de).toBe(`${subsiteUrl('de')}/`);
    expect(links.it).toBe(`${subsiteUrl('it')}/`);
  });
});
