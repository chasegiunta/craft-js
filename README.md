# Craft JS

🧪 Experimental concept for a Craft CMS javascript / typescript client that:

- Attempts to mimic Twig's familiar chainable API
- Aims to offer autocomplete through Typescript
- Makes headless projects more approachable for those who either don't know GraphQL, or don't care for it

> [!NOTE]
> If you find this concept interesting, please improve on it where you can and submit a PR. This is very much a learning project for me. I'm not a PHP developer by profession, nor do I work with Craft CMS in my day job, though I use it on a volunteer project. Any suggestions for code cleanup, improvements, future ideas, etc are welcome!

> [!CAUTION]
> Not suitable for production (yet)

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

#### Prune Responses

```js
const posts = craft
  .entries()
  .section("blog")
  // Basic usage: simply pass an array of fields
  .prune(["title", "author", "body", "url", "featuredImage"])
  .all();
```

```js
const posts = craft
  .entries()
  .section("blog")
  // Advanced object syntax
  .prune({
    title: true,
    id: true,
    uri: true,
    // Related fields simple array syntax
    author: ["username", "email"],
    // Related fields object syntax
    mainImage: {
      url: true,
      uploader: {
        // Nested related fields
        email: true,
        username: true,
      },
    },
    // Matrix fields
    contentBlocks: {
      // Denote query traits with $ prefix
      // https://www.yiiframework.com/doc/api/2.0/yii-db-querytrait
      $limit: 10,
      // Designate distinct prune fields per type with _ prefix
      _body: {
        body: true,
        intro: true,
      },
      _fullWidthImage: {
        image: ["url", "alt"],
      },
    },
  })
  .all();
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

- [x] Flesh out `prune()` API
  - [x] Nested related elements
  - [x] Conditional prune by type (`{% if block.type === 'heading' %}`)
  - [ ] Call functions on fields before response
  - [ ] Image transforms
- [ ] Generate type interfaces
  - [ ] Craft element classes
  - [ ] Extended Craft behavior classes (eg `craft.superTable`)
- [ ] Implement user / guest permissions system
- [ ] User authentication
- [ ] Commerce CRUD elements
- [ ] Routes fetching
- [ ] Proper Tests
  - [ ] Frontend
  - [ ] Backend

#### Vue app usage demo (outdated prune API)

https://github.com/chasegiunta/craft-js/assets/1377169/e2e8d096-94d0-45e2-8a80-6c6a26263440

## Requirements

This plugin requires Craft CMS 5.0.0 or later, and PHP 8.0.2 or later.

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
