class CraftQuery {
  apiUrl: string;
  endpoint: string;
  filters: {
    elementType?: string;
    section?: string;
    limit?: number;
    authorId?: number;
    orderBy?: string;
    asArray?: boolean;
    select?: string;
    with?: string;
    paginate?: number;
  };

  [key: string]: any; // Index signature

  constructor(apiUrl: string) {
    this.apiUrl = apiUrl;
    this.endpoint = `/actions/craft-js/craft`;
    this.filters = {};
  }

  isBatchMode = false;

  private clone(): CraftQuery {
    const newQuery = new CraftQuery(this.apiUrl);
    newQuery.filters = { ...this.filters };
    return newQuery;
  }

  // A method to get filters without clearing them
  getFilters() {
    return this.filters;
  }

  entries() {
    const newQuery = this.clone();
    newQuery.filters.elementType = "entries";
    return newQuery;
  }

  section(sectionName: string) {
    const newQuery = this.clone();
    newQuery.filters.section = sectionName;
    return newQuery;
  }

  limit(count: number) {
    const newQuery = this.clone();
    newQuery.filters.limit = count;
    return newQuery;
  }

  authorId(id: number) {
    const newQuery = this.clone();
    newQuery.filters.authorId = id;
    return newQuery;
  }

  orderBy(field: string, direction: string) {
    // this.filters.push(`orderBy=${field}${direction === "desc" ? "|desc" : ""}`);
    const newQuery = this.clone();
    newQuery.filters.orderBy = `${field} ${direction}`;
    return newQuery;
  }

  asArray() {
    const newQuery = this.clone();
    newQuery.filters.asArray = true;
    return newQuery;
  }

  select(columms: string[]) {
    const newQuery = this.clone();
    newQuery.filters.select = columms.join(",");
    return newQuery;
  }

  with(columns: string[]) {
    const newQuery = this.clone();
    newQuery.filters.with = columns.join(",");
    return newQuery;
  }

  paginate(page: number) {
    const newQuery = this.clone();
    newQuery.filters.paginate = page;
    return newQuery;
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
    if (!this.isBatchMode) {
      this.filters = {};
    }
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

class CraftBatch {
  apiUrl: string;
  queries: CraftQuery[] = [];

  constructor(apiUrl: string) {
    this.apiUrl = apiUrl;
  }

  addQuery(query: CraftQuery): CraftBatch {
    this.queries.push(query);
    return this;
  }

  async fetch(): Promise<any[]> {
    this.queries.forEach((q) => (q.isBatchMode = true)); // Set batch mode for all queries
    const results = await Promise.all(this.queries.map((q) => q.fetch()));
    this.queries.forEach((q) => (q.isBatchMode = false)); // Reset batch mode for all queries
    return results;
  }
}

function createCraftProxy(apiUrl = "") {
  const craftQuery = new CraftQuery(apiUrl);

  const craftProxy = new Proxy(craftQuery, {
    get(target, prop) {
      if (prop in target) {
        return target[prop];
      } else {
        return function (...args) {
          const newQuery = target.clone(); // Create a new instance
          newQuery.filters[prop] = args.join(","); // Modify the new instance
          return newQuery; // Return the new instance
        };
      }
    },
  });

  return craftProxy;
}

const Craft = (apiUrl = "") => {
  const craftProxy = createCraftProxy(apiUrl);
  craftProxy.batch = (queries: CraftQuery[]) => {
    const batch = new CraftBatch(apiUrl);
    queries.forEach((query) => batch.addQuery(query));
    return batch;
  };
  return craftProxy;
};

export default Craft;
