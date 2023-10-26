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

  clone(): CraftQuery {
    const newQuery = createCraftQueryProxy(this.apiUrl);
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

    // URLSearchParams constructor will throw an error if any values are not strings
    // Loop over this.filters and ensure that all values are strings
    const filters = {};
    for (const [key, value] of Object.entries(this.filters)) {
      filters[key] = value.toString();
    }

    const queryString = new URLSearchParams(filters).toString();
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
  endpoint: string = `/actions/craft-js/craft/batched`;

  constructor(apiUrl: string) {
    this.apiUrl = apiUrl;
  }

  addQuery(query: CraftQuery): CraftBatch {
    this.queries.push(query);
    return this;
  }

  async fetch(): Promise<any[]> {
    const headers = new Headers();
    headers.append("Accept", "application/json");
    headers.append("Content-Type", "application/json");

    // Aggregate filters from each query into an array
    const batchedFilters = this.queries.map((query) => query.getFilters());

    const options = {
      method: "POST", // We'll POST the batch of queries
      headers: headers,
      body: JSON.stringify(batchedFilters), // Send all filters in one go
    };

    const response = await fetch(`${this.apiUrl}${this.endpoint}`, options);

    if (!response.ok) {
      throw new Error(`Network response was not ok: ${response.statusText}`);
    }

    const data = await response.json();

    // Assuming the server will respond with an array of results matching the order of queries
    return data;
  }
}

function createCraftQueryProxy(apiUrl: string): CraftQuery {
  const craftQuery = new CraftQuery(apiUrl);

  return new Proxy(craftQuery, {
    get(target, prop) {
      if (typeof prop === "string" && prop in target) {
        return target[prop];
      } else {
        return function (...args) {
          const newQuery = target.clone(); // Create a new instance
          if (args[0] !== null) {
            // Check if argument is not null
            newQuery.filters[prop] = args.join(","); // Modify the new instance
          } else {
            // If argument is null, delete the corresponding property
            delete newQuery.filters[prop];
          }
          return newQuery; // Return the new instance
        };
      }
    },
  });
}

function createCraftProxy(apiUrl = ""): CraftQuery {
  return createCraftQueryProxy(apiUrl);
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
