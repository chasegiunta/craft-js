import type { EntryQuery, Craft as _Craft } from "./interfaces";
/**
 * Factory function to start the query chain.
 * @param apiUrl - The base URL of the API.
 * @returns A proxy object that wraps an instance of the `Craft` class.
 */
function init(apiUrl: string): _Craft {
  return craftProxy(apiUrl);
}
/**
 * Creates a proxy object for the Craft class.
 * The proxy object allows dynamic method invocation and property access,
 * and it automatically creates new instances of the Craft class with updated filters based on the method or property accessed.
 * @param apiUrl The base URL of the API.
 * @returns A proxy object that allows dynamic method invocation and property access on the Craft class.
 */
function craftProxy(apiUrl: string) {
  const baseQuery = new Craft(apiUrl) as _Craft & Craft;
  return new Proxy(baseQuery, {
    get(target, prop) {
      if (typeof prop === "string" && prop in target) {
        return target[prop];
      } else {
        return function (...args) {
          const newQuery = target.clone();
          newQuery.filters[prop.toString()] = args.length > 0 ? args[0] : true;
          return newQuery;
        };
      }
    },
  });
}

/**
 * The `Craft` class is a query builder that allows users to construct and execute API queries.
 * It provides methods for filtering and fetching data from the API.
 */
class Craft {
  /**
   * The base URL of the API.
   */
  protected apiUrl: string;

  /**
   * The specific endpoint that the query will hit.
   */
  protected endpoint: string = `/actions/craft-js/craft`;

  /**
   * The specific endpoint that the batch request will hit.
   */
  protected batchEndpoint: string = `/actions/craft-js/craft/batched`;

  /**
   * An array of `Craft` queries to be executed as a batch.
   */
  private queries: Craft[] = [];

  // Filters that will be applied to this query.
  public filters: Record<string, any> = {};

  /**
   * Indicates whether the query is part of a batch.
   */
  protected isBatchMode = false;

  /**
   * Initializes the `Craft` instance with the API URL.
   * @param apiUrl The base URL of the API.
   */
  constructor(apiUrl: string) {
    this.apiUrl = apiUrl;
  }

  /**
   * Index signature to allow additional methods.
   */
  [method: string]: any;

  /**
   * Sets the `prune` filter to remove specific fields from the query.
   * @param items The fields to be pruned.
   * @returns The `Craft` instance.
   */
  prune(items: string[]): Craft {
    this.filters.prune = JSON.stringify(items);
    return this;
  }

  /**
   * Creates a new instance of the `Craft` class with the same filters as the original query.
   * @returns The cloned `Craft` instance.
   */
  clone(): Craft {
    const newQuery = craftProxy(this.apiUrl);
    newQuery.filters = { ...this.filters };
    return newQuery;
  }

  one() {
    this.filters.executeMethod = "one";
    return this.fetch();
  }
  collect() {
    this.filters.executeMethod = "collect";
    return this.fetch();
  }
  exists() {
    this.filters.executeMethod = "exists";
    return this.fetch();
  }
  count() {
    this.filters.executeMethod = "count";
    return this.fetch();
  }
  ids() {
    this.filters.executeMethod = "ids";
    return this.fetch();
  }
  column() {
    this.filters.executeMethod = "column";
    return this.fetch();
  }
  all() {
    this.filters.executeMethod = "all";
    return this.fetch();
  }

  /**
   * Creates a new instance of the `CraftBatch` class to execute multiple queries as a batch.
   * @param queries The queries to be executed as a batch.
   * @returns The `CraftBatch` instance.
   */
  batch(queries: Craft[]): Craft {
    this.isBatchMode = true;
    queries.forEach((query) => this.addQuery(query));
    return this;
  }

  /**
   * Adds a `Craft` query to the batch.
   * @param query The `Craft` query to add.
   * @returns The `CraftBatch` instance.
   */
  addQuery(query: Craft): Craft {
    this.queries.push(query);
    return this;
  }

  /**
   * Fetches data from the API.
   * @returns A promise that resolves to the fetched data or array of fetched data.
   */
  async fetch(): Promise<any | any[]> {
    let url: URL | null = null;
    const headers = new Headers();
    headers.append("Accept", "application/json");

    const options: RequestInit = {
      method: this.isBatchMode ? "POST" : "GET",
      headers: headers,
    };

    if (this.isBatchMode) {
      // Batch mode
      headers.append("Content-Type", "application/json");

      // Aggregate filters from each query into an array
      const batchedFilters = this.queries.map((query) => query.filters);
      options.body = JSON.stringify(batchedFilters);
      url = new URL(`${this.apiUrl}${this.batchEndpoint}`);
    } else {
      // Single query mode
      const filters = {};
      for (const [key, value] of Object.entries(this.filters)) {
        filters[key] = value != null ? value.toString() : null;
      }

      const queryString = new URLSearchParams(filters).toString();
      url = new URL(`${this.apiUrl}${this.endpoint}?${queryString}`);
    }

    try {
      const response = await fetch(url, options);
      if (!response.ok) {
        throw new Error(`Network response was not ok: ${response.statusText}`);
      }
      const data = await response.json();

      if (!this.isBatchMode) {
        this.filters = {};
      }

      return data;
    } catch (error) {
      console.error(`Error fetching data: ${error}`);
      throw error;
    }
  }
}

export default init;
// export type { EntryQuery, _Craft };
