class CraftQuery {
  apiUrl: string;
  endpoint: string;
  filters: {};
  constructor(apiUrl: string) {
    this.apiUrl = apiUrl;
    this.endpoint = `/actions/craft-js/craft`;
    this.filters = {};
  }

  entries() {
    this.filters.elementType = "entries";
    return this;
  }

  section(sectionName: string) {
    this.filters.section = sectionName;
    return this;
  }

  limit(count: number) {
    this.filters.limit = count;
    return this;
  }

  authorId(id: number) {
    this.filters.authorId = id;
    return this;
  }

  // quotle(id: number) {
  //   this.filters.push(`quotle=${id}`);
  //   return this;
  // }

  orderBy(field: string, direction: string) {
    this.filters.orderBy = "${field} ${direction}";
    // this.filters.push(`orderBy=${field}${direction === "desc" ? "|desc" : ""}`);
    return this;
  }

  select(columms: string[]) {
    this.filters.select = columms.join(",");
    return this;
  }

  async fetch() {
    const headers = new Headers();
    headers.append("Accept", "application/json");

    const options = {
      method: "GET",
      headers: headers,
      // mode: "cors",
      // cache: "default",
    };
    // const queryString = this.filters.join("&");
    const queryString = new URLSearchParams(this.filters).toString();
    const response = await fetch(
      `${this.apiUrl}${this.endpoint}?${queryString}`,
      options
    );
    const data = await response.json();
    return data;
  }

  async post() {
    const response = await fetch(`${this.apiUrl}${this.endpoint}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(this.filters),
    });
    const data = await response.json();
    return data;
  }
}

function createCraftProxy(apiUrl = "") {
  const craftQuery = new CraftQuery(apiUrl);

  return new Proxy(craftQuery, {
    get(target, prop) {
      if (prop in target) {
        return target[prop];
      } else {
        return function (...args) {
          target.filters[prop] = args.join(",");
          return craftQuery;
        };
      }
    },
  });
}

const Craft = createCraftProxy;

export default Craft;
