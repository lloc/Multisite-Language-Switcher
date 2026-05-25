import { request } from '@playwright/test';
import {
  test as base,
  expect,
  RequestUtils,
} from '@wordpress/e2e-test-utils-playwright';
import * as path from 'node:path';

export type SubsiteSlug = '' | 'de' | 'it';

const BASE_URL = process.env.WP_BASE_URL ?? 'http://localhost:8889';
const STORAGE_STATE_DIR =
  process.env.STORAGE_STATE_DIR ??
  path.join(__dirname, '..', 'artifacts', 'storage-states');

function subsiteUrl(slug: SubsiteSlug): string {
  return slug === '' ? BASE_URL : `${BASE_URL}/${slug}`;
}

function storageStatePath(slug: SubsiteSlug): string {
  const name = slug === '' ? 'admin' : `admin-${slug}`;
  return path.join(STORAGE_STATE_DIR, `${name}.json`);
}

type MslsFixtures = {
  requestUtilsForBlog: (slug: SubsiteSlug) => Promise<RequestUtils>;
};

export const test = base.extend<MslsFixtures>({
  requestUtilsForBlog: async ({}, use) => {
    const instances = new Map<SubsiteSlug, RequestUtils>();

    const factory = async (slug: SubsiteSlug): Promise<RequestUtils> => {
      const cached = instances.get(slug);
      if (cached) {
        return cached;
      }

      const ctx = await request.newContext({ baseURL: subsiteUrl(slug) });
      const utils = new RequestUtils(ctx, {
        baseURL: subsiteUrl(slug),
        storageStatePath: storageStatePath(slug),
      });
      await utils.setupRest();

      instances.set(slug, utils);
      return utils;
    };

    await use(factory);

    for (const utils of instances.values()) {
      await utils.request.dispose();
    }
  },
});

export { expect };
