module.exports = {
  title: 'Multisite Language Switcher',
  description: 'Simple, powerful and easy to use',
  serviceWorker: true,
  head: [
    [ 'link', { rel: 'icon', href: '/favicon.png' } ]
  ],
  ga: 'UA-1058133-32',
  themeConfig: {
    logo: '/logo.png',
    lastUpdated: 'Last Updated',
    repo: 'lloc/multisite-language-switcher',
    repoLabel: 'Github Repository',
    docsRepo: 'lloc/multisite-language-switcher',
    docsDir: 'docs',
    docsBranch: 'master',
    editLinks: true,
    editLinkText: 'Help me improve this page!',
    nav: [
      {
        text: 'Plugin Directory',
        link: 'https://wordpress.org/plugins/multisite-language-switcher/',
      },
      {
        text: 'API documentation',
        link: '/api-documentation/',
      }
    ],
    sidebar: [
      ['/', 'Home'],
      {
        title: 'User documentation',
        children: [
          '/user-docs/',
          '/user-docs/install-multisite',
          '/user-docs/plugin-configuration',
          '/user-docs/editing-association-contents',
          '/user-docs/integration-website'
        ],
      },
      {
        title: 'Developer documentation',
        children: [
          '/developer-docs/snippets-examples',
          '/developer-docs/hook-reference'
        ],
      },
      {
        title: 'Appendices',
        children: [
          '/appendices/faq',
          '/appendices/help-donations',
          '/appendices/acknowledgment',
          '/appendices/license',
        ],
      },
    ]
  }
}
