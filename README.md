# Craft JS

🧪 Experimental concept for a Craft CMS javascript / typescript client that:

- Attempts to mimic Twig's familiar chainable API
- Aims to offer autocomplete through Typescript
- Makes headless projects more approachable for those who either don't know GraphQL, or don't care for it

> [!NOTE]
> If you find this concept interesting, please improve on it where you can and submit a PR. This is very much a learning project for me. I'm not a PHP developer by profession, nor do I work with Craft CMS in my day job, though I use it on a volunteer project. Any suggestions for code cleanup, improvements, future ideas, etc are welcome!

> [!CAUTION]
> Not suitable for production. Probably poses some security risks.

#### Vue app usage demo

https://github.com/chasegiunta/craft-js/assets/1377169/e2e8d096-94d0-45e2-8a80-6c6a26263440

#### Basic Usage

```js
import Craft from "{vendorPath}/chasegiunta/craft-js/dist/craft";

const craft = Craft("https://your-craft-website.test");

// Currently, only read queries are supported
const fetchPosts = async () => {
  const posts = craft
    .entries()
    .section("blog")
    .orderBy("title ASC")
    .limit(10)
    .all();
  await craft.then((response: any) => {
    console.log(response.data);
  });
};
```

##### Element Pagination

```js
const posts = craft
  .entries()
  .section("blog")
  .label("myCustomFieldLabel") // supports custom fields
  .paginate(pageNum) // paginate() will override any execution methods (.all(), .one(), etc.)
  .limit(10)
  .fetch(); // method used to fetch response (this will be changed in the future).
```

##### Batch Queries

```js
const query1 = craft
  .entries()
  .section("teams")
  .division("d1")
  .limit(10)
  .prune(["title"]);
const query2 = craft
  .entries()
  .section("news")
  .limit(20)
  .prune(["title", "url"]);

await craft
  .batch([query1, query2])
  .fetch()
  .then((responses: any[]) => {
    const teams = responses[0];
    const news = responses[1];
    // ... handle data ...

    console.log(teams.data, news.data);
  });
```

---

## Future Idea & Todos:

<details>
  <summary>Craft Element CRUD Functionality</summary>

- [ ] Create Elements
  - [ ] Entries
  - [ ] Users
  - [ ] Assets
  - [ ] Categories
  - [ ] Tags
  - [ ] Globals
  - [ ] Matrix Blocks
  - [ ] Addresses
- [ ] Read Elements
  - [x] Entries
  - [x] Users
  - [x] Assets
  - [x] Categories (untested)
  - [x] Tags (untested)
  - [x] Globals
  - [x] Matrix Blocks
  - [x] Addresses
- [ ] Update Elements
  - [ ] Entries
  - [ ] Users
  - [ ] Assets
  - [ ] Categories
  - [ ] Tags
  - [ ] Globals
  - [ ] Matrix Blocks
  - [ ] Addresses
- [ ] Delete Elements
  - [ ] Entries
  - [ ] Users
  - [ ] Assets
  - [ ] Categories
  - [ ] Tags
  - [ ] Globals
  - [ ] Matrix Blocks
  - [ ] Addresses

</details>

- [ ] Flesh out `prune()` API
  - [ ] Nested related elements
  - [ ] Conditional data (`{% if block.type === 'heading' %}`)
- [ ] Generate type interfaces
  - [ ] Craft element classes
  - [ ] Extended Craft behavior classes (eg `craft.superTable`)
- [ ] Implement user / guest permissions system
- [ ] User authentication
- [ ] Commerce CRUD elements
- [ ] Routes fetching
- [ ] Image transforms
- [ ] Proper Tests
  - [ ] Frontend
  - [ ] Backend
- [ ] Audit for securtiy
- [ ] Audit for code necessity

## Requirements

This plugin requires Craft CMS 4.3.10 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Craft JS”. Then press “Install”.

#### With Composer

##### Add the following to your composer.json file:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/chasegiunta/craft-js.git"
    }
  ],
  "require": {
    "chasegiunta/craft-js": "dev-main"
  }
}
```

```bash
# install
composer install

# tell Craft to install the plugin
./craft plugin/install craft-js
```
